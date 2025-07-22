import feedparser
import requests
import os
from datetime import datetime, timedelta
from urllib.parse import urlparse, urljoin
import time
from concurrent.futures import ThreadPoolExecutor, as_completed
import json
import csv
from pathlib import Path
import hashlib
import math

class PodcastHarvester:
    def __init__(self, download_dir="podcast_downloads", max_workers=5, batch_size=50):
        self.download_dir = Path(download_dir)
        self.download_dir.mkdir(exist_ok=True)
        self.max_workers = max_workers
        self.batch_size = batch_size
        self.session = requests.Session()
        self.session.headers.update({
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        })
        
    def is_recent_episode(self, published_date, days_back=7):
        """Check if episode was published within the last specified days"""
        if not published_date:
            return False
            
        try:
            # Parse the published date
            episode_date = datetime(*published_date[:6])
            cutoff_date = datetime.now() - timedelta(days=days_back)
            return episode_date >= cutoff_date
        except (TypeError, ValueError):
            return False
    
    def extract_audio_url(self, entry):
        """Extract MP3 or audio URL from RSS entry"""
        audio_url = None
        
        # Check enclosures first (most common)
        if hasattr(entry, 'enclosures') and entry.enclosures:
            for enclosure in entry.enclosures:
                if 'audio' in enclosure.get('type', '').lower():
                    audio_url = enclosure.get('url') or enclosure.get('href')
                    break
        
        # Check links
        if not audio_url and hasattr(entry, 'links'):
            for link in entry.links:
                if link.get('type', '').startswith('audio'):
                    audio_url = link.get('href')
                    break
        
        # Check media content
        if not audio_url and hasattr(entry, 'media_content'):
            for media in entry.media_content:
                if 'audio' in media.get('type', '').lower():
                    audio_url = media.get('url')
                    break
                    
        return audio_url
    
    def sanitise_filename(self, filename):
        """Clean filename for safe saving"""
        invalid_chars = '<>:"/\\|?*'
        for char in invalid_chars:
            filename = filename.replace(char, '_')
        return filename[:200]  # Limit length
    
    def generate_unique_id(self, podcast_title, episode_title, published_date):
        """Generate a unique ID for each episode"""
        content = f"{podcast_title}_{episode_title}_{published_date}"
        return hashlib.md5(content.encode('utf-8')).hexdigest()[:12]
    
    def create_batch_directory(self, batch_number):
        """Create a directory for a specific batch"""
        batch_dir = self.download_dir / f"batch_{batch_number:03d}"
        batch_dir.mkdir(exist_ok=True)
        return batch_dir
    
    def extract_basic_metadata(self, entry, feed, rss_url):
        """Extract only the essential metadata needed for CSV"""
        return {
            # Essential data only
            'podcast_title': feed.feed.get('title', 'Unknown Podcast'),
            'episode_title': entry.get('title', 'Untitled'),
            'episode_url': entry.get('link', ''),
            'media_url': self.extract_audio_url(entry),
            
            # Internal processing data (not in CSV)
            'episode_id': self.generate_unique_id(
                feed.feed.get('title', 'Unknown'),
                entry.get('title', 'Untitled'),
                entry.get('published', '')
            ),
            'published_date': entry.get('published', ''),
            'download_status': 'pending',
            'local_filename': '',
            'local_filepath': '',
            'batch_number': 0  # Will be set during batching
        }
    
    def download_audio_file(self, url, filepath):
        """Download audio file to specific path"""
        try:
            response = self.session.get(url, stream=True, timeout=30)
            response.raise_for_status()
            
            with open(filepath, 'wb') as f:
                for chunk in response.iter_content(chunk_size=8192):
                    if chunk:
                        f.write(chunk)
            
            return str(filepath)
        except Exception as e:
            print(f"Error downloading {filepath.name}: {str(e)}")
            return None
    
    def process_podcast_feed(self, rss_url, podcast_name=None):
        """Process a single podcast RSS feed"""
        episodes_data = []
        
        try:
            print(f"Processing: {rss_url}")
            feed = feedparser.parse(rss_url)
            
            if not hasattr(feed, 'entries'):
                print(f"No entries found in feed: {rss_url}")
                return episodes_data
            
            for entry in feed.entries:
                # Check if episode is recent
                if not self.is_recent_episode(entry.get('published_parsed')):
                    continue
                
                # Extract audio URL
                audio_url = self.extract_audio_url(entry)
                if not audio_url:
                    continue
                
                # Extract basic metadata
                episode_data = self.extract_basic_metadata(entry, feed, rss_url)
                episodes_data.append(episode_data)
                
        except Exception as e:
            print(f"Error processing feed {rss_url}: {str(e)}")
            
        return episodes_data
    
    def create_batches(self, episodes_data):
        """Split episodes into batches of specified size"""
        batches = []
        total_episodes = len(episodes_data)
        num_batches = math.ceil(total_episodes / self.batch_size)
        
        for i in range(num_batches):
            start_idx = i * self.batch_size
            end_idx = min(start_idx + self.batch_size, total_episodes)
            batch = episodes_data[start_idx:end_idx]
            
            # Assign batch number to each episode
            for episode in batch:
                episode['batch_number'] = i + 1
            
            batches.append({
                'batch_number': i + 1,
                'episodes': batch,
                'total_episodes': len(batch)
            })
        
        return batches
    
    def download_batch(self, batch_info):
        """Download all episodes in a batch"""
        batch_number = batch_info['batch_number']
        episodes = batch_info['episodes']
        
        print(f"\n=== Processing Batch {batch_number} ({len(episodes)} episodes) ===")
        
        # Create batch directory
        batch_dir = self.create_batch_directory(batch_number)
        
        # Download episodes in this batch
        downloaded_episodes = []
        for episode in episodes:
            try:
                # Create safe filename
                safe_podcast = self.sanitise_filename(episode['podcast_title'])
                safe_episode = self.sanitise_filename(episode['episode_title'])
                episode_id = episode['episode_id']
                
                filename = f"{episode_id}_{safe_podcast}_{safe_episode}.mp3"
                filepath = batch_dir / filename
                
                # Download the file
                downloaded_path = self.download_audio_file(episode['media_url'], filepath)
                
                if downloaded_path:
                    episode['local_filepath'] = downloaded_path
                    episode['local_filename'] = filename
                    episode['download_status'] = 'success'
                    print(f"✓ Downloaded: {filename}")
                else:
                    episode['download_status'] = 'failed'
                    print(f"✗ Failed: {filename}")
                    
            except Exception as e:
                episode['download_status'] = f'error: {str(e)}'
                print(f"✗ Error downloading {episode['episode_title']}: {str(e)}")
            
            downloaded_episodes.append(episode)
            time.sleep(0.1)  # Small delay between downloads
        
        # Save batch CSV
        self.save_batch_csv(batch_dir, batch_number, downloaded_episodes)
        
        return downloaded_episodes
    
    def save_batch_csv(self, batch_dir, batch_number, episodes):
        """Save CSV file for a specific batch with only essential metadata"""
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        csv_filename = f"batch_{batch_number:03d}_episodes_{timestamp}.csv"
        csv_filepath = batch_dir / csv_filename
        
        # Define only the required fields
        required_fields = [
            'podcast_title',
            'episode_title', 
            'episode_url',
            'media_url'
        ]
        
        # Filter episodes to only those successfully downloaded
        successful_episodes = [ep for ep in episodes if ep.get('download_status') == 'success']
        
        if successful_episodes:
            with open(csv_filepath, 'w', newline='', encoding='utf-8') as f:
                writer = csv.DictWriter(f, fieldnames=required_fields)
                writer.writeheader()
                
                for episode in successful_episodes:
                    # Extract only the required fields
                    row_data = {field: episode.get(field, '') for field in required_fields}
                    writer.writerow(row_data)
            
            print(f"📄 Batch CSV saved: {csv_filename} ({len(successful_episodes)} episodes)")
        else:
            print(f"⚠️  No successful downloads in batch {batch_number}, CSV not created")
        
        # Also create a batch summary file
        summary_file = batch_dir / f"batch_{batch_number:03d}_summary.txt"
        with open(summary_file, 'w', encoding='utf-8') as f:
            f.write(f"BATCH {batch_number} SUMMARY\n")
            f.write(f"Generated: {datetime.now().isoformat()}\n")
            f.write(f"Total episodes processed: {len(episodes)}\n")
            f.write(f"Successful downloads: {len(successful_episodes)}\n")
            f.write(f"Failed downloads: {len(episodes) - len(successful_episodes)}\n")
            f.write(f"CSV file: {csv_filename}\n")
            f.write(f"MP3 files directory: {batch_dir}\n\n")
            
            if successful_episodes:
                f.write("SUCCESSFUL DOWNLOADS:\n")
                for episode in successful_episodes:
                    f.write(f"- {episode['podcast_title']}: {episode['episode_title']}\n")
    
    def harvest_podcasts(self, rss_urls, days_back=7):
        """Main method to harvest podcasts with batch processing"""
        all_episodes = []
        
        print(f"Starting harvest of {len(rss_urls)} podcast feeds...")
        print(f"Looking for episodes from the last {days_back} days")
        print(f"Will batch downloads into groups of {self.batch_size} episodes")
        
        # Process feeds to find recent episodes
        with ThreadPoolExecutor(max_workers=self.max_workers) as executor:
            feed_futures = {executor.submit(self.process_podcast_feed, url): url 
                          for url in rss_urls}
            
            for future in as_completed(feed_futures):
                episodes = future.result()
                all_episodes.extend(episodes)
                time.sleep(0.5)  # Be respectful to servers
        
        print(f"Found {len(all_episodes)} recent episodes")
        
        if not all_episodes:
            print("No recent episodes found!")
            return []
        
        # Create batches
        batches = self.create_batches(all_episodes)
        print(f"Created {len(batches)} batches for download")
        
        # Process each batch
        all_downloaded_episodes = []
        for batch_info in batches:
            downloaded_batch = self.download_batch(batch_info)
            all_downloaded_episodes.extend(downloaded_batch)
        
        # Create overall summary
        self.save_harvest_summary(all_downloaded_episodes, batches)
        
        successful_downloads = len([ep for ep in all_downloaded_episodes 
                                  if ep.get('download_status') == 'success'])
        
        print(f"\n=== HARVEST COMPLETE ===")
        print(f"Total episodes found: {len(all_downloaded_episodes)}")
        print(f"Successfully downloaded: {successful_downloads}")
        print(f"Organised into {len(batches)} batches")
        print(f"Files saved to: {self.download_dir}")
        
        return all_downloaded_episodes
    
    def save_harvest_summary(self, all_episodes, batches):
        """Save overall harvest summary"""
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        summary_file = self.download_dir / f"harvest_summary_{timestamp}.txt"
        
        successful_episodes = [ep for ep in all_episodes if ep.get('download_status') == 'success']
        
        with open(summary_file, 'w', encoding='utf-8') as f:
            f.write(f"PODCAST HARVEST SUMMARY\n")
            f.write(f"Generated: {datetime.now().isoformat()}\n")
            f.write(f"="*50 + "\n\n")
            
            f.write(f"OVERALL STATISTICS:\n")
            f.write(f"Total episodes found: {len(all_episodes)}\n")
            f.write(f"Successfully downloaded: {len(successful_episodes)}\n")
            f.write(f"Failed downloads: {len(all_episodes) - len(successful_episodes)}\n")
            f.write(f"Number of batches: {len(batches)}\n")
            f.write(f"Batch size: {self.batch_size}\n\n")
            
            f.write(f"BATCH BREAKDOWN:\n")
            for batch in batches:
                batch_successful = len([ep for ep in batch['episodes'] 
                                      if ep.get('download_status') == 'success'])
                f.write(f"Batch {batch['batch_number']:03d}: {batch_successful}/{batch['total_episodes']} successful\n")
            
            f.write(f"\nBATCH DIRECTORIES:\n")
            for batch in batches:
                f.write(f"batch_{batch['batch_number']:03d}/ - {batch['total_episodes']} episodes\n")
        
        print(f"📄 Overall summary saved: {summary_file.name}")

# Complete list of parenting podcast RSS URLs
PARENTING_PODCAST_URLS = [
    "https://feeds.simplecast.com/ZTNLWlKG",
    "https://feeds.megaphone.fm/slatesmomanddadarefighting",
    "https://feeds.megaphone.fm/GLT9881106244",
    "https://feeds.megaphone.fm/FAMM2866220157",
    "http://feeds.soundcloud.com/users/soundcloud:users:91056977/sounds.rss",
    "https://feeds.megaphone.fm/WFH1568989392",
    "http://rss.acast.com/happymumhappybaby",
    "https://feeds.megaphone.fm/TSF1512501751",
    "https://feeds.podcastmirror.com/zpr",
    "https://feeds.redcircle.com/4fa4a453-97f2-4c65-9611-3ca7c15e6911",
    "https://feeds.megaphone.fm/WFH1761316716",
    "https://feeds.redcircle.com/1e4f2d80-e26d-4632-8973-de98d5348c31",
    "https://feeds.simplecast.com/FJo1cu4f",
    "https://rss.art19.com/the-mom-hour",
    "https://feeds.megaphone.fm/WFH6335777472",
    "https://rss.art19.com/raising-good-humans",
    "https://rss.podplaystudio.com/2755.xml",
    "https://feeds.megaphone.fm/WFH7200177554",
    "http://feeds.feedburner.com/focus-on-the-family/focus-on-parenting",
    "https://feeds.megaphone.fm/poweryourparenting",
    "https://feeds.megaphone.fm/CELBL1853551588",
    "https://feeds.megaphone.fm/yogabirthbabies",
    "https://feeds.megaphone.fm/WFH3692120188",
    "https://feeds.acast.com/public/shows/5f9b18f378a9f70fee092ff4",
    "https://feeds.megaphone.fm/TNM9157362658",
    "https://feeds.megaphone.fm/wonderofparenting",
    "https://feeds.buzzsprout.com/1255388.rss",
    "https://www.pediascribe.com/podcast/feed.xml",
    "https://feeds.megaphone.fm/ADL9419958163",
    "https://feeds.redcircle.com/b496157d-bbb3-4c65-9611-3ca7c15e6911",
    "https://www.omnycontent.com/d/playlist/3ea926be-9be7-482a-8ac3-a44f016e43d1/5c116b8b-958c-413e-aabb-a4990019e0f7/8b30cd92-16a3-4a17-a36c-a4990059c7e5/podcast.rss",
    "https://feeds.captivate.fm/yourparentingmojo/",
    "https://feeds.libsyn.com/131808/rss",
    "https://feed.podbean.com/parentingwithgingerhubbard/feed.xml",
    "https://feeds.megaphone.fm/OLD2998733144",
    "https://www.spreaker.com/show/3680863/episodes/feed",
    "https://feeds.simplecast.com/Y5N0xWWZ",
    "https://authenticmoments.libsyn.com/rss",
    "https://feeds.simplecast.com/CrAcwZyv",
    "https://feeds.simplecast.com/98s4Kt5e",
    "https://rss.acast.com/notanothermummy",
    "https://feeds.captivate.fm/just-breathe-parenting/",
    "https://feeds.captivate.fm/mastermind-parenting/",
    "https://childlifeoncall.libsyn.com/rss",
    "https://feed.podbean.com/wholesomemumma/feed.xml",
    "https://feeds.megaphone.fm/strollercoaster",
    "https://feeds.buzzsprout.com/1516189.rss",
    "https://www.northeastohioparent.com/feed/podcast/",
    "https://feeds.simplecast.com/y6QJPB20",
    "https://feed.podbean.com/treehousestoryteller/feed.xml",
    "https://parentingforthepresent.libsyn.com/rss",
    "https://feeds.buzzsprout.com/927808.rss",
    "https://feeds.redcircle.com/614ade8b-196b-4e25-9eac-214df6ee2a03",
    "https://truthloveparent.podomatic.com/rss2.xml",
    "https://parentingwithoutpowerstruggles.libsyn.com/rss",
    "https://feeds.buzzsprout.com/1239269.rss",
    "https://feeds.blubrry.com/feeds/connected_parenting.xml",
    "https://feeds.redcircle.com/7b705795-7e51-4417-aefb-94769f0af239",
    "https://feeds.simplecast.com/Xwkk4p0k",
    "https://feed.podbean.com/theimpactfulparent/feed.xml",
    "https://unswaddled.libsyn.com/rss",
    "https://www.abc.net.au/feeds/11115204/podcast.xml",
    "https://audioboom.com/channels/4732330.rss",
    "https://feeds.simplecast.com/PpglTPtc",
    "https://feeds.buzzsprout.com/1217843.rss",
    "https://feeds.buzzsprout.com/886945.rss",
    "https://feeds.redcircle.com/7a8c36f9-d50a-453a-a9c0-72b8a66b6094",
    "https://www.gooddadspodcast.com/rss/",
    "https://feeds.libsyn.com/253152/rss",
    "https://feeds.buzzsprout.com/1421847.rss",
    "https://iono.fm/rss/c/3559",
    "https://thekidcounselor.libsyn.com/rss",
    "https://feeds.buzzsprout.com/1479301.rss",
    "https://feeds.redcircle.com/e3bf9db2-9772-47dc-ab41-05adb65b6549",
    "https://parentingmadepractical.libsyn.com/rss",
    "https://feeds.soundcloud.com/users/soundcloud:users:680844845/sounds.rss",
    "https://feeds.soundcloud.com/users/soundcloud:users:593550776/sounds.rss",
    "https://feeds.redcircle.com/6952a1a5-f707-45f7-aea5-9a397e73a0e0",
    "https://rss.acast.com/twodotingdads",
    "https://realworldpeacefulparenting.libsyn.com/rss",
    "https://feeds.npr.org/510334/podcast.xml"
]

def main():
    """Main execution function"""
    # Initialise the harvester with batch processing
    harvester = PodcastHarvester(
        download_dir="parent_podcast_harvest",
        max_workers=3,  # Be conservative to avoid overwhelming servers
        batch_size=50   # Maximum episodes per batch
    )
    
    # Use the complete list of podcast URLs
    rss_urls = PARENTING_PODCAST_URLS
    
    print(f"Ready to process {len(rss_urls)} parenting podcast feeds")
    print(f"Downloads will be organised into batches of {harvester.batch_size} episodes")
    print(f"Each batch will have its own folder and CSV file")
    
    # Harvest episodes from the last 7 days
    episodes = harvester.harvest_podcasts(rss_urls, days_back=7)
    
    # Print final summary
    successful_downloads = len([ep for ep in episodes 
                              if ep.get('download_status') == 'success'])
    
    if episodes:
        total_batches = len(set(ep.get('batch_number', 0) for ep in episodes))
        print(f"\n=== FINAL RESULTS ===")
        print(f"Total episodes processed: {len(episodes)}")
        print(f"Successfully downloaded: {successful_downloads}")
        print(f"Organised into {total_batches} batches")
        print(f"Files location: {harvester.download_dir}")
        
        print(f"\n=== BATCH STRUCTURE ===")
        for i in range(1, total_batches + 1):
            batch_episodes = [ep for ep in episodes if ep.get('batch_number') == i]
            batch_successful = len([ep for ep in batch_episodes if ep.get('download_status') == 'success'])
            print(f"  batch_{i:03d}/ - {batch_successful} MP3 files + CSV")
    
    return episodes

if __name__ == "__main__":
    episodes = main()

<?php
require_once 'config.php';
require_once 'classes/ArticleManager.php';

$articleManager = new ArticleManager();

// Get filter parameters
$filters = [
    'age_group' => $_GET['age_group'] ?? '',
    'category' => $_GET['category'] ?? '',
    'content_type' => $_GET['content_type'] ?? '',
    'urgency' => $_GET['urgency'] ?? '',
    'search' => $_GET['search'] ?? ''
];

$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 12;

// Get articles and filter options
$articles = $articleManager->getArticles($filters, $page, $perPage);
$ageGroups = $articleManager->getAgeGroups();
$categories = $articleManager->getCategories();

$contentTypes = [
    'heartwarming-story' => 'Heartwarming Story',
    'practical-tip' => 'Practical Tip',
    'expert-technique' => 'Expert Technique',
    'real-life-hack' => 'Real Life Hack',
    'research-insight' => 'Research Insight',
    'problem-solution' => 'Problem Solution',
    'funny-moment' => 'Funny Moment',
    'aha-moment' => 'Aha Moment'
];

$urgencyTypes = [
    'timeless' => 'Timeless',
    'trending' => 'Trending',
    'seasonal' => 'Seasonal',
    'urgent' => 'Urgent'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-3C3YK1HQGM"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-3C3YK1HQGM');
</script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> - <?= SITE_TAGLINE ?></title>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7749455827894286" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="assets/css/club-dadvice.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo"><?= SITE_NAME ?></div>
            <div class="tagline"><?= SITE_TAGLINE ?></div>
        </div>
    </header>
    
    <div class="container">
        <!-- Top Banner Ad -->
        <div class="top-banner-ad">
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="ca-pub-7749455827894286"
                 data-ad-slot="1234567890"
                 data-ad-format="auto"
                 data-full-width-responsive="true"></ins>
            <script>
                 (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>
        
        <div class="filters">
            <form method="GET" action="">
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="age_group">Age Group</label>
                        <select name="age_group" id="age_group">
                            <option value="">All Ages</option>
                            <?php foreach ($ageGroups as $ageGroup): ?>
                                <option value="<?= htmlspecialchars($ageGroup['name']) ?>" 
                                        <?= $filters['age_group'] === $ageGroup['name'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($ageGroup['display_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="category">Category</label>
                        <select name="category" id="category">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= htmlspecialchars($category['name']) ?>" 
                                        <?= $filters['category'] === $category['name'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['display_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="content_type">Content Type</label>
                        <select name="content_type" id="content_type">
                            <option value="">All Types</option>
                            <?php foreach ($contentTypes as $value => $label): ?>
                                <option value="<?= htmlspecialchars($value) ?>" 
                                        <?= $filters['content_type'] === $value ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="search">Search</label>
                        <input type="text" name="search" id="search" 
                               value="<?= htmlspecialchars($filters['search']) ?>" 
                               placeholder="Search articles...">
                    </div>
                </div>
                
                <div class="filter-buttons">
                    <button type="submit" class="btn btn-primary">Filter Articles</button>
                    <a href="index.php" class="btn btn-secondary">Clear Filters</a>
                </div>
            </form>
        </div>
        
        <?php if (empty($articles)): ?>
            <div class="no-articles">
                <h3>No articles found</h3>
                <p>Try adjusting your filters or search terms.</p>
            </div>
        <?php else: ?>
            <div class="articles-grid">
                <?php 
                $adCounter = 0;
                foreach ($articles as $index => $article): 
                    // Insert ad after every 3rd article
                    if ($index > 0 && $index % 3 == 0): ?>
                        <div class="article-card ad-card">
                            <ins class="adsbygoogle"
                                 style="display:block; width: 100%; height: 250px;"
                                 data-ad-client="ca-pub-7749455827894286"
                                 data-ad-slot="<?= 1234567891 + $adCounter ?>"
                                 data-ad-format="auto"></ins>
                            <script>
                                 (adsbygoogle = window.adsbygoogle || []).push({});
                            </script>
                        </div>
                    <?php 
                    $adCounter++;
                    endif; 
                ?>
                    <div class="article-card">
                        <div class="article-header">
                            <h2 class="article-title">
                                <a href="article.php?id=<?= urlencode($article['id']) ?>">
                                    <?= htmlspecialchars($article['title']) ?>
                                </a>
                            </h2>
                            <p class="article-summary"><?= htmlspecialchars($article['summary']) ?></p>
                            
                            <div class="article-meta">
                                <span class="meta-tag content-type"><?= htmlspecialchars($contentTypes[$article['content_type']] ?? $article['content_type']) ?></span>
                                <span class="meta-tag urgency"><?= htmlspecialchars($urgencyTypes[$article['urgency']] ?? $article['urgency']) ?></span>
                                <?php if ($article['age_groups']): ?>
                                    <span class="meta-tag"><?= htmlspecialchars($article['age_groups']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="article-content">
                            <div class="article-excerpt">
                                <?= truncateText(strip_tags($article['full_content']), 200) ?>
                            </div>
                            
                            <div class="article-scores">
                                <div class="score">
                                    <div class="score-value"><?= $article['engagement_score'] ?></div>
                                    <div class="score-label">Engagement</div>
                                </div>
                                <div class="score">
                                    <div class="score-value"><?= $article['practical_score'] ?></div>
                                    <div class="score-label">Practical</div>
                                </div>
                                <div class="score">
                                    <div class="score-value"><?= $article['universal_appeal'] ?></div>
                                    <div class="score-label">Universal</div>
                                </div>
                            </div>
                            
                            <a href="article.php?id=<?= urlencode($article['id']) ?>" class="btn btn-primary">Read More</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
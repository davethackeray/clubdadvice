# Design Document

## Overview

The Club Dadvice platform evolution transforms the current JSON-fed article site into a comprehensive, personalised community platform for fathers. Built on the proven PHP/MySQL foundation with Python for advanced features, the platform will scale from the current article-focused site to include community features, personalised newsletters, podcast integration, and mobile-first experiences. The architecture prioritises reliability, maintainability, and gradual feature rollout while preserving the existing content and user experience.

## Architecture

### Infrastructure Foundation

**Database Setup & Testing Validation System**
Reference: `.kiro/specs/database-setup-validation/`

This foundational system ensures reliable development and deployment workflows:

- **Configuration Manager**: Automatic environment detection and conflict-free configuration loading
- **Database Setup Manager**: Automated schema creation and data population
- **Testing Framework**: Comprehensive validation of all application components
- **Migration Engine**: Safe database schema evolution and version management
- **Health Monitor**: Continuous database performance and integrity monitoring

This infrastructure enables frictionless development across local and Hostinger environments while ensuring platform reliability for near-instantaneous user access.

### High-Level System Architecture

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │   Backend       │    │   Data Layer    │
│                 │    │                 │    │                 │
│ • Responsive    │◄──►│ • PHP Core      │◄──►│ • MySQL DB      │
│   Web App       │    │ • REST APIs     │    │ • File Storage  │
│ • Progressive   │    │ • Session Mgmt  │    │ • Cache Layer   │
│   Web App       │    │ • Auth System   │    │                 │
│ • Mobile First  │    │                 │    │                 │
└─────────────────┘    └─────────────────┘    └─────────────────┘
                                │
                       ┌─────────────────┐
                       │   Services      │
                       │                 │
                       │ • Python ML     │
                       │ • Email Service │
                       │ • Push Notifs   │
                       │ • Analytics     │
                       └─────────────────┘
```

### Technology Stack Evolution Strategy

**Phase 1: Simplest Possible Stack (Months 1-3)**
```
Current Foundation + Minimal Extensions
├── Frontend: Enhanced HTML/CSS/JS (no frameworks)
├── Backend: PHP 8.1+ (procedural → simple OOP)
├── Database: MySQL (your existing schema + simple extensions)
├── Sessions: PHP file sessions (built-in)
├── Email: PHP mail() → SMTP when needed
└── Hosting: Current Hostinger setup
```

**Why Start Simple:**
- **Immediate Delivery**: Build on what you know best
- **Zero Learning Curve**: No new technologies to master
- **Minimal Risk**: Proven, stable foundation
- **Fast Iteration**: Quick changes and deployments
- **Cost Effective**: Use existing hosting and tools

**Phase 2: Smart Scaling (Months 4-8)**
```
Selective Technology Upgrades
├── Frontend: Add Progressive Web App features
├── Backend: Structured PHP classes + simple APIs
├── Database: MySQL with proper indexing + basic caching
├── Email: Dedicated SMTP service (SendGrid/Mailgun)
├── Analytics: Simple PHP-based tracking
└── Caching: File-based caching for performance
```

**Phase 3: Advanced Features (Months 9-18)**
```
Introduce Python for Specific Needs
├── Core Platform: Still PHP (your strength)
├── Personalisation: Python microservice (when user base justifies it)
├── Newsletter: Python for advanced templating (when needed)
├── Analytics: Python for complex analysis (when data volume requires it)
├── Infrastructure: Redis caching, CDN integration
└── Mobile: PWA → Native app consideration
```

**Technology Decision Framework:**

**Start Simple When:**
- Feature can be built in PHP quickly
- User base < 10,000 active users
- Performance is adequate with current stack
- Development speed is more important than optimisation

**Scale Up When:**
- PHP solution becomes complex/unmaintainable
- Performance bottlenecks appear
- User base > 50,000 active users
- Feature requires specialised libraries (ML, advanced analytics)

**Practical Example - Personalisation Evolution:**

**Month 1-3: Simple PHP Personalisation**
```php
// Simple but effective - gets 80% of the value
function getPersonalisedContent($user_id) {
    $user_children = getUserChildren($user_id);
    $age_groups = calculateAgeGroups($user_children);
    
    // Simple SQL query with age group filtering
    return getArticlesByAgeGroups($age_groups, 20);
}
```

**Month 6-9: Enhanced PHP with Basic ML**
```php
// Still PHP, but smarter
function getPersonalisedContent($user_id) {
    $user_profile = getUserProfile($user_id);
    $interactions = getUserInteractions($user_id);
    
    // Simple scoring algorithm
    $scored_articles = scoreArticlesForUser($user_profile, $interactions);
    return array_slice($scored_articles, 0, 20);
}
```

**Month 12+: Python Microservice (Only When Justified)**
```python
# Only when PHP becomes limiting
class PersonalisationEngine:
    def get_recommendations(self, user_id: int) -> List[Dict]:
        # Advanced ML algorithms, collaborative filtering, etc.
        # But only when user base and complexity justify it
```

## Components and Interfaces

### 1. User Management System

**User Authentication & Profiles**
```php
class UserManager {
    public function register($email, $password, $profile_data);
    public function authenticate($email, $password);
    public function updateProfile($user_id, $profile_data);
    public function getPersonalisationData($user_id);
}
```

**Database Schema Extensions**
```sql
-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100),
    location VARCHAR(100),
    timezone VARCHAR(50) DEFAULT 'Europe/London',
    newsletter_frequency ENUM('daily', 'weekly', 'monthly') DEFAULT 'weekly',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE
);

-- User children for personalisation
CREATE TABLE user_children (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100),
    birth_date DATE,
    interests TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- User preferences
CREATE TABLE user_preferences (
    user_id INT PRIMARY KEY,
    preferred_content_types JSON,
    preferred_categories JSON,
    notification_settings JSON,
    privacy_settings JSON,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### 2. Content Management System

**Enhanced Article Management**
```php
class ArticleManager {
    public function getPersonalisedContent($user_id, $limit = 20);
    public function getContentByAgeGroup($age_groups, $limit = 20);
    public function searchContent($query, $filters = []);
    public function getRelatedContent($article_id, $user_id = null);
    public function trackEngagement($user_id, $article_id, $action);
}
```

**Content Personalisation Service (Python)**
```python
class PersonalisationEngine:
    def get_recommendations(self, user_id: int, limit: int = 20) -> List[Dict]:
        """Generate personalised content recommendations"""
        
    def calculate_content_scores(self, user_profile: Dict, articles: List[Dict]) -> List[Dict]:
        """Score articles based on user preferences and child ages"""
        
    def update_user_model(self, user_id: int, interactions: List[Dict]):
        """Update user preference model based on interactions"""
```

### 3. Community Platform

**Forum & Discussion System**
```php
class CommunityManager {
    public function createPost($user_id, $title, $content, $category, $age_groups);
    public function getPostsByCategory($category, $age_group = null);
    public function addReply($post_id, $user_id, $content);
    public function moderateContent($post_id, $action, $moderator_id);
    public function findLocalDads($user_id, $radius_km = 50);
}
```

**Database Schema for Community**
```sql
-- Community posts
CREATE TABLE community_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(500) NOT NULL,
    content TEXT NOT NULL,
    category_id INT,
    is_question BOOLEAN DEFAULT FALSE,
    is_solved BOOLEAN DEFAULT FALSE,
    upvotes INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Post replies
CREATE TABLE post_replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    is_expert_response BOOLEAN DEFAULT FALSE,
    upvotes INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES community_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- User interactions
CREATE TABLE user_interactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content_type ENUM('article', 'post', 'reply') NOT NULL,
    content_id INT NOT NULL,
    interaction_type ENUM('view', 'like', 'bookmark', 'share') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### 4. Newsletter System

**Newsletter Management (PHP)**
```php
class NewsletterManager {
    public function generatePersonalisedNewsletter($user_id);
    public function scheduleNewsletter($user_id, $send_time);
    public function getNewsletterTemplate($template_name);
    public function trackNewsletterEngagement($newsletter_id, $user_id, $action);
}
```

**Newsletter Generation Service (Python)**
```python
class NewsletterGenerator:
    def create_personalised_content(self, user_profile: Dict, content_pool: List[Dict]) -> Dict:
        """Generate personalised newsletter content"""
        
    def generate_html_template(self, content: Dict, user_preferences: Dict) -> str:
        """Create HTML newsletter from content and preferences"""
        
    def schedule_delivery(self, newsletters: List[Dict], send_time: datetime):
        """Schedule newsletter delivery via email service"""
```

### 5. Podcast Integration

**Media Management System**
```php
class MediaManager {
    public function addPodcastEpisode($title, $description, $audio_url, $transcript);
    public function getEpisodesByTopic($topic, $age_group = null);
    public function searchTranscripts($query);
    public function trackPlaybackProgress($user_id, $episode_id, $progress);
}
```

**Database Schema for Media**
```sql
-- Podcast episodes
CREATE TABLE podcast_episodes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(500) NOT NULL,
    description TEXT,
    audio_url VARCHAR(1000),
    duration_seconds INT,
    transcript LONGTEXT,
    published_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Episode topics (many-to-many with categories)
CREATE TABLE episode_categories (
    episode_id INT,
    category_id INT,
    PRIMARY KEY (episode_id, category_id),
    FOREIGN KEY (episode_id) REFERENCES podcast_episodes(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- User playback progress
CREATE TABLE playback_progress (
    user_id INT,
    episode_id INT,
    progress_seconds INT DEFAULT 0,
    completed BOOLEAN DEFAULT FALSE,
    last_played TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, episode_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (episode_id) REFERENCES podcast_episodes(id) ON DELETE CASCADE
);
```

### 6. Progressive Web App (PWA)

**Service Worker Implementation**
```javascript
// service-worker.js
class DadvicePWA {
    constructor() {
        this.cacheName = 'dadvice-v1';
        this.offlineContent = [
            '/',
            '/assets/css/club-dadvice.css',
            '/assets/js/app.js',
            '/offline.html'
        ];
    }
    
    async cacheEssentialContent() {
        // Cache critical resources for offline access
    }
    
    async handlePushNotification(event) {
        // Handle push notifications for new content
    }
    
    async syncOfflineActions() {
        // Sync user actions when back online
    }
}
```

**Push Notification System**
```php
class NotificationManager {
    public function sendPushNotification($user_id, $title, $message, $url);
    public function subscribeToNotifications($user_id, $subscription_data);
    public function getNotificationPreferences($user_id);
    public function scheduleContentNotifications($content_id, $target_users);
}
```

## Data Models

### Database Evolution Strategy

**Approach: Incremental Schema Evolution with Future-Proofing**

The strategy is to extend your existing solid foundation incrementally, adding new tables and columns that won't break current functionality but will support future features. This allows immediate delivery while building towards the full vision.

**Phase 1: Minimal Viable Extensions (Week 1)**
```sql
-- Add to existing schema - these won't break current functionality
ALTER TABLE articles ADD COLUMN view_count INT DEFAULT 0;
ALTER TABLE articles ADD COLUMN bookmark_count INT DEFAULT 0;
ALTER TABLE articles ADD COLUMN share_count INT DEFAULT 0;

-- New tables that can be added immediately without affecting current site
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE,
    password_hash VARCHAR(255),
    first_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    -- Future columns can be added later without breaking existing functionality
    INDEX idx_email (email),
    INDEX idx_created_at (created_at)
);

-- Simple user bookmarks - immediate value, no complexity
CREATE TABLE user_bookmarks (
    user_id INT,
    article_id VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, article_id),
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
);
```

**Phase 2: Personalisation Foundation (Week 2-3)**
```sql
-- Add user children for personalisation - simple and effective
CREATE TABLE user_children (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    birth_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Track basic interactions - foundation for recommendations
CREATE TABLE user_interactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    article_id VARCHAR(255),
    interaction_type ENUM('view', 'bookmark', 'share') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    INDEX idx_user_interactions (user_id, created_at),
    INDEX idx_article_interactions (article_id, interaction_type)
);
```

**Phase 3: Community Ready (Week 4-6)**
```sql
-- Community posts - can be added when ready for community features
CREATE TABLE community_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(500) NOT NULL,
    content TEXT NOT NULL,
    category_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (category_id) REFERENCES categories(id),
    INDEX idx_posts_category (category_id, created_at),
    INDEX idx_posts_user (user_id, created_at)
);
```

**Phase 4: Full Feature Set (Week 8+)**
```sql
-- Newsletter preferences
CREATE TABLE user_preferences (
    user_id INT PRIMARY KEY,
    newsletter_frequency ENUM('daily', 'weekly', 'monthly') DEFAULT 'weekly',
    preferred_content_types JSON,
    notification_settings JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Podcast episodes
CREATE TABLE podcast_episodes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(500) NOT NULL,
    description TEXT,
    audio_url VARCHAR(1000),
    duration_seconds INT,
    published_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_episodes_published (published_at)
);
```

### Core Data Relationships (Final State)

```
Users (1) ──── (M) UserChildren
  │
  ├── (1) ──── (1) UserPreferences  
  │
  ├── (1) ──── (M) UserInteractions
  │
  ├── (1) ──── (M) UserBookmarks
  │
  ├── (1) ──── (M) CommunityPosts
  │
  └── (1) ──── (M) PlaybackProgress

Articles (M) ──── (M) AgeGroups [EXISTING]
    │
    ├── (M) ──── (M) Categories [EXISTING]
    │
    ├── (M) ──── (M) Tags [EXISTING]
    │
    ├── (1) ──── (M) Takeaways [EXISTING]
    │
    └── (1) ──── (M) RelatedTopics [EXISTING]

PodcastEpisodes (M) ──── (M) Categories
```

### Migration Strategy Benefits

**Immediate Delivery Advantages:**
1. **No Breaking Changes**: Current site continues working perfectly
2. **Incremental Value**: Each phase adds immediate user value
3. **Low Risk**: Small, tested changes rather than big-bang migration
4. **Fast Feedback**: Users can start using features as they're built

**Future Scaling Advantages:**
1. **Proper Indexing**: Database optimised for performance from day one
2. **Flexible Schema**: JSON columns for evolving preferences
3. **Referential Integrity**: Foreign keys ensure data consistency
4. **Audit Trail**: Created/updated timestamps for analytics

**Development Efficiency:**
1. **Parallel Development**: Frontend and backend can evolve together
2. **Testing Friendly**: Each phase can be thoroughly tested
3. **Rollback Safe**: Easy to revert individual changes if needed
4. **Performance Monitoring**: Can optimise queries as data grows

### Personalisation Data Model

**User Profile Vector**
```python
UserProfile = {
    'user_id': int,
    'children_ages': List[int],  # Ages in months for precision
    'content_preferences': {
        'content_types': List[str],
        'categories': List[str],
        'reading_time': str,  # 'quick', 'medium', 'detailed'
        'media_preference': str  # 'text', 'audio', 'mixed'
    },
    'engagement_history': {
        'viewed_articles': List[int],
        'bookmarked_articles': List[int],
        'shared_articles': List[int],
        'time_spent': Dict[int, float]  # article_id: seconds
    },
    'community_activity': {
        'posts_created': int,
        'replies_made': int,
        'helpful_votes': int
    }
}
```

## Error Handling

### Graceful Degradation Strategy

**Progressive Enhancement Layers**
1. **Base Layer**: Static HTML content accessible without JavaScript
2. **Enhanced Layer**: Dynamic filtering and personalisation with JavaScript
3. **Advanced Layer**: PWA features, push notifications, offline access

**Error Handling Patterns**
```php
class ErrorHandler {
    public function handleDatabaseError($exception, $fallback_action = 'show_cached');
    public function handleAPIError($service, $error_code, $fallback_data = null);
    public function logUserError($user_id, $error_type, $context);
    public function showUserFriendlyMessage($error_type, $context = []);
}
```

**Offline Functionality**
```javascript
class OfflineManager {
    async handleOfflineRequest(request) {
        // Serve cached content when offline
        // Queue user actions for later sync
    }
    
    async syncWhenOnline() {
        // Sync bookmarks, comments, progress when connection restored
    }
}
```

## Testing Strategy

### Testing Pyramid

**Unit Tests (PHP & Python)**
```php
// PHP Unit Tests
class ArticleManagerTest extends PHPUnit\Framework\TestCase {
    public function testPersonalisedContentRetrieval();
    public function testContentFiltering();
    public function testEngagementTracking();
}
```

```python
# Python Unit Tests
class TestPersonalisationEngine(unittest.TestCase):
    def test_recommendation_generation(self):
        # Test recommendation algorithm
        
    def test_user_model_updates(self):
        # Test user preference learning
```

**Integration Tests**
- Database connection and query performance
- API endpoint functionality
- Email service integration
- Push notification delivery
- PWA service worker functionality

**End-to-End Tests**
- User registration and profile setup
- Content personalisation flow
- Community interaction workflow
- Newsletter subscription and delivery
- Mobile app functionality

**Performance Tests**
- Database query optimisation
- Page load speed testing
- Concurrent user handling
- Memory usage monitoring
- Cache effectiveness

### Testing Tools & Framework

**PHP Testing**
- PHPUnit for unit and integration tests
- Codeception for acceptance testing
- PHP_CodeSniffer for code quality

**Python Testing**
- pytest for unit tests
- requests-mock for API testing
- locust for load testing

**Frontend Testing**
- Jest for JavaScript unit tests
- Cypress for end-to-end testing
- Lighthouse for performance auditing

**Database Testing**
- MySQL performance schema analysis
- Query execution plan optimisation
- Data integrity validation

## Implementation Phases

### Phase 1: Foundation Enhancement (Weeks 1-4)
- User authentication and profile system
- Enhanced database schema
- Basic personalisation engine
- Mobile-responsive improvements

### Phase 2: Community Features (Weeks 5-8)
- Community forum implementation
- User interaction tracking
- Content moderation system
- Local dad matching

### Phase 3: Newsletter & Communication (Weeks 9-12)
- Personalised newsletter system
- Email service integration
- Push notification infrastructure
- PWA implementation

### Phase 4: Media Integration (Weeks 13-16)
- Podcast episode management
- Audio player integration
- Transcript search functionality
- Media recommendation engine

### Phase 5: Advanced Features (Weeks 17-20)
- Advanced personalisation algorithms
- Analytics dashboard
- Revenue optimisation features
- Performance optimisation

### Phase 6: Scale & Polish (Weeks 21-24)
- Load testing and optimisation
- Security hardening
- User experience refinements
- Launch preparation

## Security Considerations

**Authentication & Authorisation**
- Secure password hashing (bcrypt)
- Session management with CSRF protection
- Role-based access control for moderation
- Two-factor authentication option

**Data Protection**
- Input validation and sanitisation
- SQL injection prevention (prepared statements)
- XSS protection with Content Security Policy
- GDPR compliance for user data

**Infrastructure Security**
- HTTPS enforcement
- Database connection encryption
- File upload restrictions
- Rate limiting for API endpoints

**Privacy Protection**
- Anonymised analytics data
- User data deletion capabilities
- Consent management system
- Secure data transmission
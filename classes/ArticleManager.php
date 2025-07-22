<?php
require_once 'config.php';

class ArticleManager {
    private $pdo;
    
    public function __construct() {
        $this->pdo = getDBConnection();
    }
    
    /**
     * Import articles from JSON feed
     */
    public function importFromJSON($jsonData) {
        $data = json_decode($jsonData, true);
        
        if (!isset($data['newsletter_content'])) {
            throw new Exception('Invalid JSON structure: missing newsletter_content');
        }
        
        $imported = 0;
        $errors = [];
        
        foreach ($data['newsletter_content'] as $article) {
            try {
                $this->saveArticle($article);
                $imported++;
            } catch (Exception $e) {
                $errors[] = "Error importing article {$article['id']}: " . $e->getMessage();
            }
        }
        
        return [
            'imported' => $imported,
            'errors' => $errors
        ];
    }
    
    /**
     * Save a single article to database
     */
    private function saveArticle($articleData) {
        $this->pdo->beginTransaction();
        
        try {
            // Insert main article
            $stmt = $this->pdo->prepare("
                INSERT INTO articles (id, title, summary, full_content, content_type, urgency, 
                                    engagement_score, practical_score, universal_appeal, 
                                    quote_highlight, newsletter_priority, app_featured)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    title = VALUES(title),
                    summary = VALUES(summary),
                    full_content = VALUES(full_content),
                    content_type = VALUES(content_type),
                    urgency = VALUES(urgency),
                    engagement_score = VALUES(engagement_score),
                    practical_score = VALUES(practical_score),
                    universal_appeal = VALUES(universal_appeal),
                    quote_highlight = VALUES(quote_highlight),
                    newsletter_priority = VALUES(newsletter_priority),
                    app_featured = VALUES(app_featured),
                    updated_at = CURRENT_TIMESTAMP
            ");
            
            $stmt->execute([
                $articleData['id'],
                $articleData['title'],
                $articleData['summary'],
                $articleData['full_content'],
                $articleData['content_type'],
                $articleData['urgency'],
                $articleData['engagement_score'],
                $articleData['practical_score'],
                $articleData['universal_appeal'],
                $articleData['quote_highlight'] ?? null,
                $articleData['newsletter_priority'],
                $articleData['app_featured'] ? 1 : 0
            ]);
            
            // Clear existing relationships
            $this->clearArticleRelationships($articleData['id']);
            
            // Insert source information
            if (isset($articleData['source'])) {
                $this->insertSource($articleData['id'], $articleData['source']);
            }
            
            // Insert age groups
            if (isset($articleData['age_groups'])) {
                $this->insertAgeGroups($articleData['id'], $articleData['age_groups']);
            }
            
            // Insert categories
            if (isset($articleData['categories'])) {
                $this->insertCategories($articleData['id'], $articleData['categories']);
            }
            
            // Insert tags
            if (isset($articleData['tags'])) {
                $this->insertTags($articleData['id'], $articleData['tags']);
            }
            
            // Insert takeaways
            if (isset($articleData['actionable_takeaways'])) {
                $this->insertTakeaways($articleData['id'], $articleData['actionable_takeaways']);
            }
            
            // Insert related topics
            if (isset($articleData['related_topics'])) {
                $this->insertRelatedTopics($articleData['id'], $articleData['related_topics']);
            }
            
            $this->pdo->commit();
            
        } catch (Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }
    
    private function clearArticleRelationships($articleId) {
        $tables = ['sources', 'article_age_groups', 'article_categories', 'article_tags', 'takeaways', 'related_topics'];
        
        foreach ($tables as $table) {
            $stmt = $this->pdo->prepare("DELETE FROM {$table} WHERE article_id = ?");
            $stmt->execute([$articleId]);
        }
    }
    
    private function insertSource($articleId, $sourceData) {
        $stmt = $this->pdo->prepare("
            INSERT INTO sources (article_id, podcast_title, episode_title, episode_url, media_url, timestamp, host_name)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $articleId,
            $sourceData['podcast_title'] ?? null,
            $sourceData['episode_title'] ?? null,
            $sourceData['episode_url'] ?? null,
            $sourceData['media_url'] ?? null,
            $sourceData['timestamp'] ?? null,
            $sourceData['host_name'] ?? null
        ]);
    }
    
    private function insertAgeGroups($articleId, $ageGroups) {
        foreach ($ageGroups as $ageGroup) {
            $ageGroupId = $this->getAgeGroupId($ageGroup);
            if ($ageGroupId) {
                $stmt = $this->pdo->prepare("INSERT INTO article_age_groups (article_id, age_group_id) VALUES (?, ?)");
                $stmt->execute([$articleId, $ageGroupId]);
            }
        }
    }
    
    private function insertCategories($articleId, $categories) {
        foreach ($categories as $category) {
            $categoryId = $this->getCategoryId($category);
            if ($categoryId) {
                $stmt = $this->pdo->prepare("INSERT INTO article_categories (article_id, category_id) VALUES (?, ?)");
                $stmt->execute([$articleId, $categoryId]);
            }
        }
    }
    
    private function insertTags($articleId, $tags) {
        foreach ($tags as $tag) {
            $tagId = $this->getOrCreateTagId($tag);
            $stmt = $this->pdo->prepare("INSERT INTO article_tags (article_id, tag_id) VALUES (?, ?)");
            $stmt->execute([$articleId, $tagId]);
        }
    }
    
    private function insertTakeaways($articleId, $takeaways) {
        foreach ($takeaways as $index => $takeaway) {
            $stmt = $this->pdo->prepare("INSERT INTO takeaways (article_id, takeaway, sort_order) VALUES (?, ?, ?)");
            $stmt->execute([$articleId, $takeaway, $index + 1]);
        }
    }
    
    private function insertRelatedTopics($articleId, $topics) {
        foreach ($topics as $topic) {
            $stmt = $this->pdo->prepare("INSERT INTO related_topics (article_id, topic) VALUES (?, ?)");
            $stmt->execute([$articleId, $topic]);
        }
    }
    
    private function getAgeGroupId($name) {
        $stmt = $this->pdo->prepare("SELECT id FROM age_groups WHERE name = ?");
        $stmt->execute([$name]);
        $result = $stmt->fetch();
        return $result ? $result['id'] : null;
    }
    
    private function getCategoryId($name) {
        $stmt = $this->pdo->prepare("SELECT id FROM categories WHERE name = ?");
        $stmt->execute([$name]);
        $result = $stmt->fetch();
        return $result ? $result['id'] : null;
    }
    
    private function getOrCreateTagId($name) {
        $stmt = $this->pdo->prepare("SELECT id FROM tags WHERE name = ?");
        $stmt->execute([$name]);
        $result = $stmt->fetch();
        
        if ($result) {
            return $result['id'];
        }
        
        $stmt = $this->pdo->prepare("INSERT INTO tags (name) VALUES (?)");
        $stmt->execute([$name]);
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Get articles with filtering and pagination
     */
    public function getArticles($filters = [], $page = 1, $perPage = 10) {
        $where = [];
        $params = [];
        
        if (!empty($filters['age_group'])) {
            $where[] = "EXISTS (SELECT 1 FROM article_age_groups aag JOIN age_groups ag ON aag.age_group_id = ag.id WHERE aag.article_id = a.id AND ag.name = ?)";
            $params[] = $filters['age_group'];
        }
        
        if (!empty($filters['category'])) {
            $where[] = "EXISTS (SELECT 1 FROM article_categories ac JOIN categories c ON ac.category_id = c.id WHERE ac.article_id = a.id AND c.name = ?)";
            $params[] = $filters['category'];
        }
        
        if (!empty($filters['content_type'])) {
            $where[] = "a.content_type = ?";
            $params[] = $filters['content_type'];
        }
        
        if (!empty($filters['urgency'])) {
            $where[] = "a.urgency = ?";
            $params[] = $filters['urgency'];
        }
        
        if (!empty($filters['search'])) {
            $where[] = "(a.title LIKE ? OR a.summary LIKE ? OR a.full_content LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $offset = ($page - 1) * $perPage;
        
        $sql = "
            SELECT a.*, 
                   GROUP_CONCAT(DISTINCT ag.display_name) as age_groups,
                   GROUP_CONCAT(DISTINCT c.display_name) as categories
            FROM articles a
            LEFT JOIN article_age_groups aag ON a.id = aag.article_id
            LEFT JOIN age_groups ag ON aag.age_group_id = ag.id
            LEFT JOIN article_categories ac ON a.id = ac.article_id
            LEFT JOIN categories c ON ac.category_id = c.id
            {$whereClause}
            GROUP BY a.id
            ORDER BY a.newsletter_priority ASC, a.engagement_score DESC, a.created_at DESC
            LIMIT {$perPage} OFFSET {$offset}
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get single article with all related data
     */
    public function getArticle($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM articles WHERE id = ?");
        $stmt->execute([$id]);
        $article = $stmt->fetch();
        
        if (!$article) {
            return null;
        }
        
        // Get source
        $stmt = $this->pdo->prepare("SELECT * FROM sources WHERE article_id = ?");
        $stmt->execute([$id]);
        $article['source'] = $stmt->fetch();
        
        // Get age groups
        $stmt = $this->pdo->prepare("
            SELECT ag.name, ag.display_name 
            FROM article_age_groups aag 
            JOIN age_groups ag ON aag.age_group_id = ag.id 
            WHERE aag.article_id = ?
        ");
        $stmt->execute([$id]);
        $article['age_groups'] = $stmt->fetchAll();
        
        // Get categories
        $stmt = $this->pdo->prepare("
            SELECT c.name, c.display_name 
            FROM article_categories ac 
            JOIN categories c ON ac.category_id = c.id 
            WHERE ac.article_id = ?
        ");
        $stmt->execute([$id]);
        $article['categories'] = $stmt->fetchAll();
        
        // Get tags
        $stmt = $this->pdo->prepare("
            SELECT t.name 
            FROM article_tags at 
            JOIN tags t ON at.tag_id = t.id 
            WHERE at.article_id = ?
        ");
        $stmt->execute([$id]);
        $article['tags'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Get takeaways
        $stmt = $this->pdo->prepare("SELECT takeaway FROM takeaways WHERE article_id = ? ORDER BY sort_order");
        $stmt->execute([$id]);
        $article['takeaways'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Get related topics
        $stmt = $this->pdo->prepare("SELECT topic FROM related_topics WHERE article_id = ?");
        $stmt->execute([$id]);
        $article['related_topics'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        return $article;
    }
    
    /**
     * Get all age groups
     */
    public function getAgeGroups() {
        $stmt = $this->pdo->query("SELECT * FROM age_groups ORDER BY sort_order");
        return $stmt->fetchAll();
    }
    
    /**
     * Get all categories
     */
    public function getCategories() {
        $stmt = $this->pdo->query("SELECT * FROM categories ORDER BY sort_order");
        return $stmt->fetchAll();
    }
}
?>
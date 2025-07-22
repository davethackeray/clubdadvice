<?php
require_once 'config.php';
require_once 'classes/ArticleManager.php';

$articleManager = new ArticleManager();
$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['json_data']) && !empty($_POST['json_data'])) {
        try {
            $result = $articleManager->importFromJSON($_POST['json_data']);
            
            $message = "Import completed! {$result['imported']} articles imported successfully.";
            $messageType = 'success';
            
            if (!empty($result['errors'])) {
                $message .= "\n\nErrors encountered:\n" . implode("\n", $result['errors']);
                $messageType = 'warning';
            }
            
        } catch (Exception $e) {
            $message = "Import failed: " . $e->getMessage();
            $messageType = 'error';
        }
    } else {
        $message = "Please provide JSON data to import.";
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Articles - <?= SITE_NAME ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Georgia', serif;
            line-height: 1.6;
            color: #2c3e50;
            background-color: #f8f9fa;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 0;
            text-align: center;
            margin-bottom: 40px;
        }
        
        .logo {
            font-size: 2.5em;
            font-weight: bold;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .logo a {
            color: white;
            text-decoration: none;
        }
        
        .tagline {
            font-size: 1.1em;
            opacity: 0.9;
            font-style: italic;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 30px;
            color: #3498db;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.1em;
        }
        
        .back-link:hover {
            color: #2980b9;
        }
        
        .import-form {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 40px;
            margin-bottom: 40px;
        }
        
        .form-title {
            font-size: 2em;
            margin-bottom: 20px;
            color: #2c3e50;
            text-align: center;
        }
        
        .form-description {
            background-color: #e3f2fd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            color: #1976d2;
        }
        
        .form-description h3 {
            margin-bottom: 15px;
            color: #1565c0;
        }
        
        .form-description ul {
            margin-left: 20px;
        }
        
        .form-description li {
            margin-bottom: 5px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #2c3e50;
            font-size: 1.1em;
        }
        
        .form-group textarea {
            width: 100%;
            min-height: 400px;
            padding: 15px;
            border: 2px solid #ecf0f1;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            line-height: 1.4;
            resize: vertical;
        }
        
        .form-group textarea:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background-color: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background-color: #95a5a6;
            color: white;
            margin-left: 10px;
        }
        
        .btn-secondary:hover {
            background-color: #7f8c8d;
        }
        
        .message {
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            white-space: pre-line;
        }
        
        .message.success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .message.warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }
        
        .message.error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        .sample-json {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-top: 30px;
        }
        
        .sample-json h3 {
            margin-bottom: 15px;
            color: #2c3e50;
        }
        
        .sample-json pre {
            background-color: #2c3e50;
            color: #ecf0f1;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            font-size: 12px;
            line-height: 1.4;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            color: #3498db;
            margin-bottom: 10px;
        }
        
        .stat-label {
            color: #7f8c8d;
            font-size: 1.1em;
        }
        
        @media (max-width: 768px) {
            .import-form {
                padding: 25px;
            }
            
            .form-title {
                font-size: 1.6em;
            }
            
            .form-group textarea {
                min-height: 300px;
            }
            
            .btn {
                width: 100%;
                margin-bottom: 10px;
            }
            
            .btn-secondary {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo">
                <a href="index.php"><?= SITE_NAME ?></a>
            </div>
            <div class="tagline"><?= SITE_TAGLINE ?></div>
        </div>
    </header>
    
    <div class="container">
        <a href="index.php" class="back-link">← Back to Articles</a>
        
        <?php if (!empty($message)): ?>
            <div class="message <?= $messageType ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        
        <div class="stats">
            <?php
            try {
                $pdo = getDBConnection();
                
                $stmt = $pdo->query("SELECT COUNT(*) as total FROM articles");
                $totalArticles = $stmt->fetch()['total'];
                
                $stmt = $pdo->query("SELECT COUNT(*) as total FROM articles WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
                $recentArticles = $stmt->fetch()['total'];
                
                $stmt = $pdo->query("SELECT COUNT(DISTINCT name) as total FROM tags");
                $totalTags = $stmt->fetch()['total'];
                
                $stmt = $pdo->query("SELECT AVG(engagement_score) as avg_score FROM articles WHERE engagement_score > 0");
                $avgEngagement = round($stmt->fetch()['avg_score'] ?? 0, 1);
            } catch (Exception $e) {
                $totalArticles = $recentArticles = $totalTags = $avgEngagement = 0;
            }
            ?>
            <div class="stat-card">
                <div class="stat-number"><?= $totalArticles ?></div>
                <div class="stat-label">Total Articles</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $recentArticles ?></div>
                <div class="stat-label">This Week</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $totalTags ?></div>
                <div class="stat-label">Unique Tags</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $avgEngagement ?></div>
                <div class="stat-label">Avg Engagement</div>
            </div>
        </div>
        
        <div class="import-form">
            <h1 class="form-title">Import Articles from JSON Feed</h1>
            
            <div class="form-description">
                <h3>How to Import Articles:</h3>
                <ul>
                    <li>Paste your JSON feed data in the textarea below</li>
                    <li>The JSON should contain a "newsletter_content" array with article objects</li>
                    <li>Each article will be processed and stored in the database</li>
                    <li>Existing articles with the same ID will be updated</li>
                    <li>All related data (tags, categories, takeaways, etc.) will be imported</li>
                </ul>
            </div>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="json_data">JSON Feed Data:</label>
                    <textarea name="json_data" id="json_data" placeholder="Paste your JSON feed here..." required><?= isset($_POST['json_data']) ? htmlspecialchars($_POST['json_data']) : '' ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Import Articles</button>
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('json_data').value = ''">Clear</button>
            </form>
            
            <div class="sample-json">
                <h3>Expected JSON Structure:</h3>
                <pre>{
  "newsletter_content": [
    {
      "id": "unique_article_id",
      "title": "Article Title",
      "summary": "Brief summary of the article",
      "full_content": "Full article content...",
      "content_type": "practical-tip",
      "age_groups": ["preschooler", "school-age"],
      "categories": ["educational-support", "communication-skills"],
      "tags": ["school readiness", "parenting tips"],
      "urgency": "timeless",
      "engagement_score": 9,
      "practical_score": 8,
      "universal_appeal": 9,
      "source": {
        "podcast_title": "The PedsDocTalk Podcast",
        "episode_title": "Episode Title",
        "episode_url": "https://example.com/episode",
        "host_name": "Dr. Host Name"
      },
      "actionable_takeaways": [
        "First actionable tip",
        "Second actionable tip"
      ],
      "quote_highlight": "Important quote from the content",
      "newsletter_priority": 1,
      "app_featured": true
    }
  ]
}</pre>
            </div>
        </div>
    </div>
</body>
</html>
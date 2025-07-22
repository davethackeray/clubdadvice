<?php
require_once 'config.php';
require_once 'classes/ArticleManager.php';

$articleManager = new ArticleManager();
$newsletter = '';
$selectedArticles = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filters = [
        'age_group' => $_POST['age_group'] ?? '',
        'category' => $_POST['category'] ?? '',
        'content_type' => $_POST['content_type'] ?? '',
        'urgency' => $_POST['urgency'] ?? '',
        'limit' => intval($_POST['limit'] ?? 5)
    ];
    
    $selectedIds = $_POST['selected_articles'] ?? [];
    $newsletterTitle = $_POST['newsletter_title'] ?? 'Dadvice Newsletter';
    $newsletterSubtitle = $_POST['newsletter_subtitle'] ?? 'Your weekly dose of parenting wisdom';
    
    if (!empty($selectedIds)) {
        foreach ($selectedIds as $articleId) {
            $article = $articleManager->getArticle($articleId);
            if ($article) {
                $selectedArticles[] = $article;
            }
        }
        
        if (!empty($selectedArticles)) {
            $newsletter = generateNewsletterHTML($selectedArticles, $newsletterTitle, $newsletterSubtitle);
        }
    }
}

// Get filter options
try {
    $ageGroups = $articleManager->getAgeGroups();
    $categories = $articleManager->getCategories();
    $articles = $articleManager->getArticles([], 1, 50); // Get recent articles for selection
} catch (Exception $e) {
    $ageGroups = [];
    $categories = [];
    $articles = [];
}

function generateNewsletterHTML($articles, $title, $subtitle) {
    ob_start();
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
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
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .logo {
            font-size: 2.5em;
            font-weight: bold;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .tagline {
            font-size: 1.1em;
            opacity: 0.9;
            font-style: italic;
        }
        
        .issue-info {
            background-color: #ecf0f1;
            padding: 20px 30px;
            border-left: 4px solid #3498db;
            margin: 0;
        }
        
        .issue-info h2 {
            color: #2980b9;
            margin-bottom: 10px;
            font-size: 1.3em;
        }
        
        .content {
            padding: 30px;
        }
        
        .intro {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 30px;
            font-size: 1.1em;
        }
        
        .article {
            margin-bottom: 40px;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 30px;
        }
        
        .article:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .article-header {
            margin-bottom: 20px;
        }
        
        .article-title {
            color: #2c3e50;
            font-size: 1.4em;
            margin-bottom: 10px;
            line-height: 1.3;
        }
        
        .article-summary {
            color: #7f8c8d;
            font-style: italic;
            margin-bottom: 15px;
        }
        
        .quote-highlight {
            background-color: #e8f5e8;
            border-left: 4px solid #27ae60;
            padding: 20px;
            margin: 20px 0;
            font-style: italic;
            font-size: 1.1em;
            color: #2c3e50;
        }
        
        .takeaways {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .takeaways h4 {
            color: #e74c3c;
            margin-bottom: 15px;
            font-size: 1.1em;
        }
        
        .takeaways ul {
            list-style: none;
            padding-left: 0;
        }
        
        .takeaways li {
            margin-bottom: 10px;
            padding-left: 25px;
            position: relative;
        }
        
        .takeaways li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #27ae60;
            font-weight: bold;
        }
        
        .source-link {
            font-size: 0.9em;
            margin-top: 10px;
        }
        
        .source-link a {
            color: #27ae60;
            text-decoration: none;
        }
        
        .footer {
            background-color: #34495e;
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .footer p {
            margin-bottom: 10px;
        }
        
        .dad-emoji {
            font-size: 1.2em;
            margin: 0 5px;
        }
        
        @media (max-width: 600px) {
            .container {
                margin: 0;
                box-shadow: none;
            }
            
            .header, .content, .footer {
                padding: 20px;
            }
            
            .logo {
                font-size: 2em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="logo">DADVICE</div>
            <div class="tagline">Navigating fatherhood, one insight at a time</div>
        </header>
        
        <div class="issue-info">
            <h2><?= htmlspecialchars($subtitle) ?></h2>
            <p><strong>Issue Date:</strong> <?= date('F j, Y') ?></p>
        </div>
        
        <div class="content">
            <div class="intro">
                <p><strong>Hey Dad,</strong></p>
                <p>Here's your curated collection of parenting wisdom to help you navigate the beautiful chaos of raising children. Each insight has been carefully selected to give you practical, actionable advice you can use right away.</p>
                <p>Let's dive in.</p>
            </div>

            <?php foreach ($articles as $index => $article): ?>
            <article class="article">
                <div class="article-header">
                    <h3 class="article-title"><?= htmlspecialchars($article['title']) ?></h3>
                    <p class="article-summary"><?= htmlspecialchars($article['summary']) ?></p>
                </div>
                
                <div class="article-content">
                    <?= nl2br(htmlspecialchars($article['full_content'])) ?>
                </div>
                
                <?php if (!empty($article['quote_highlight'])): ?>
                    <div class="quote-highlight">
                        <?= htmlspecialchars($article['quote_highlight']) ?>
                        <?php if (!empty($article['source'])): ?>
                            <div class="source-link">
                                <small><strong>Source:</strong> 
                                <?php if (!empty($article['source']['episode_url'])): ?>
                                    <a href="<?= htmlspecialchars($article['source']['episode_url']) ?>"><?= htmlspecialchars($article['source']['podcast_title']) ?> - "<?= htmlspecialchars($article['source']['episode_title']) ?>"</a>
                                <?php else: ?>
                                    <?= htmlspecialchars($article['source']['podcast_title']) ?> - "<?= htmlspecialchars($article['source']['episode_title']) ?>"
                                <?php endif; ?>
                                <?php if (!empty($article['source']['host_name'])): ?>
                                    with <?= htmlspecialchars($article['source']['host_name']) ?>
                                <?php endif; ?>
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($article['takeaways'])): ?>
                    <div class="takeaways">
                        <h4>Dad Action Plan:</h4>
                        <ul>
                            <?php foreach ($article['takeaways'] as $takeaway): ?>
                                <li><?= htmlspecialchars($takeaway) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </article>
            <?php endforeach; ?>
        </div>
        
        <footer class="footer">
            <p><strong>That's a wrap, Dad!</strong> <span class="dad-emoji">👨‍👧‍👦</span></p>
            <p>Remember: You're doing better than you think. Trust your instincts, stay curious, and don't forget to take care of yourself too.</p>
            <p>You've got this. <span class="dad-emoji">💪</span></p>
            <hr style="margin: 20px 0; border: none; border-top: 1px solid #7f8c8d;">
            <p style="font-size: 0.9em; opacity: 0.8;">
                <strong>Dadvice Newsletter</strong><br>
                Helping new dads navigate the beautiful chaos of raising children<br>
                <em>Because being a dad is the hardest job you'll ever love</em>
            </p>
        </footer>
    </div>
</body>
</html>
    <?php
    return ob_get_clean();
}

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsletter Generator - <?= SITE_NAME ?></title>
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
            max-width: 1400px;
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
        
        .generator-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .generator-panel {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 30px;
        }
        
        .panel-title {
            font-size: 1.8em;
            margin-bottom: 25px;
            color: #2c3e50;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 10px;
        }
        
        .form-section {
            margin-bottom: 30px;
        }
        
        .form-section h3 {
            margin-bottom: 15px;
            color: #2c3e50;
            font-size: 1.2em;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 2px solid #ecf0f1;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3498db;
        }
        
        .articles-list {
            max-height: 400px;
            overflow-y: auto;
            border: 2px solid #ecf0f1;
            border-radius: 8px;
            padding: 15px;
        }
        
        .article-item {
            display: flex;
            align-items: flex-start;
            padding: 15px;
            border-bottom: 1px solid #ecf0f1;
            margin-bottom: 10px;
        }
        
        .article-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .article-checkbox {
            margin-right: 15px;
            margin-top: 5px;
        }
        
        .article-info {
            flex: 1;
        }
        
        .article-info h4 {
            margin-bottom: 5px;
            color: #2c3e50;
            font-size: 1em;
        }
        
        .article-info p {
            color: #7f8c8d;
            font-size: 0.9em;
            margin-bottom: 8px;
        }
        
        .article-meta {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .meta-tag {
            background-color: #ecf0f1;
            color: #2c3e50;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.75em;
            font-weight: bold;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
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
        }
        
        .btn-secondary {
            background-color: #95a5a6;
            color: white;
            margin-left: 10px;
        }
        
        .btn-secondary:hover {
            background-color: #7f8c8d;
        }
        
        .newsletter-preview {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 30px;
            margin-top: 40px;
        }
        
        .preview-actions {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 2px solid #ecf0f1;
        }
        
        .newsletter-html {
            border: 2px solid #ecf0f1;
            border-radius: 8px;
            max-height: 600px;
            overflow: auto;
        }
        
        @media (max-width: 1200px) {
            .generator-layout {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .preview-actions {
                flex-direction: column;
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
        
        <div class="generator-layout">
            <div class="generator-panel">
                <h1 class="panel-title">Newsletter Generator</h1>
                
                <form method="POST" action="" id="newsletter-form">
                    <div class="form-section">
                        <h3>Newsletter Settings</h3>
                        <div class="form-group">
                            <label for="newsletter_title">Newsletter Title</label>
                            <input type="text" name="newsletter_title" id="newsletter_title" 
                                   value="<?= htmlspecialchars($_POST['newsletter_title'] ?? 'Dadvice Newsletter') ?>">
                        </div>
                        <div class="form-group">
                            <label for="newsletter_subtitle">Newsletter Subtitle</label>
                            <input type="text" name="newsletter_subtitle" id="newsletter_subtitle" 
                                   value="<?= htmlspecialchars($_POST['newsletter_subtitle'] ?? 'Your weekly dose of parenting wisdom') ?>">
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Filter Articles</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="age_group">Age Group</label>
                                <select name="age_group" id="age_group">
                                    <option value="">All Ages</option>
                                    <?php foreach ($ageGroups as $ageGroup): ?>
                                        <option value="<?= htmlspecialchars($ageGroup['name']) ?>" 
                                                <?= ($_POST['age_group'] ?? '') === $ageGroup['name'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($ageGroup['display_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="category">Category</label>
                                <select name="category" id="category">
                                    <option value="">All Categories</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= htmlspecialchars($category['name']) ?>" 
                                                <?= ($_POST['category'] ?? '') === $category['name'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($category['display_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="content_type">Content Type</label>
                                <select name="content_type" id="content_type">
                                    <option value="">All Types</option>
                                    <?php foreach ($contentTypes as $value => $label): ?>
                                        <option value="<?= htmlspecialchars($value) ?>" 
                                                <?= ($_POST['content_type'] ?? '') === $value ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($label) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="urgency">Urgency</label>
                                <select name="urgency" id="urgency">
                                    <option value="">All Urgency</option>
                                    <?php foreach ($urgencyTypes as $value => $label): ?>
                                        <option value="<?= htmlspecialchars($value) ?>" 
                                                <?= ($_POST['urgency'] ?? '') === $value ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($label) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Select Articles</h3>
                        <div class="articles-list">
                            <?php if (empty($articles)): ?>
                                <p style="text-align: center; color: #7f8c8d; padding: 20px;">
                                    No articles available. Import some articles first.
                                </p>
                            <?php else: ?>
                                <?php foreach ($articles as $article): ?>
                                    <div class="article-item">
                                        <input type="checkbox" name="selected_articles[]" 
                                               value="<?= htmlspecialchars($article['id']) ?>" 
                                               class="article-checkbox"
                                               <?= in_array($article['id'], $_POST['selected_articles'] ?? []) ? 'checked' : '' ?>>
                                        <div class="article-info">
                                            <h4><?= htmlspecialchars($article['title']) ?></h4>
                                            <p><?= htmlspecialchars(truncateText($article['summary'], 100)) ?></p>
                                            <div class="article-meta">
                                                <span class="meta-tag"><?= htmlspecialchars($contentTypes[$article['content_type']] ?? $article['content_type']) ?></span>
                                                <span class="meta-tag">Score: <?= $article['engagement_score'] ?></span>
                                                <?php if ($article['age_groups']): ?>
                                                    <span class="meta-tag"><?= htmlspecialchars($article['age_groups']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Generate Newsletter</button>
                    <button type="button" class="btn btn-secondary" onclick="clearSelection()">Clear Selection</button>
                </form>
            </div>
            
            <div class="generator-panel">
                <h2 class="panel-title">Quick Actions</h2>
                
                <div class="form-section">
                    <h3>Age-Specific Newsletters</h3>
                    <p style="margin-bottom: 15px; color: #7f8c8d;">Generate newsletters tailored to specific age groups:</p>
                    
                    <?php foreach ($ageGroups as $ageGroup): ?>
                        <button type="button" class="btn btn-secondary" style="margin: 5px; width: calc(50% - 10px);" 
                                onclick="generateAgeSpecific('<?= htmlspecialchars($ageGroup['name']) ?>', '<?= htmlspecialchars($ageGroup['display_name']) ?>')">
                            <?= htmlspecialchars($ageGroup['display_name']) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
                
                <div class="form-section">
                    <h3>Quick Templates</h3>
                    <button type="button" class="btn btn-secondary" style="margin: 5px; width: calc(50% - 10px);" 
                            onclick="generateTemplate('trending')">This Week's Trending</button>
                    <button type="button" class="btn btn-secondary" style="margin: 5px; width: calc(50% - 10px);" 
                            onclick="generateTemplate('practical')">Practical Tips Only</button>
                    <button type="button" class="btn btn-secondary" style="margin: 5px; width: calc(50% - 10px);" 
                            onclick="generateTemplate('seasonal')">Seasonal Content</button>
                    <button type="button" class="btn btn-secondary" style="margin: 5px; width: calc(50% - 10px);" 
                            onclick="generateTemplate('high-engagement')">High Engagement</button>
                </div>
                
                <div class="form-section">
                    <h3>Newsletter Stats</h3>
                    <div style="background-color: #f8f9fa; padding: 15px; border-radius: 8px;">
                        <p><strong>Total Articles:</strong> <?= count($articles) ?></p>
                        <p><strong>Selected:</strong> <span id="selected-count">0</span></p>
                        <p><strong>Avg Engagement:</strong> <?= count($articles) > 0 ? round(array_sum(array_column($articles, 'engagement_score')) / count($articles), 1) : 0 ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if (!empty($newsletter)): ?>
            <div class="newsletter-preview">
                <div class="preview-actions">
                    <h2 style="flex: 1; margin: 0;">Newsletter Preview</h2>
                    <button type="button" class="btn btn-primary" onclick="downloadNewsletter()">Download HTML</button>
                    <button type="button" class="btn btn-secondary" onclick="copyToClipboard()">Copy HTML</button>
                </div>
                
                <div class="newsletter-html">
                    <?= $newsletter ?>
                </div>
                
                <textarea id="newsletter-source" style="display: none;"><?= htmlspecialchars($newsletter) ?></textarea>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        function clearSelection() {
            document.querySelectorAll('input[name="selected_articles[]"]').forEach(cb => cb.checked = false);
            updateSelectedCount();
        }
        
        function updateSelectedCount() {
            const count = document.querySelectorAll('input[name="selected_articles[]"]:checked').length;
            document.getElementById('selected-count').textContent = count;
        }
        
        function generateAgeSpecific(ageGroup, displayName) {
            document.getElementById('age_group').value = ageGroup;
            document.getElementById('newsletter_title').value = 'Dadvice Newsletter';
            document.getElementById('newsletter_subtitle').value = 'Parenting wisdom for ' + displayName;
            
            // Auto-select top articles for this age group
            document.querySelectorAll('input[name="selected_articles[]"]').forEach(cb => {
                const articleItem = cb.closest('.article-item');
                const metaTags = articleItem.querySelectorAll('.meta-tag');
                let hasAgeGroup = false;
                metaTags.forEach(tag => {
                    if (tag.textContent.toLowerCase().includes(displayName.toLowerCase())) {
                        hasAgeGroup = true;
                    }
                });
                cb.checked = hasAgeGroup;
            });
            
            updateSelectedCount();
        }
        
        function generateTemplate(type) {
            // Clear current selection
            clearSelection();
            
            switch(type) {
                case 'trending':
                    document.getElementById('urgency').value = 'trending';
                    document.getElementById('newsletter_subtitle').value = "This week's trending parenting insights";
                    break;
                case 'practical':
                    document.getElementById('content_type').value = 'practical-tip';
                    document.getElementById('newsletter_subtitle').value = "Practical tips you can use today";
                    break;
                case 'seasonal':
                    document.getElementById('urgency').value = 'seasonal';
                    document.getElementById('newsletter_subtitle').value = "Timely advice for the season";
                    break;
                case 'high-engagement':
                    document.getElementById('newsletter_subtitle').value = "Our most engaging parenting content";
                    // Auto-select high engagement articles
                    document.querySelectorAll('input[name="selected_articles[]"]').forEach(cb => {
                        const articleItem = cb.closest('.article-item');
                        const scoreTag = Array.from(articleItem.querySelectorAll('.meta-tag')).find(tag => 
                            tag.textContent.includes('Score:'));
                        if (scoreTag) {
                            const score = parseInt(scoreTag.textContent.replace('Score: ', ''));
                            if (score >= 8) {
                                cb.checked = true;
                            }
                        }
                    });
                    break;
            }
            
            updateSelectedCount();
        }
        
        function downloadNewsletter() {
            const content = document.getElementById('newsletter-source').value;
            const blob = new Blob([content], { type: 'text/html' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'dadvice-newsletter-' + new Date().toISOString().split('T')[0] + '.html';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }
        
        function copyToClipboard() {
            const textarea = document.getElementById('newsletter-source');
            textarea.select();
            document.execCommand('copy');
            alert('Newsletter HTML copied to clipboard!');
        }
        
        // Update selected count on page load and when checkboxes change
        document.addEventListener('DOMContentLoaded', function() {
            updateSelectedCount();
            document.querySelectorAll('input[name="selected_articles[]"]').forEach(cb => {
                cb.addEventListener('change', updateSelectedCount);
            });
        });
    </script>
</body>
</html>
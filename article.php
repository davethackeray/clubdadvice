<?php
require_once 'config.php';
require_once 'classes/ArticleManager.php';

$articleManager = new ArticleManager();
$articleId = $_GET['id'] ?? '';

if (empty($articleId)) {
    header('Location: index.php');
    exit;
}

$article = $articleManager->getArticle($articleId);

if (!$article) {
    header('HTTP/1.0 404 Not Found');
    echo "Article not found";
    exit;
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
    <title><?= htmlspecialchars($article['title']) ?> - <?= SITE_NAME ?></title>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7749455827894286" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="assets/css/article.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo">
                <a href="index.php"><?= SITE_NAME ?></a>
            </div>
        </div>
    </header>
    
    <div class="container">
        <a href="index.php" class="back-link">← Back to Articles</a>
        
        <article class="article">
            <div class="article-header">
                <h1 class="article-title"><?= htmlspecialchars($article['title']) ?></h1>
                <p class="article-summary"><?= htmlspecialchars($article['summary']) ?></p>
                
                <div class="article-meta">
                    <span class="meta-tag content-type">
                        <?= htmlspecialchars($contentTypes[$article['content_type']] ?? $article['content_type']) ?>
                    </span>
                    
                    <?php foreach ($article['age_groups'] as $ageGroup): ?>
                        <span class="meta-tag age-group"><?= htmlspecialchars($ageGroup['display_name']) ?></span>
                    <?php endforeach; ?>
                    
                    <?php foreach ($article['categories'] as $category): ?>
                        <span class="meta-tag category"><?= htmlspecialchars($category['display_name']) ?></span>
                    <?php endforeach; ?>
                </div>
                
                <div class="article-scores">
                    <div class="score">
                        <div class="score-value"><?= $article['engagement_score'] ?></div>
                        <div class="score-label">Engagement Score</div>
                    </div>
                    <div class="score">
                        <div class="score-value"><?= $article['practical_score'] ?></div>
                        <div class="score-label">Practical Score</div>
                    </div>
                    <div class="score">
                        <div class="score-value"><?= $article['universal_appeal'] ?></div>
                        <div class="score-label">Universal Appeal</div>
                    </div>
                </div>
            </div>
            
            <div class="article-content">
                <div class="article-text">
                    <?= nl2br(htmlspecialchars($article['full_content'])) ?>
                </div>
                
                <!-- Mid-article Ad -->
                <div class="mid-article-ad">
                    <ins class="adsbygoogle"
                         style="display:block"
                         data-ad-client="ca-pub-7749455827894286"
                         data-ad-slot="1234567892"
                         data-ad-format="auto"
                         data-full-width-responsive="true"></ins>
                    <script>
                         (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
                </div>
                
                <?php if (!empty($article['quote_highlight'])): ?>
                    <div class="quote-highlight">
                        <?= htmlspecialchars($article['quote_highlight']) ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($article['takeaways'])): ?>
                    <div class="takeaways">
                        <h3>Dad Action Plan:</h3>
                        <ul>
                            <?php foreach ($article['takeaways'] as $takeaway): ?>
                                <li><?= htmlspecialchars($takeaway) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($article['source'])): ?>
                    <div class="source-info">
                        <h3>Source</h3>
                        <?php if (!empty($article['source']['podcast_title'])): ?>
                            <p><strong>Podcast:</strong> <?= htmlspecialchars($article['source']['podcast_title']) ?></p>
                        <?php endif; ?>
                        
                        <?php if (!empty($article['source']['episode_title'])): ?>
                            <p><strong>Episode:</strong> 
                                <?php if (!empty($article['source']['episode_url'])): ?>
                                    <a href="<?= htmlspecialchars($article['source']['episode_url']) ?>" target="_blank">
                                        <?= htmlspecialchars($article['source']['episode_title']) ?>
                                    </a>
                                <?php else: ?>
                                    <?= htmlspecialchars($article['source']['episode_title']) ?>
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>
                        
                        <?php if (!empty($article['source']['host_name'])): ?>
                            <p><strong>Host:</strong> <?= htmlspecialchars($article['source']['host_name']) ?></p>
                        <?php endif; ?>
                        
                        <?php if (!empty($article['source']['timestamp'])): ?>
                            <p><strong>Timestamp:</strong> <?= htmlspecialchars($article['source']['timestamp']) ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($article['tags'])): ?>
                    <div class="tags">
                        <h3>Tags</h3>
                        <div class="tag-list">
                            <?php foreach ($article['tags'] as $tag): ?>
                                <span class="tag"><?= htmlspecialchars($tag) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($article['related_topics'])): ?>
                    <div class="related-topics">
                        <h3>Related Topics</h3>
                        <ul>
                            <?php foreach ($article['related_topics'] as $topic): ?>
                                <li><?= htmlspecialchars($topic) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </article>
    </div>
</body>
</html>
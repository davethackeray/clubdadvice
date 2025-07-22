<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= SITE_TAGLINE ?>">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME ?></title>
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/club-dadvice.css">
    <?php if (isset($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?= $css ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#667eea">
    <link rel="manifest" href="manifest.json">
    <link rel="apple-touch-icon" href="assets/images/icon-192x192.png">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?= isset($pageTitle) ? $pageTitle : SITE_NAME ?>">
    <meta property="og:description" content="<?= SITE_TAGLINE ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= BASE_URL . $_SERVER['REQUEST_URI'] ?>">
    <meta property="og:image" content="<?= BASE_URL ?>/assets/images/og-image.jpg">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= isset($pageTitle) ? $pageTitle : SITE_NAME ?>">
    <meta name="twitter:description" content="<?= SITE_TAGLINE ?>">
    <meta name="twitter:image" content="<?= BASE_URL ?>/assets/images/og-image.jpg">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo">
                <a href="index.php"><?= SITE_NAME ?></a>
            </div>
            <div class="tagline"><?= SITE_TAGLINE ?></div>
            
            <!-- Navigation will be added as features are implemented -->
            <nav class="main-nav" style="display: none;">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <?php if (defined('ENABLE_COMMUNITY') && ENABLE_COMMUNITY): ?>
                        <li><a href="community.php">Community</a></li>
                    <?php endif; ?>
                    <?php if (defined('ENABLE_PODCAST') && ENABLE_PODCAST): ?>
                        <li><a href="podcast.php">Podcast</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            
            <!-- User menu will be added when authentication is implemented -->
            <div class="user-menu" style="display: none;">
                <?php if (function_exists('isLoggedIn') && isLoggedIn()): ?>
                    <a href="profile.php">Profile</a>
                    <a href="bookmarks.php">Bookmarks</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    
    <main class="main-content">
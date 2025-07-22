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
        <div class="container header-container">
            <div class="header-main">
                <div class="logo">
                    <a href="index.php"><?= SITE_NAME ?></a>
                </div>
                <div class="tagline"><?= SITE_TAGLINE ?></div>
            </div>
            
            <!-- User authentication menu -->
            <div class="user-menu">
                <?php
                // Check if user is logged in
                $isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
                if ($isLoggedIn):
                    $displayName = $_SESSION['user_display_name'] ?? $_SESSION['user_first_name'] ?? 'User';
                ?>
                    <div class="user-dropdown">
                        <button class="user-toggle" onclick="toggleUserMenu()">
                            <span class="user-avatar"><?= strtoupper(substr($displayName, 0, 1)) ?></span>
                            <span class="user-name"><?= htmlspecialchars($displayName) ?></span>
                            <span class="dropdown-arrow">▼</span>
                        </button>
                        <div class="user-dropdown-menu" id="userDropdown">
                            <a href="profile.php">My Profile</a>
                            <a href="bookmarks.php">Bookmarks</a>
                            <a href="logout.php">Sign Out</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="auth-links">
                        <a href="login.php" class="auth-link">Sign In</a>
                        <a href="register.php" class="auth-link auth-link-primary">Join Club</a>
                    </div>
                <?php endif; ?>
            </div>
            
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
        </div>
    </header>
    
    <main class="main-content">
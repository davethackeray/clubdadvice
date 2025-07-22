<?php
require_once 'config.php';
require_once 'classes/User.php';

$user = new User();

// Redirect if not logged in
if (!$user->isLoggedIn()) {
    header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$current_user = $user->getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookmarks - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="assets/css/club-dadvice.css">
    <style>
        .bookmarks-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
        }
        
        .bookmarks-header {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .coming-soon {
            background: white;
            padding: 3rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            color: #666;
        }
        
        .coming-soon h2 {
            color: #333;
            margin-bottom: 1rem;
        }
        
        .coming-soon p {
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <div class="bookmarks-container">
            <div class="bookmarks-header">
                <h1>My Bookmarks</h1>
                <p>Your saved articles will appear here</p>
            </div>
            
            <div class="coming-soon">
                <h2>📚 Bookmarks Coming Soon!</h2>
                <p>We're working on the bookmark functionality. Soon you'll be able to save your favourite articles and access them here.</p>
                <p>This feature will be available in the next phase of development.</p>
                <a href="index.php" class="btn btn-primary">Browse Articles</a>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>
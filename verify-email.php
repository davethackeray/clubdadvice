<?php
require_once 'config.php';
require_once 'classes/User.php';

$user = new User();
$error = '';
$success = '';
$token = $_GET['token'] ?? '';

if (empty($token)) {
    $error = 'Invalid verification link.';
} else {
    try {
        $result = $user->verifyEmail($token);
        $success = "Your email has been verified successfully! You can now sign in to your account.";
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="assets/css/club-dadvice.css">
    <style>
        .auth-container {
            max-width: 500px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .error-message {
            background: #fee;
            color: #c33;
            padding: 1rem;
            border-radius: 4px;
            border-left: 4px solid #c33;
            margin-bottom: 1rem;
            text-align: left;
        }
        
        .success-message {
            background: #efe;
            color: #363;
            padding: 1rem;
            border-radius: 4px;
            border-left: 4px solid #363;
            margin-bottom: 1rem;
            text-align: left;
        }
        
        .auth-links {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #eee;
        }
        
        .auth-links a {
            color: #007cba;
            text-decoration: none;
        }
        
        .auth-links a:hover {
            text-decoration: underline;
        }
        
        .verification-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .success-icon {
            color: #4CAF50;
        }
        
        .error-icon {
            color: #f44336;
        }
        
        @media (max-width: 600px) {
            .auth-container {
                margin: 1rem;
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <div class="auth-container">
            <?php if ($success): ?>
                <div class="verification-icon success-icon">✓</div>
                <h1>Email Verified!</h1>
                <div class="success-message"><?= htmlspecialchars($success) ?></div>
                
                <div class="auth-links">
                    <p><a href="login.php" class="btn btn-primary">Sign In Now</a></p>
                </div>
                
            <?php else: ?>
                <div class="verification-icon error-icon">✗</div>
                <h1>Verification Failed</h1>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
                
                <div class="auth-links">
                    <p>The verification link may have expired or been used already.</p>
                    <p><a href="register.php">Create a new account</a> or <a href="login.php">try signing in</a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>
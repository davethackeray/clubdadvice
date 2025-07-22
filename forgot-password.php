<?php
require_once 'config.php';
require_once 'classes/User.php';

$user = new User();
$error = '';
$success = '';

// Redirect if already logged in
if ($user->isLoggedIn()) {
    header('Location: profile.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // CSRF Protection
        requireCSRFToken();
        
        // Rate limiting - 3 password reset requests per 60 minutes per IP
        $client_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        checkRateLimit($client_ip, 'password_reset', 3, 60);
        
        $email = sanitizeInput($_POST['email'] ?? '');
        
        if (empty($email)) {
            throw new Exception('Please enter your email address');
        }
        
        // Additional rate limiting per email
        checkRateLimit($email, 'password_reset_email', 2, 60);
        
        $user->requestPasswordReset($email);
        
        $success = "If an account with that email address exists, we've sent you a password reset link. Please check your email.";
        
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
    <title>Forgot Password - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="assets/css/club-dadvice.css">
    <style>
        .auth-container {
            max-width: 400px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .auth-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }
        
        .form-group input {
            padding: 0.75rem;
            border: 2px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #007cba;
        }
        
        .error-message {
            background: #fee;
            color: #c33;
            padding: 1rem;
            border-radius: 4px;
            border-left: 4px solid #c33;
            margin-bottom: 1rem;
        }
        
        .success-message {
            background: #efe;
            color: #363;
            padding: 1rem;
            border-radius: 4px;
            border-left: 4px solid #363;
            margin-bottom: 1rem;
        }
        
        .auth-links {
            text-align: center;
            margin-top: 1rem;
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
        
        .help-text {
            color: #666;
            font-size: 0.875rem;
            margin-bottom: 1rem;
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
            <h1>Reset Your Password</h1>
            <p class="help-text">Enter your email address and we'll send you a link to reset your password.</p>
            
            <?php if ($error): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success-message"><?= htmlspecialchars($success) ?></div>
            <?php else: ?>
                <form method="POST" class="auth-form">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" 
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" 
                               required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Send Reset Link</button>
                </form>
            <?php endif; ?>
            
            <div class="auth-links">
                <p><a href="login.php">← Back to Sign In</a></p>
                <p>Don't have an account? <a href="register.php">Create one here</a></p>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>
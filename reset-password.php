<?php
require_once 'config.php';
require_once 'classes/User.php';

$user = new User();
$error = '';
$success = '';
$token = $_GET['token'] ?? '';

// Redirect if already logged in
if ($user->isLoggedIn()) {
    header('Location: profile.php');
    exit;
}

if (empty($token)) {
    $error = 'Invalid reset link. Please request a new password reset.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($token)) {
    try {
        // CSRF Protection
        requireCSRFToken();
        
        // Rate limiting - 5 password reset attempts per 15 minutes per IP
        $client_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        checkRateLimit($client_ip, 'password_reset_submit', 5, 15);
        
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if (empty($password) || empty($confirm_password)) {
            throw new Exception('Please fill in all fields');
        }
        
        if ($password !== $confirm_password) {
            throw new Exception('Passwords do not match');
        }
        
        $user->resetPassword($token, $password);
        
        $success = "Your password has been reset successfully. You can now sign in with your new password.";
        
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
    <title>Reset Password - <?= SITE_NAME ?></title>
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
        
        .password-requirements {
            font-size: 0.875rem;
            color: #666;
            margin-top: 0.25rem;
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
            <h1>Set New Password</h1>
            
            <?php if ($error): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success-message"><?= htmlspecialchars($success) ?></div>
                <div class="auth-links">
                    <p><a href="login.php">Sign In Now</a></p>
                </div>
            <?php elseif (!empty($token)): ?>
                <p>Enter your new password below.</p>
                
                <form method="POST" class="auth-form">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" id="password" name="password" required>
                        <div class="password-requirements">
                            Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, and one number.
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </form>
            <?php else: ?>
                <div class="auth-links">
                    <p><a href="forgot-password.php">Request a new password reset</a></p>
                    <p><a href="login.php">← Back to Sign In</a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>
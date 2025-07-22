<?php
require_once 'config.php';
require_once 'classes/User.php';

$user = new User();
$error = '';

// Redirect if already logged in
if ($user->isLoggedIn()) {
    header('Location: profile.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // CSRF Protection
        requireCSRFToken();
        
        // Rate limiting - 5 attempts per 15 minutes per IP
        $client_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        checkRateLimit($client_ip, 'login', 5, 15);
        
        $email = sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember_me = isset($_POST['remember_me']);
        
        if (empty($email) || empty($password)) {
            throw new Exception('Please enter both email and password');
        }
        
        // Additional rate limiting per email
        checkRateLimit($email, 'login_email', 3, 15);
        
        $result = $user->login($email, $password, $remember_me);
        
        // Clear rate limits on successful login
        clearRateLimit($client_ip, 'login');
        clearRateLimit($email, 'login_email');
        
        // Redirect to intended page or profile
        $redirect = $_GET['redirect'] ?? 'profile.php';
        header('Location: ' . $redirect);
        exit;
        
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
    <title>Login - <?= SITE_NAME ?></title>
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
        
        .form-group-checkbox {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-group-checkbox input[type="checkbox"] {
            width: auto;
        }
        
        .error-message {
            background: #fee;
            color: #c33;
            padding: 1rem;
            border-radius: 4px;
            border-left: 4px solid #c33;
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
        
        .forgot-password {
            text-align: right;
            margin-top: 0.5rem;
        }
        
        .forgot-password a {
            color: #666;
            font-size: 0.875rem;
            text-decoration: none;
        }
        
        .forgot-password a:hover {
            color: #007cba;
            text-decoration: underline;
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
            <h1>Welcome Back</h1>
            <p>Sign in to access your personalised parenting advice and saved articles.</p>
            
            <?php if ($error): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" 
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <div class="forgot-password">
                        <a href="forgot-password.php">Forgot your password?</a>
                    </div>
                </div>
                
                <div class="form-group-checkbox">
                    <input type="checkbox" id="remember_me" name="remember_me">
                    <label for="remember_me">Remember me for 30 days</label>
                </div>
                
                <button type="submit" class="btn btn-primary">Sign In</button>
            </form>
            
            <div class="auth-links">
                <p>Don't have an account? <a href="register.php">Create one here</a></p>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>
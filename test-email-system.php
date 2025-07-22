<?php
/**
 * Email System Test and Configuration Utility
 * This file helps test and configure the email system
 */

require_once 'config.php';
require_once 'classes/EmailService.php';

$test_results = [];
$error = '';
$success = '';

// Handle test email sending
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        requireCSRFToken();
        
        $test_email = sanitizeInput($_POST['test_email'] ?? '');
        $test_type = sanitizeInput($_POST['test_type'] ?? '');
        
        if (empty($test_email)) {
            throw new Exception('Please enter a test email address');
        }
        
        $emailService = new EmailService();
        
        switch ($test_type) {
            case 'verification':
                $result = $emailService->sendVerificationEmail($test_email, 'test-token-123', 'Test User');
                $success = "Verification email sent to {$test_email}";
                break;
                
            case 'password_reset':
                $result = $emailService->sendPasswordResetEmail($test_email, 'test-token-456', 'Test User');
                $success = "Password reset email sent to {$test_email}";
                break;
                
            case 'welcome':
                $result = $emailService->sendWelcomeEmail($test_email, 'Test User');
                $success = "Welcome email sent to {$test_email}";
                break;
                
            default:
                throw new Exception('Invalid test type');
        }
        
        if (!$result) {
            throw new Exception('Email sending failed');
        }
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get email configuration status
$emailService = new EmailService();
$config_status = $emailService->testConfiguration();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email System Test - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="assets/css/club-dadvice.css">
    <style>
        .test-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
        }
        
        .test-section {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .test-section h2 {
            margin-top: 0;
            color: #333;
            border-bottom: 2px solid #007cba;
            padding-bottom: 0.5rem;
        }
        
        .status-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .status-item {
            padding: 1rem;
            border-radius: 4px;
            border-left: 4px solid #ddd;
        }
        
        .status-success {
            background: #efe;
            border-left-color: #4CAF50;
        }
        
        .status-warning {
            background: #fff3cd;
            border-left-color: #ffc107;
        }
        
        .status-error {
            background: #fee;
            border-left-color: #dc3545;
        }
        
        .config-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }
        
        .config-table th,
        .config-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .config-table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        
        .test-form {
            display: grid;
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
        
        .form-group input,
        .form-group select {
            padding: 0.75rem;
            border: 2px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .form-group input:focus,
        .form-group select:focus {
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
        
        .setup-guide {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 4px;
            margin-bottom: 2rem;
        }
        
        .setup-guide h3 {
            margin-top: 0;
            color: #007cba;
        }
        
        .setup-guide code {
            background: #e9ecef;
            padding: 0.25rem 0.5rem;
            border-radius: 3px;
            font-family: monospace;
        }
        
        .setup-guide pre {
            background: #e9ecef;
            padding: 1rem;
            border-radius: 4px;
            overflow-x: auto;
        }
        
        @media (max-width: 768px) {
            .test-container {
                margin: 1rem;
                padding: 1rem;
            }
            
            .status-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <div class="test-container">
            <h1>📧 Email System Test & Configuration</h1>
            
            <!-- Configuration Status -->
            <div class="test-section">
                <h2>Current Configuration Status</h2>
                
                <div class="status-grid">
                    <div class="status-item <?= EMAIL_USE_SMTP ? 'status-success' : 'status-warning' ?>">
                        <strong>SMTP Status:</strong><br>
                        <?= EMAIL_USE_SMTP ? '✓ Enabled' : '⚠️ Disabled (using mail())' ?>
                    </div>
                    
                    <div class="status-item <?= !empty(EMAIL_SMTP_HOST) ? 'status-success' : 'status-error' ?>">
                        <strong>SMTP Host:</strong><br>
                        <?= !empty(EMAIL_SMTP_HOST) ? '✓ Configured' : '✗ Not configured' ?>
                    </div>
                    
                    <div class="status-item <?= !empty(EMAIL_SMTP_USERNAME) ? 'status-success' : 'status-error' ?>">
                        <strong>SMTP Username:</strong><br>
                        <?= !empty(EMAIL_SMTP_USERNAME) ? '✓ Configured' : '✗ Not configured' ?>
                    </div>
                    
                    <div class="status-item <?= !empty(EMAIL_SMTP_PASSWORD) ? 'status-success' : 'status-error' ?>">
                        <strong>SMTP Password:</strong><br>
                        <?= !empty(EMAIL_SMTP_PASSWORD) ? '✓ Configured' : '✗ Not configured' ?>
                    </div>
                </div>
                
                <table class="config-table">
                    <thead>
                        <tr>
                            <th>Setting</th>
                            <th>Current Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>SMTP Host</td>
                            <td><?= htmlspecialchars(EMAIL_SMTP_HOST) ?></td>
                        </tr>
                        <tr>
                            <td>SMTP Port</td>
                            <td><?= EMAIL_SMTP_PORT ?></td>
                        </tr>
                        <tr>
                            <td>SMTP Security</td>
                            <td><?= htmlspecialchars(EMAIL_SMTP_SECURE) ?></td>
                        </tr>
                        <tr>
                            <td>From Email</td>
                            <td><?= htmlspecialchars(EMAIL_FROM_ADDRESS) ?></td>
                        </tr>
                        <tr>
                            <td>From Name</td>
                            <td><?= htmlspecialchars(EMAIL_FROM_NAME) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Setup Guide -->
            <div class="test-section">
                <h2>📋 Email Setup Guide</h2>
                
                <div class="setup-guide">
                    <h3>For Hostinger Users:</h3>
                    <p>To configure SMTP with Hostinger, update these values in your <code>config.php</code>:</p>
                    <pre>
// Email configuration
define('EMAIL_USE_SMTP', true);
define('EMAIL_SMTP_HOST', 'smtp.hostinger.com');
define('EMAIL_SMTP_PORT', 587);
define('EMAIL_SMTP_SECURE', 'tls');
define('EMAIL_SMTP_USERNAME', 'your-email@yourdomain.com');
define('EMAIL_SMTP_PASSWORD', 'your-email-password');
define('EMAIL_FROM_ADDRESS', 'noreply@yourdomain.com');
define('EMAIL_FROM_NAME', 'Club Dadvice');
                    </pre>
                    
                    <h3>For Gmail SMTP:</h3>
                    <pre>
define('EMAIL_SMTP_HOST', 'smtp.gmail.com');
define('EMAIL_SMTP_PORT', 587);
define('EMAIL_SMTP_SECURE', 'tls');
define('EMAIL_SMTP_USERNAME', 'your-gmail@gmail.com');
define('EMAIL_SMTP_PASSWORD', 'your-app-password'); // Use App Password, not regular password
                    </pre>
                    
                    <h3>For Other Providers:</h3>
                    <ul>
                        <li><strong>Outlook/Hotmail:</strong> smtp-mail.outlook.com:587 (TLS)</li>
                        <li><strong>Yahoo:</strong> smtp.mail.yahoo.com:587 (TLS)</li>
                        <li><strong>SendGrid:</strong> smtp.sendgrid.net:587 (TLS)</li>
                        <li><strong>Mailgun:</strong> smtp.mailgun.org:587 (TLS)</li>
                    </ul>
                </div>
            </div>
            
            <!-- Test Email Sending -->
            <div class="test-section">
                <h2>🧪 Test Email Sending</h2>
                
                <?php if ($error): ?>
                    <div class="error-message"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="success-message"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>
                
                <form method="POST" class="test-form">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    
                    <div class="form-group">
                        <label for="test_email">Test Email Address</label>
                        <input type="email" id="test_email" name="test_email" 
                               value="<?= htmlspecialchars($_POST['test_email'] ?? '') ?>" 
                               required placeholder="Enter your email to receive test">
                    </div>
                    
                    <div class="form-group">
                        <label for="test_type">Email Type to Test</label>
                        <select id="test_type" name="test_type" required>
                            <option value="">Select email type...</option>
                            <option value="verification" <?= ($_POST['test_type'] ?? '') === 'verification' ? 'selected' : '' ?>>
                                Email Verification
                            </option>
                            <option value="password_reset" <?= ($_POST['test_type'] ?? '') === 'password_reset' ? 'selected' : '' ?>>
                                Password Reset
                            </option>
                            <option value="welcome" <?= ($_POST['test_type'] ?? '') === 'welcome' ? 'selected' : '' ?>>
                                Welcome Email
                            </option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Send Test Email</button>
                </form>
                
                <div class="setup-guide">
                    <h3>⚠️ Important Notes:</h3>
                    <ul>
                        <li>Test emails contain dummy tokens and links that won't work</li>
                        <li>Check your spam folder if you don't receive the test email</li>
                        <li>If SMTP is not configured, emails will use PHP's mail() function</li>
                        <li>For production use, always configure proper SMTP settings</li>
                    </ul>
                </div>
            </div>
            
            <!-- Security Features -->
            <div class="test-section">
                <h2>🔒 Security Features Implemented</h2>
                
                <div class="status-grid">
                    <div class="status-item status-success">
                        <strong>CSRF Protection:</strong><br>
                        ✓ All forms protected with CSRF tokens
                    </div>
                    
                    <div class="status-item status-success">
                        <strong>Rate Limiting:</strong><br>
                        ✓ Login, registration, and email requests limited
                    </div>
                    
                    <div class="status-item status-success">
                        <strong>Email Security:</strong><br>
                        ✓ Enhanced email service with fallback options
                    </div>
                    
                    <div class="status-item status-success">
                        <strong>Input Validation:</strong><br>
                        ✓ All inputs sanitized and validated
                    </div>
                </div>
                
                <h3>Rate Limiting Rules:</h3>
                <ul>
                    <li><strong>Login:</strong> 5 attempts per 15 minutes per IP, 3 per email</li>
                    <li><strong>Registration:</strong> 3 registrations per 60 minutes per IP</li>
                    <li><strong>Password Reset:</strong> 3 requests per 60 minutes per IP, 2 per email</li>
                    <li><strong>Profile Updates:</strong> 10 updates per 60 minutes per user</li>
                    <li><strong>Email Changes:</strong> 3 changes per 24 hours per user</li>
                </ul>
            </div>
            
            <div class="test-section">
                <p><a href="test-auth-system.php" class="btn btn-secondary">← Back to Auth System Test</a></p>
                <p><a href="index.php" class="btn btn-primary">← Back to Home</a></p>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>
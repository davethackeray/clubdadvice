<?php
/**
 * Email Service Class
 * Handles all email functionality with SMTP support and fallback options
 */

class EmailService {
    private $smtp_host;
    private $smtp_port;
    private $smtp_username;
    private $smtp_password;
    private $smtp_secure;
    private $from_email;
    private $from_name;
    private $use_smtp;
    
    public function __construct() {
        // Load email configuration
        $this->smtp_host = EMAIL_SMTP_HOST ?? 'localhost';
        $this->smtp_port = EMAIL_SMTP_PORT ?? 587;
        $this->smtp_username = EMAIL_SMTP_USERNAME ?? '';
        $this->smtp_password = EMAIL_SMTP_PASSWORD ?? '';
        $this->smtp_secure = EMAIL_SMTP_SECURE ?? 'tls';
        $this->from_email = EMAIL_FROM_ADDRESS ?? 'noreply@clubdadvice.com';
        $this->from_name = EMAIL_FROM_NAME ?? 'Club Dadvice';
        $this->use_smtp = EMAIL_USE_SMTP ?? false;
    }
    
    /**
     * Send email with multiple delivery methods
     */
    public function sendEmail($to, $subject, $html_body, $text_body = null) {
        // Generate text version if not provided
        if (!$text_body) {
            $text_body = $this->htmlToText($html_body);
        }
        
        // In local development mode, log emails instead of sending
        if (defined('LOCAL_DEV_MODE') && LOCAL_DEV_MODE) {
            return $this->logEmailForDevelopment($to, $subject, $html_body, $text_body);
        }
        
        // Try SMTP first if configured
        if ($this->use_smtp && $this->isSmtpConfigured()) {
            try {
                return $this->sendViaSMTP($to, $subject, $html_body, $text_body);
            } catch (Exception $e) {
                error_log("SMTP send failed: " . $e->getMessage());
                // Fall back to mail() function
            }
        }
        
        // Fallback to PHP mail() function
        return $this->sendViaMail($to, $subject, $html_body, $text_body);
    }
    
    /**
     * Send email via SMTP (using PHPMailer-like functionality)
     */
    private function sendViaSMTP($to, $subject, $html_body, $text_body) {
        // This is a simplified SMTP implementation
        // In production, you'd use PHPMailer or similar library
        
        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: multipart/alternative; boundary="boundary-' . uniqid() . '"',
            'From: ' . $this->from_name . ' <' . $this->from_email . '>',
            'Reply-To: ' . $this->from_email,
            'X-Mailer: Club Dadvice Email Service',
            'X-Priority: 3'
        ];
        
        $boundary = 'boundary-' . uniqid();
        
        $message = "--{$boundary}\r\n";
        $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $message .= $text_body . "\r\n\r\n";
        
        $message .= "--{$boundary}\r\n";
        $message .= "Content-Type: text/html; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $message .= $html_body . "\r\n\r\n";
        
        $message .= "--{$boundary}--";
        
        // For now, we'll use mail() with proper headers
        // In production, implement actual SMTP connection
        return mail($to, $subject, $message, implode("\r\n", $headers));
    }
    
    /**
     * Send email via PHP mail() function with enhanced headers
     */
    private function sendViaMail($to, $subject, $html_body, $text_body) {
        $boundary = 'boundary-' . uniqid();
        
        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
            'From: ' . $this->from_name . ' <' . $this->from_email . '>',
            'Reply-To: ' . $this->from_email,
            'X-Mailer: Club Dadvice Email Service',
            'X-Priority: 3',
            'Return-Path: ' . $this->from_email
        ];
        
        $message = "--{$boundary}\r\n";
        $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $message .= $text_body . "\r\n\r\n";
        
        $message .= "--{$boundary}\r\n";
        $message .= "Content-Type: text/html; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $message .= $html_body . "\r\n\r\n";
        
        $message .= "--{$boundary}--";
        
        return mail($to, $subject, $message, implode("\r\n", $headers));
    }
    
    /**
     * Send verification email
     */
    public function sendVerificationEmail($email, $token, $first_name) {
        $verification_url = BASE_URL . "/verify-email.php?token=" . $token;
        
        $subject = "Verify your Club Dadvice account";
        
        $html_body = $this->getEmailTemplate('verification', [
            'first_name' => $first_name,
            'verification_url' => $verification_url,
            'site_name' => SITE_NAME
        ]);
        
        return $this->sendEmail($email, $subject, $html_body);
    }
    
    /**
     * Send password reset email
     */
    public function sendPasswordResetEmail($email, $token, $first_name) {
        $reset_url = BASE_URL . "/reset-password.php?token=" . $token;
        
        $subject = "Reset your Club Dadvice password";
        
        $html_body = $this->getEmailTemplate('password_reset', [
            'first_name' => $first_name,
            'reset_url' => $reset_url,
            'site_name' => SITE_NAME
        ]);
        
        return $this->sendEmail($email, $subject, $html_body);
    }
    
    /**
     * Send welcome email after verification
     */
    public function sendWelcomeEmail($email, $first_name) {
        $subject = "Welcome to Club Dadvice!";
        
        $html_body = $this->getEmailTemplate('welcome', [
            'first_name' => $first_name,
            'site_name' => SITE_NAME,
            'login_url' => BASE_URL . '/login.php'
        ]);
        
        return $this->sendEmail($email, $subject, $html_body);
    }
    
    /**
     * Get email template
     */
    private function getEmailTemplate($template, $variables = []) {
        $template_file = "templates/email/{$template}.html";
        
        if (file_exists($template_file)) {
            $content = file_get_contents($template_file);
            
            // Replace variables
            foreach ($variables as $key => $value) {
                $content = str_replace('{{' . $key . '}}', htmlspecialchars($value), $content);
            }
            
            return $content;
        }
        
        // Fallback to inline templates
        return $this->getInlineTemplate($template, $variables);
    }
    
    /**
     * Get inline email templates as fallback
     */
    private function getInlineTemplate($template, $variables) {
        $base_style = $this->getEmailBaseStyle();
        
        switch ($template) {
            case 'verification':
                return $base_style . "
                <div class='email-container'>
                    <div class='email-header'>
                        <h1>Welcome to {$variables['site_name']}!</h1>
                    </div>
                    <div class='email-content'>
                        <p>Hi {$variables['first_name']},</p>
                        <p>Thanks for joining our community of dads who are committed to raising world-class kids.</p>
                        <p>Please click the button below to verify your email address:</p>
                        <div class='email-button'>
                            <a href='{$variables['verification_url']}' class='btn'>Verify Email Address</a>
                        </div>
                        <p>If the button doesn't work, copy and paste this link into your browser:</p>
                        <p class='link'>{$variables['verification_url']}</p>
                        <p><small>This link will expire in 24 hours.</small></p>
                    </div>
                    <div class='email-footer'>
                        <p>Cheers,<br>The Club Dadvice Team</p>
                    </div>
                </div>";
                
            case 'password_reset':
                return $base_style . "
                <div class='email-container'>
                    <div class='email-header'>
                        <h1>Password Reset Request</h1>
                    </div>
                    <div class='email-content'>
                        <p>Hi {$variables['first_name']},</p>
                        <p>We received a request to reset your Club Dadvice password.</p>
                        <p>Click the button below to reset your password:</p>
                        <div class='email-button'>
                            <a href='{$variables['reset_url']}' class='btn'>Reset Password</a>
                        </div>
                        <p>If the button doesn't work, copy and paste this link into your browser:</p>
                        <p class='link'>{$variables['reset_url']}</p>
                        <p><small>This link will expire in 1 hour.</small></p>
                        <p>If you didn't request this password reset, please ignore this email.</p>
                    </div>
                    <div class='email-footer'>
                        <p>Cheers,<br>The Club Dadvice Team</p>
                    </div>
                </div>";
                
            case 'welcome':
                return $base_style . "
                <div class='email-container'>
                    <div class='email-header'>
                        <h1>Welcome to {$variables['site_name']}!</h1>
                    </div>
                    <div class='email-content'>
                        <p>Hi {$variables['first_name']},</p>
                        <p>Your email has been verified and your account is now active!</p>
                        <p>You can now access all the features of Club Dadvice:</p>
                        <ul>
                            <li>Personalized parenting advice</li>
                            <li>Bookmark your favorite articles</li>
                            <li>Connect with other dads</li>
                            <li>Weekly newsletter with curated content</li>
                        </ul>
                        <div class='email-button'>
                            <a href='{$variables['login_url']}' class='btn'>Start Exploring</a>
                        </div>
                    </div>
                    <div class='email-footer'>
                        <p>Cheers,<br>The Club Dadvice Team</p>
                    </div>
                </div>";
                
            default:
                return "<p>Email template not found.</p>";
        }
    }
    
    /**
     * Get base email styling
     */
    private function getEmailBaseStyle() {
        return "
        <style>
            .email-container {
                max-width: 600px;
                margin: 0 auto;
                font-family: Arial, sans-serif;
                line-height: 1.6;
                color: #333;
            }
            .email-header {
                background: #007cba;
                color: white;
                padding: 2rem;
                text-align: center;
                border-radius: 8px 8px 0 0;
            }
            .email-header h1 {
                margin: 0;
                font-size: 1.5rem;
            }
            .email-content {
                background: white;
                padding: 2rem;
                border: 1px solid #ddd;
            }
            .email-footer {
                background: #f8f9fa;
                padding: 1rem 2rem;
                border-radius: 0 0 8px 8px;
                border: 1px solid #ddd;
                border-top: none;
                text-align: center;
                color: #666;
            }
            .email-button {
                text-align: center;
                margin: 2rem 0;
            }
            .btn {
                display: inline-block;
                background: #007cba;
                color: white;
                padding: 12px 24px;
                text-decoration: none;
                border-radius: 5px;
                font-weight: bold;
            }
            .link {
                word-break: break-all;
                color: #007cba;
                font-size: 0.9rem;
            }
            ul {
                padding-left: 1.5rem;
            }
            li {
                margin-bottom: 0.5rem;
            }
        </style>";
    }
    
    /**
     * Convert HTML to plain text
     */
    private function htmlToText($html) {
        // Remove HTML tags and convert to plain text
        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }
    
    /**
     * Check if SMTP is properly configured
     */
    private function isSmtpConfigured() {
        return !empty($this->smtp_host) && 
               !empty($this->smtp_username) && 
               !empty($this->smtp_password);
    }
    
    /**
     * Log email for development instead of sending
     */
    private function logEmailForDevelopment($to, $subject, $html_body, $text_body) {
        $log_entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'to' => $to,
            'subject' => $subject,
            'html_body' => $html_body,
            'text_body' => $text_body
        ];
        
        // Ensure logs directory exists
        if (!is_dir('logs')) {
            mkdir('logs', 0755, true);
        }
        
        // Log to file
        $log_file = 'logs/emails_' . date('Y-m-d') . '.log';
        $log_message = "\n" . str_repeat('=', 80) . "\n";
        $log_message .= "EMAIL LOG - " . $log_entry['timestamp'] . "\n";
        $log_message .= "To: " . $log_entry['to'] . "\n";
        $log_message .= "Subject: " . $log_entry['subject'] . "\n";
        $log_message .= "Content:\n" . $log_entry['text_body'] . "\n";
        $log_message .= "Verification URL: ";
        
        // Extract verification URL from HTML if present
        if (preg_match('/href=[\'"]([^\'"]*(verify-email|reset-password)[^\'"]*)[\'"]/', $html_body, $matches)) {
            $log_message .= $matches[1] . "\n";
        } else {
            $log_message .= "No verification URL found\n";
        }
        
        $log_message .= str_repeat('=', 80) . "\n";
        
        file_put_contents($log_file, $log_message, FILE_APPEND | LOCK_EX);
        
        // Also log to PHP error log for immediate visibility
        error_log("DEV EMAIL: To={$to}, Subject={$subject}");
        
        return true; // Always return success in development mode
    }

    /**
     * Test email configuration
     */
    public function testConfiguration() {
        $test_email = 'test@example.com';
        $subject = 'Email Configuration Test';
        $body = '<p>This is a test email to verify email configuration.</p>';
        
        try {
            // Don't actually send, just test the setup
            return [
                'smtp_configured' => $this->isSmtpConfigured(),
                'smtp_host' => $this->smtp_host,
                'smtp_port' => $this->smtp_port,
                'from_email' => $this->from_email,
                'from_name' => $this->from_name,
                'local_dev_mode' => defined('LOCAL_DEV_MODE') && LOCAL_DEV_MODE
            ];
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage()
            ];
        }
    }
}
?>
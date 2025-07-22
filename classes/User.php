<?php
/**
 * User Authentication and Management Class
 * Handles user registration, login, password reset, and profile management
 */

class User {
    private $pdo;
    
    public function __construct() {
        $this->pdo = getDBConnection();
    }
    
    /**
     * Register a new user with email validation
     */
    public function register($email, $password, $first_name, $last_name = '') {
        // Validate input
        if (!$this->isValidEmail($email)) {
            throw new Exception('Invalid email address');
        }
        
        if (!$this->isValidPassword($password)) {
            throw new Exception('Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, and one number');
        }
        
        // Check if email already exists
        if ($this->emailExists($email)) {
            throw new Exception('Email address already registered');
        }
        
        // Hash password securely
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Generate email verification token
        $verification_token = bin2hex(random_bytes(32));
        
        // Create display name
        $display_name = trim($first_name . ' ' . $last_name);
        if (empty($display_name)) {
            $display_name = explode('@', $email)[0];
        }
        
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO users (email, password_hash, first_name, last_name, display_name, email_verification_token) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $email,
                $password_hash,
                $first_name,
                $last_name,
                $display_name,
                $verification_token
            ]);
            
            $user_id = $this->pdo->lastInsertId();
            
            // Send verification email
            $this->sendVerificationEmail($email, $verification_token, $first_name);
            
            return [
                'user_id' => $user_id,
                'email' => $email,
                'verification_token' => $verification_token
            ];
            
        } catch (PDOException $e) {
            throw new Exception('Registration failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Authenticate user login
     */
    public function login($email, $password, $remember_me = false) {
        if (!$this->isValidEmail($email)) {
            throw new Exception('Invalid email address');
        }
        
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, email, password_hash, first_name, last_name, display_name, 
                       email_verified, is_active, login_count
                FROM users 
                WHERE email = ? AND is_active = 1
            ");
            
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if (!$user || !password_verify($password, $user['password_hash'])) {
                throw new Exception('Invalid email or password');
            }
            
            // Check if email is verified
            if (!$user['email_verified']) {
                throw new Exception('Please verify your email address before logging in');
            }
            
            // Update login statistics
            $this->updateLoginStats($user['id']);
            
            // Start session
            $this->startUserSession($user, $remember_me);
            
            return [
                'user_id' => $user['id'],
                'email' => $user['email'],
                'display_name' => $user['display_name'],
                'first_name' => $user['first_name']
            ];
            
        } catch (PDOException $e) {
            throw new Exception('Login failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Logout user
     */
    public function logout() {
        // Clear session
        session_start();
        session_unset();
        session_destroy();
        
        // Clear remember me cookie if it exists
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }
    }
    
    /**
     * Request password reset
     */
    public function requestPasswordReset($email) {
        if (!$this->isValidEmail($email)) {
            throw new Exception('Invalid email address');
        }
        
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, first_name FROM users 
                WHERE email = ? AND is_active = 1
            ");
            
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if (!$user) {
                // Don't reveal if email exists or not for security
                return true;
            }
            
            // Generate reset token
            $reset_token = bin2hex(random_bytes(32));
            $reset_expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Save reset token
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET password_reset_token = ?, password_reset_expires = ? 
                WHERE id = ?
            ");
            
            $stmt->execute([$reset_token, $reset_expires, $user['id']]);
            
            // Send reset email
            $this->sendPasswordResetEmail($email, $reset_token, $user['first_name']);
            
            return true;
            
        } catch (PDOException $e) {
            throw new Exception('Password reset request failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Reset password with token
     */
    public function resetPassword($token, $new_password) {
        if (!$this->isValidPassword($new_password)) {
            throw new Exception('Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, and one number');
        }
        
        try {
            $stmt = $this->pdo->prepare("
                SELECT id FROM users 
                WHERE password_reset_token = ? 
                AND password_reset_expires > NOW() 
                AND is_active = 1
            ");
            
            $stmt->execute([$token]);
            $user = $stmt->fetch();
            
            if (!$user) {
                throw new Exception('Invalid or expired reset token');
            }
            
            // Hash new password
            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Update password and clear reset token
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET password_hash = ?, password_reset_token = NULL, password_reset_expires = NULL 
                WHERE id = ?
            ");
            
            $stmt->execute([$password_hash, $user['id']]);
            
            return true;
            
        } catch (PDOException $e) {
            throw new Exception('Password reset failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Verify email address
     */
    public function verifyEmail($token) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, email, first_name FROM users 
                WHERE email_verification_token = ? AND is_active = 1
            ");
            
            $stmt->execute([$token]);
            $user = $stmt->fetch();
            
            if (!$user) {
                throw new Exception('Invalid verification token');
            }
            
            // Mark email as verified
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET email_verified = 1, email_verification_token = NULL 
                WHERE id = ?
            ");
            
            $stmt->execute([$user['id']]);
            
            // Send welcome email
            $this->sendWelcomeEmail($user['email'], $user['first_name']);
            
            return [
                'user_id' => $user['id'],
                'email' => $user['email'],
                'first_name' => $user['first_name']
            ];
            
        } catch (PDOException $e) {
            throw new Exception('Email verification failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Get user profile
     */
    public function getProfile($user_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, email, first_name, last_name, display_name, location, 
                       timezone, newsletter_frequency, created_at, last_login, login_count
                FROM users 
                WHERE id = ? AND is_active = 1
            ");
            
            $stmt->execute([$user_id]);
            return $stmt->fetch();
            
        } catch (PDOException $e) {
            throw new Exception('Failed to get user profile: ' . $e->getMessage());
        }
    }
    
    /**
     * Update user profile
     */
    public function updateProfile($user_id, $data) {
        $allowed_fields = ['first_name', 'last_name', 'display_name', 'location', 'timezone', 'newsletter_frequency'];
        $update_fields = [];
        $values = [];
        
        foreach ($allowed_fields as $field) {
            if (isset($data[$field])) {
                $update_fields[] = "$field = ?";
                $values[] = $data[$field];
            }
        }
        
        if (empty($update_fields)) {
            throw new Exception('No valid fields to update');
        }
        
        $values[] = $user_id;
        
        try {
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET " . implode(', ', $update_fields) . ", updated_at = NOW() 
                WHERE id = ? AND is_active = 1
            ");
            
            $stmt->execute($values);
            
            return $this->getProfile($user_id);
            
        } catch (PDOException $e) {
            throw new Exception('Profile update failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Update user email (requires verification)
     */
    public function updateEmail($user_id, $new_email) {
        if (!$this->isValidEmail($new_email)) {
            throw new Exception('Invalid email address');
        }
        
        if ($this->emailExists($new_email)) {
            throw new Exception('Email address already in use');
        }
        
        // Generate new verification token
        $verification_token = bin2hex(random_bytes(32));
        
        try {
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET email = ?, email_verified = 0, email_verification_token = ? 
                WHERE id = ? AND is_active = 1
            ");
            
            $stmt->execute([$new_email, $verification_token, $user_id]);
            
            // Get user's first name for email
            $user = $this->getProfile($user_id);
            
            // Send verification email to new address
            $this->sendVerificationEmail($new_email, $verification_token, $user['first_name']);
            
            return true;
            
        } catch (PDOException $e) {
            throw new Exception('Email update failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        session_start();
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Get current logged in user
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return [
            'user_id' => $_SESSION['user_id'],
            'email' => $_SESSION['user_email'],
            'display_name' => $_SESSION['user_display_name'],
            'first_name' => $_SESSION['user_first_name']
        ];
    }
    
    // Private helper methods
    
    private function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    private function isValidPassword($password) {
        // At least 8 characters, one uppercase, one lowercase, one number
        return strlen($password) >= 8 && 
               preg_match('/[A-Z]/', $password) && 
               preg_match('/[a-z]/', $password) && 
               preg_match('/[0-9]/', $password);
    }
    
    private function emailExists($email) {
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() !== false;
    }
    
    private function updateLoginStats($user_id) {
        $stmt = $this->pdo->prepare("
            UPDATE users 
            SET last_login = NOW(), login_count = login_count + 1 
            WHERE id = ?
        ");
        $stmt->execute([$user_id]);
    }
    
    private function startUserSession($user, $remember_me = false) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_display_name'] = $user['display_name'];
        $_SESSION['user_first_name'] = $user['first_name'];
        
        // Set remember me cookie if requested
        if ($remember_me) {
            $remember_token = bin2hex(random_bytes(32));
            setcookie('remember_token', $remember_token, time() + (30 * 24 * 60 * 60), '/'); // 30 days
            
            // Store remember token in database (you might want to create a separate table for this)
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET remember_token = ? 
                WHERE id = ?
            ");
            $stmt->execute([$remember_token, $user['id']]);
        }
    }
    
    private function sendVerificationEmail($email, $token, $first_name) {
        require_once 'EmailService.php';
        $emailService = new EmailService();
        
        try {
            $result = $emailService->sendVerificationEmail($email, $token, $first_name);
            if (!$result) {
                error_log("Failed to send verification email to: " . $email);
            }
            return $result;
        } catch (Exception $e) {
            error_log("Email service error: " . $e->getMessage());
            return false;
        }
    }
    
    private function sendPasswordResetEmail($email, $token, $first_name) {
        require_once 'EmailService.php';
        $emailService = new EmailService();
        
        try {
            $result = $emailService->sendPasswordResetEmail($email, $token, $first_name);
            if (!$result) {
                error_log("Failed to send password reset email to: " . $email);
            }
            return $result;
        } catch (Exception $e) {
            error_log("Email service error: " . $e->getMessage());
            return false;
        }
    }
    
    private function sendWelcomeEmail($email, $first_name) {
        require_once 'EmailService.php';
        $emailService = new EmailService();
        
        try {
            $result = $emailService->sendWelcomeEmail($email, $first_name);
            if (!$result) {
                error_log("Failed to send welcome email to: " . $email);
            }
            return $result;
        } catch (Exception $e) {
            error_log("Email service error: " . $e->getMessage());
            return false;
        }
    }
}
?>
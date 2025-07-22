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
$profile = $user->getProfile($current_user['user_id']);
$error = '';
$success = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['update_email'])) {
    try {
        // CSRF Protection
        requireCSRFToken();
        
        // Rate limiting - 10 profile updates per 60 minutes per user
        checkRateLimit($current_user['user_id'], 'profile_update', 10, 60);
        
        $update_data = [
            'first_name' => sanitizeInput($_POST['first_name'] ?? ''),
            'last_name' => sanitizeInput($_POST['last_name'] ?? ''),
            'display_name' => sanitizeInput($_POST['display_name'] ?? ''),
            'location' => sanitizeInput($_POST['location'] ?? ''),
            'timezone' => sanitizeInput($_POST['timezone'] ?? ''),
            'newsletter_frequency' => sanitizeInput($_POST['newsletter_frequency'] ?? '')
        ];
        
        // Validate required fields
        if (empty($update_data['first_name'])) {
            throw new Exception('First name is required');
        }
        
        if (empty($update_data['display_name'])) {
            $update_data['display_name'] = trim($update_data['first_name'] . ' ' . $update_data['last_name']);
        }
        
        $profile = $user->updateProfile($current_user['user_id'], $update_data);
        
        // Update session data
        $_SESSION['user_display_name'] = $profile['display_name'];
        $_SESSION['user_first_name'] = $profile['first_name'];
        
        $success = 'Profile updated successfully!';
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Handle email update
if (isset($_POST['update_email'])) {
    try {
        // CSRF Protection
        requireCSRFToken();
        
        // Rate limiting - 3 email updates per 24 hours per user
        checkRateLimit($current_user['user_id'], 'email_update', 3, 1440);
        
        $new_email = sanitizeInput($_POST['new_email'] ?? '');
        
        if (empty($new_email)) {
            throw new Exception('Please enter a new email address');
        }
        
        $user->updateEmail($current_user['user_id'], $new_email);
        $success = 'A verification email has been sent to your new email address. Please verify it to complete the change.';
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$timezones = [
    'Europe/London' => 'London (GMT)',
    'Europe/Dublin' => 'Dublin (GMT)',
    'Europe/Paris' => 'Paris (CET)',
    'Europe/Berlin' => 'Berlin (CET)',
    'Europe/Rome' => 'Rome (CET)',
    'Europe/Madrid' => 'Madrid (CET)',
    'America/New_York' => 'New York (EST)',
    'America/Chicago' => 'Chicago (CST)',
    'America/Denver' => 'Denver (MST)',
    'America/Los_Angeles' => 'Los Angeles (PST)',
    'Australia/Sydney' => 'Sydney (AEST)',
    'Australia/Melbourne' => 'Melbourne (AEST)',
    'Asia/Tokyo' => 'Tokyo (JST)',
    'Asia/Singapore' => 'Singapore (SGT)'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="assets/css/club-dadvice.css">
    <style>
        .profile-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
        }
        
        .profile-header {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #007cba;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: bold;
            margin: 0 auto 1rem;
        }
        
        .profile-section {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .profile-section h2 {
            margin-top: 0;
            color: #333;
            border-bottom: 2px solid #007cba;
            padding-bottom: 0.5rem;
        }
        
        .profile-form {
            display: grid;
            gap: 1rem;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
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
            transition: border-color 0.3s;
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
        
        .profile-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #007cba;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }
        
        .email-section {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        
        .email-verified {
            color: #28a745;
            font-weight: 600;
        }
        
        .email-unverified {
            color: #dc3545;
            font-weight: 600;
        }
        
        .logout-section {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid #eee;
        }
        
        @media (max-width: 768px) {
            .profile-container {
                margin: 1rem;
                padding: 1rem;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .profile-stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <div class="profile-container">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-avatar">
                    <?= strtoupper(substr($profile['first_name'], 0, 1)) ?>
                </div>
                <h1><?= htmlspecialchars($profile['display_name']) ?></h1>
                <p><?= htmlspecialchars($profile['email']) ?></p>
                <p>Member since <?= formatDate($profile['created_at']) ?></p>
            </div>
            
            <!-- Profile Stats -->
            <div class="profile-stats">
                <div class="stat-card">
                    <div class="stat-value"><?= $profile['login_count'] ?></div>
                    <div class="stat-label">Total Logins</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?= $profile['last_login'] ? formatDate($profile['last_login']) : 'Never' ?></div>
                    <div class="stat-label">Last Login</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?= ucfirst($profile['newsletter_frequency']) ?></div>
                    <div class="stat-label">Newsletter</div>
                </div>
            </div>
            
            <?php if ($error): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success-message"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            
            <!-- Profile Information -->
            <div class="profile-section">
                <h2>Profile Information</h2>
                <form method="POST" class="profile-form">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name *</label>
                            <input type="text" id="first_name" name="first_name" 
                                   value="<?= htmlspecialchars($profile['first_name']) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" id="last_name" name="last_name" 
                                   value="<?= htmlspecialchars($profile['last_name'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="display_name">Display Name</label>
                        <input type="text" id="display_name" name="display_name" 
                               value="<?= htmlspecialchars($profile['display_name']) ?>">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" id="location" name="location" 
                                   value="<?= htmlspecialchars($profile['location'] ?? '') ?>" 
                                   placeholder="e.g., London, UK">
                        </div>
                        
                        <div class="form-group">
                            <label for="timezone">Timezone</label>
                            <select id="timezone" name="timezone">
                                <?php foreach ($timezones as $value => $label): ?>
                                    <option value="<?= htmlspecialchars($value) ?>" 
                                            <?= $profile['timezone'] === $value ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="newsletter_frequency">Newsletter Frequency</label>
                        <select id="newsletter_frequency" name="newsletter_frequency">
                            <option value="daily" <?= $profile['newsletter_frequency'] === 'daily' ? 'selected' : '' ?>>Daily</option>
                            <option value="weekly" <?= $profile['newsletter_frequency'] === 'weekly' ? 'selected' : '' ?>>Weekly</option>
                            <option value="monthly" <?= $profile['newsletter_frequency'] === 'monthly' ? 'selected' : '' ?>>Monthly</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
            
            <!-- Email Settings -->
            <div class="profile-section">
                <h2>Email Settings</h2>
                
                <div class="email-section">
                    <p><strong>Current Email:</strong> <?= htmlspecialchars($profile['email']) ?></p>
                    <p>
                        <strong>Status:</strong> 
                        <?php if ($profile['email_verified']): ?>
                            <span class="email-verified">✓ Verified</span>
                        <?php else: ?>
                            <span class="email-unverified">✗ Not Verified</span>
                        <?php endif; ?>
                    </p>
                </div>
                
                <form method="POST" class="profile-form">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    
                    <div class="form-group">
                        <label for="new_email">Change Email Address</label>
                        <input type="email" id="new_email" name="new_email" 
                               placeholder="Enter new email address">
                    </div>
                    
                    <button type="submit" name="update_email" class="btn btn-secondary">
                        Update Email Address
                    </button>
                </form>
            </div>
            
            <!-- Account Actions -->
            <div class="profile-section">
                <div class="logout-section">
                    <a href="logout.php" class="btn btn-secondary">Sign Out</a>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>
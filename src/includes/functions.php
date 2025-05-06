<?php
require_once __DIR__ . '/../../config/database.php';

/**
 * Check if a user is logged in
 * @return bool
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Get user data by ID
 * @param int $user_id
 * @return array|false
 */
function get_user($user_id) {
    global $db;
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Login user
 * @param string $email
 * @param string $password
 * @return bool
 */
function login_user($email, $password) {
    global $db;
    $stmt = $db->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            return true;
        }
    }
    return false;
}

/**
 * Register a new user
 */
function register_user($username, $email, $password) {
    global $db;
    
    // Check if username or email already exists
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    
    if ($stmt->rowCount() > 0) {
        return false;
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    return $stmt->execute([$username, $email, $hashed_password]);
}

/**
 * Get user activities
 * @param int $user_id
 * @return array
 */
function get_user_activities($user_id) {
    global $db;
    $stmt = $db->prepare("
        SELECT a.*, c.name as category_name, c.icon 
        FROM activities a 
        LEFT JOIN custom_categories c ON a.activity_type = c.name 
        WHERE a.user_id = ? 
        ORDER BY a.activity_date DESC 
        LIMIT 10
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get user statistics
 * @param int $user_id
 * @return array
 */
function get_user_stats($user_id) {
    global $db;
    $stats = [
        'total_activities' => 0,
        'total_duration' => 0,
        'average_duration' => 0,
        'last_activity' => null
    ];
    
    // Get total activities and duration
    $stmt = $db->prepare("
        SELECT COUNT(*) as total, SUM(duration) as duration, MAX(date) as last_date 
        FROM activities 
        WHERE user_id = ?
    ");
    $stmt->execute([$user_id]);
    
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $stats['total_activities'] = $row['total'];
        $stats['total_duration'] = $row['duration'] ?? 0;
        $stats['last_activity'] = $row['last_date'];
        $stats['average_duration'] = $row['total'] > 0 ? $row['duration'] / $row['total'] : 0;
    }
    
    return $stats;
}

/**
 * Get activity categories
 * @param bool $is_premium
 * @return array
 */
function get_activity_categories($is_premium) {
    global $db;
    $sql = "SELECT * FROM custom_categories";
    if (!$is_premium) {
        $sql .= " WHERE is_default = 1";
    }
    $stmt = $db->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Add new activity
 * @param int $user_id
 * @param int $category_id
 * @param int $duration
 * @param string $date
 * @param string $notes
 * @return bool
 */
function add_activity($user_id, $category_id, $duration, $date, $notes = '') {
    global $db;
    $stmt = $db->prepare("
        INSERT INTO activities (user_id, category_id, duration, date, notes) 
        VALUES (?, ?, ?, ?, ?)
    ");
    return $stmt->execute([$user_id, $category_id, $duration, $date, $notes]);
}

/**
 * Format duration in minutes to hours and minutes
 * @param int $minutes
 * @return string
 */
function format_duration($minutes) {
    $hours = floor($minutes / 60);
    $mins = $minutes % 60;
    
    if ($hours > 0) {
        return sprintf("%du %02dmin", $hours, $mins);
    }
    return sprintf("%dmin", $mins);
}

/**
 * Upgrade user to premium
 * @param int $user_id
 * @return bool
 */
function upgrade_to_premium($user_id) {
    global $db;
    $stmt = $db->prepare("UPDATE users SET is_premium = 1 WHERE id = ?");
    return $stmt->execute([$user_id]);
}

/**
 * Check if user is premium
 * @param int $user_id
 * @return bool
 */
function is_premium_user($user_id) {
    global $db;
    $stmt = $db->prepare("SELECT is_premium FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user && $user['is_premium'];
}

/**
 * Check and award achievements
 * @param int $user_id
 */
function check_achievements($user_id) {
    global $db;
    
    // Get user's activity count
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM activities WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $activity_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Check for achievements
    $stmt = $db->prepare("SELECT * FROM achievements WHERE achievement_condition = 'first_activity' AND threshold <= ?");
    $stmt->execute([$activity_count]);
    
    // Award achievements
    while ($achievement = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $stmt2 = $db->prepare("INSERT IGNORE INTO user_achievements (user_id, achievement_id, achieved_at) VALUES (?, ?, NOW())");
        $stmt2->execute([$user_id, $achievement['id']]);
    }
}

/**
 * Get activity type name in Dutch
 * @param string $type
 * @return string
 */
function get_activity_type_name($type) {
    $types = [
        'running' => 'Hardlopen',
        'cycling' => 'Fietsen',
        'swimming' => 'Zwemmen',
        'gym' => 'Gym',
        'other' => 'Anders'
    ];
    return $types[$type] ?? $type;
}

/**
 * Check if current user is premium
 * @return bool
 */
function is_premium() {
    if (!is_logged_in()) {
        return false;
    }
    return is_premium_user($_SESSION['user_id']);
}
?> 
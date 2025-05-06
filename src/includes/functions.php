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
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Login user
 * @param string $email
 * @param string $password
 * @return bool
 */
function login_user($email, $password) {
    global $conn;
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            return true;
        }
    }
    return false;
}

/**
 * Register new user
 * @param string $username
 * @param string $email
 * @param string $password
 * @return bool
 */
function register_user($username, $email, $password) {
    global $conn;
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashed_password);
    return $stmt->execute();
}

/**
 * Get user activities
 * @param int $user_id
 * @return array
 */
function get_user_activities($user_id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT a.*, c.name, c.icon 
        FROM activities a 
        JOIN custom_categories c ON a.category_id = c.id 
        WHERE a.user_id = ? 
        ORDER BY a.date DESC 
        LIMIT 10
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $activities = [];
    while ($row = $result->fetch_assoc()) {
        $activities[] = $row;
    }
    return $activities;
}

/**
 * Get user statistics
 * @param int $user_id
 * @return array
 */
function get_user_stats($user_id) {
    global $conn;
    $stats = [
        'total_activities' => 0,
        'total_duration' => 0,
        'average_duration' => 0,
        'last_activity' => null
    ];
    
    // Get total activities and duration
    $stmt = $conn->prepare("
        SELECT COUNT(*) as total, SUM(duration) as duration, MAX(date) as last_date 
        FROM activities 
        WHERE user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
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
    global $conn;
    $sql = "SELECT * FROM custom_categories";
    if (!$is_premium) {
        $sql .= " WHERE is_default = 1";
    }
    $result = $conn->query($sql);
    
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    return $categories;
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
    global $conn;
    $stmt = $conn->prepare("
        INSERT INTO activities (user_id, category_id, duration, date, notes) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iiiss", $user_id, $category_id, $duration, $date, $notes);
    return $stmt->execute();
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
    global $conn;
    $stmt = $conn->prepare("UPDATE users SET is_premium = 1 WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    return $stmt->execute();
}

/**
 * Check if user is premium
 * @param int $user_id
 * @return bool
 */
function is_premium_user($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT is_premium FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    return $user && $user['is_premium'];
}

/**
 * Check and award achievements
 * @param int $user_id
 */
function check_achievements($user_id) {
    global $conn;
    
    // Get user's activity count
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM activities WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $activity_count = $result->fetch_assoc()['count'];
    
    // Check for achievements
    $stmt = $conn->prepare("SELECT * FROM achievements WHERE achievement_condition = 'first_activity' AND threshold <= ?");
    $stmt->bind_param("i", $activity_count);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Award achievements
    while ($achievement = $result->fetch_assoc()) {
        $stmt = $conn->prepare("INSERT IGNORE INTO user_achievements (user_id, achievement_id, achieved_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("ii", $user_id, $achievement['id']);
        $stmt->execute();
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
?> 
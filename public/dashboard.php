<?php
session_start();
require_once '../config/database.php';
require_once '../src/includes/functions.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit();
}

$user = get_user($_SESSION['user_id']);
$activities = get_user_activities($_SESSION['user_id']);
$stats = get_user_stats($_SESSION['user_id']);

include '../src/views/header.php';
?>

<div class="dashboard">
    <div class="dashboard-header">
        <h2>Welkom, <?php echo htmlspecialchars($user['username']); ?>!</h2>
        <?php if (!$user['is_premium']): ?>
            <a href="premium.php" class="premium-badge">Upgrade naar Premium</a>
        <?php endif; ?>
    </div>
    
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Totale Activiteiten</h3>
            <p class="stat-value"><?php echo $stats['total_activities']; ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Totale Tijd</h3>
            <p class="stat-value"><?php echo format_duration($stats['total_duration']); ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Gemiddelde Duur</h3>
            <p class="stat-value"><?php echo format_duration($stats['average_duration']); ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Laatste Activiteit</h3>
            <p class="stat-value"><?php echo $stats['last_activity'] ? date('d-m-Y', strtotime($stats['last_activity'])) : 'Geen'; ?></p>
        </div>
    </div>
    
    <div class="activities-section">
        <div class="section-header">
            <h3>Recente Activiteiten</h3>
            <a href="add_activity.php" class="btn">Nieuwe Activiteit</a>
        </div>
        
        <?php if (empty($activities)): ?>
            <p class="no-data">Nog geen activiteiten geregistreerd.</p>
        <?php else: ?>
            <div class="activities-list">
                <?php foreach ($activities as $activity): ?>
                    <div class="activity-card">
                        <div class="activity-icon">
                            <i class="fas fa-<?php echo $activity['icon']; ?>"></i>
                        </div>
                        <div class="activity-details">
                            <h4><?php echo htmlspecialchars($activity['name']); ?></h4>
                            <p class="activity-date"><?php echo date('d-m-Y', strtotime($activity['date'])); ?></p>
                            <p class="activity-duration"><?php echo format_duration($activity['duration']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../src/views/footer.php'; ?> 
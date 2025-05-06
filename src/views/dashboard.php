<?php
$user = get_user($_SESSION['user_id']);
$activities = get_user_activities($_SESSION['user_id']);
$is_premium = is_premium_user($_SESSION['user_id']);
?>

<div class="dashboard">
    <h2>Welkom, <?php echo htmlspecialchars($user['username']); ?>!</h2>
    
    <?php if (!$is_premium): ?>
        <div class="premium-banner">
            <h3>Upgrade naar Premium</h3>
            <p>Krijg toegang tot alle functies en onbeperkte activiteiten!</p>
            <a href="premium.php" class="btn btn-premium">Nu Upgraden</a>
        </div>
    <?php endif; ?>
    
    <div class="dashboard-stats">
        <div class="stat-card">
            <h3>Totaal Activiteiten</h3>
            <p class="stat-number"><?php echo count($activities); ?></p>
        </div>
        <div class="stat-card">
            <h3>Totale Tijd</h3>
            <p class="stat-number">
                <?php
                $total_minutes = array_sum(array_column($activities, 'duration'));
                echo format_duration($total_minutes);
                ?>
            </p>
        </div>
    </div>
    
    <div class="activities-section">
        <h3>Recente Activiteiten</h3>
        <?php if (empty($activities)): ?>
            <p>Nog geen activiteiten geregistreerd.</p>
        <?php else: ?>
            <div class="activity-list">
                <?php foreach ($activities as $activity): ?>
                    <div class="activity-card">
                        <h4><?php echo get_activity_type_name($activity['activity_type']); ?></h4>
                        <div class="meta">
                            <span>Datum: <?php echo date('d-m-Y', strtotime($activity['activity_date'])); ?></span>
                            <span>Duur: <?php echo format_duration($activity['duration']); ?></span>
                        </div>
                        <?php if (!empty($activity['notes'])): ?>
                            <p class="notes"><?php echo htmlspecialchars($activity['notes']); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <a href="add_activity.php" class="btn btn-primary">Nieuwe Activiteit</a>
    </div>
</div> 
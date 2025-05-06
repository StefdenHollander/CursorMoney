<?php
session_start();
require_once '../config/database.php';
require_once '../src/includes/functions.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit();
}

$user = get_user($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (upgrade_to_premium($_SESSION['user_id'])) {
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Er is een fout opgetreden bij het upgraden naar premium";
    }
}

include '../src/views/header.php';
?>

<div class="premium-container">
    <h2>Upgrade naar Premium</h2>
    
    <?php if (isset($error)): ?>
        <div class="error-message">
            <p><?php echo $error; ?></p>
        </div>
    <?php endif; ?>
    
    <div class="premium-features">
        <div class="feature">
            <i class="fas fa-infinity"></i>
            <h3>Onbeperkte Activiteiten</h3>
            <p>Registreer zoveel activiteiten als je wilt</p>
        </div>
        
        <div class="feature">
            <i class="fas fa-chart-line"></i>
            <h3>Uitgebreide Statistieken</h3>
            <p>Gedetailleerde inzichten in je prestaties</p>
        </div>
        
        <div class="feature">
            <i class="fas fa-tags"></i>
            <h3>Aangepaste Categorieën</h3>
            <p>Maak je eigen activiteitencategorieën</p>
        </div>
        
        <div class="feature">
            <i class="fas fa-trophy"></i>
            <h3>Extra Doelen</h3>
            <p>Stel meer persoonlijke doelen in</p>
        </div>
    </div>
    
    <div class="premium-price">
        <h3>Premium Abonnement</h3>
        <p class="price">€4,99<span>/maand</span></p>
        <ul class="price-features">
            <li>Onbeperkte activiteiten</li>
            <li>Uitgebreide statistieken</li>
            <li>Aangepaste categorieën</li>
            <li>Extra doelen</li>
            <li>Prioriteit support</li>
        </ul>
        
        <?php if (!$user['is_premium']): ?>
            <form method="POST" action="premium.php">
                <button type="submit" class="premium-button">Nu Upgraden</button>
            </form>
        <?php else: ?>
            <p class="premium-status">Je hebt al een premium abonnement!</p>
        <?php endif; ?>
    </div>
</div>

<?php include '../src/views/footer.php'; ?> 
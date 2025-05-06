<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (upgrade_to_premium($_SESSION['user_id'])) {
        header('Location: index.php?page=dashboard');
        exit();
    } else {
        $error = "Er is een fout opgetreden bij het upgraden naar premium";
    }
}
?>

<div class="premium-container">
    <h2>Upgrade naar Premium</h2>
    
    <?php if (isset($error)): ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="premium-features">
        <div class="feature">
            <h3>Onbeperkte Activiteiten</h3>
            <p>Voeg zoveel activiteiten toe als je wilt, zonder limiet.</p>
        </div>
        
        <div class="feature">
            <h3>Gedetailleerde Statistieken</h3>
            <p>Bekijk uitgebreide statistieken en grafieken over je prestaties.</p>
        </div>
        
        <div class="feature">
            <h3>Aangepaste Categorieën</h3>
            <p>Maak je eigen activiteitstypen aan voor een persoonlijke ervaring.</p>
        </div>
        
        <div class="feature">
            <h3>Doelen en Uitdagingen</h3>
            <p>Stel doelen in en volg je voortgang met uitdagingen.</p>
        </div>
    </div>
    
    <div class="premium-pricing">
        <h3>Premium Abonnement</h3>
        <p class="price">€4,99 per maand</p>
        <ul class="price-features">
            <li>Onbeperkte activiteiten</li>
            <li>Alle premium functies</li>
            <li>Geen advertenties</li>
            <li>Prioriteit support</li>
        </ul>
        
        <form method="POST" action="index.php?page=premium">
            <button type="submit" class="btn btn-premium">Nu Upgraden</button>
        </form>
    </div>
</div> 
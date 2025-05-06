<?php
// Home page content
?>
<div class="hero">
    <h1>Welkom bij SportsTracker</h1>
    <p>Houd je sportactiviteiten bij en bereik je doelen!</p>
    <?php if (!isset($_SESSION['user_id'])): ?>
        <div class="cta-buttons">
            <a href="index.php?page=register" class="btn btn-primary">Registreren</a>
            <a href="index.php?page=login" class="btn btn-secondary">Inloggen</a>
        </div>
    <?php else: ?>
        <div class="cta-buttons">
            <a href="index.php?page=dashboard" class="btn btn-primary">Naar Dashboard</a>
        </div>
    <?php endif; ?>
</div>

<div class="features">
    <div class="feature-card">
        <h3>Activiteiten Bijhouden</h3>
        <p>Registreer al je sportactiviteiten en houd je voortgang bij.</p>
    </div>
    <div class="feature-card">
        <h3>Statistieken</h3>
        <p>Bekijk gedetailleerde statistieken over je prestaties.</p>
    </div>
    <div class="feature-card">
        <h3>Premium Features</h3>
        <p>Upgrade naar premium voor extra functies en onbeperkte activiteiten.</p>
    </div>
</div> 
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sports Tracker</title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <nav>
                <a href="index.php" class="logo"><i class="fas fa-running"></i> Sports Tracker</a>
                <ul class="nav-links">
                    <?php if (is_logged_in()): ?>
                        <li><a href="dashboard.php" class="btn">Dashboard</a></li>
                        <li><a href="add_activity.php" class="btn">Activiteit Toevoegen</a></li>
                        <?php if (!is_premium()): ?>
                            <li><a href="premium.php" class="premium-button">Upgrade naar Premium</a></li>
                        <?php else: ?>
                            <li><span class="premium-badge">Premium</span></li>
                        <?php endif; ?>
                        <li><a href="logout.php" class="btn btn-secondary">Uitloggen</a></li>
                    <?php else: ?>
                        <li><a href="login.php" class="btn btn-primary">Inloggen</a></li>
                        <li><a href="register.php" class="btn btn-primary">Registreren</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container"> 
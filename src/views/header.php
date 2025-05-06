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
                <a href="/" class="logo">Sports Tracker</a>
                <ul class="nav-links">
                    <?php if (is_logged_in()): ?>
                        <li><a href="/dashboard.php">Dashboard</a></li>
                        <li><a href="/add_activity.php">Nieuwe Activiteit</a></li>
                        <?php if (!get_user($_SESSION['user_id'])['is_premium']): ?>
                            <li><a href="/premium.php">Premium</a></li>
                        <?php endif; ?>
                        <li><a href="/logout.php">Uitloggen</a></li>
                    <?php else: ?>
                        <li><a href="/login.php">Inloggen</a></li>
                        <li><a href="/register.php">Registreren</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container"> 
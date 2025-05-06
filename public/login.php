<?php
session_start();
require_once '../config/database.php';
require_once '../src/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (login_user($email, $password)) {
        header('Location: index.php?page=dashboard');
        exit();
    } else {
        $error = "Ongeldige inloggegevens";
    }
}

include '../src/views/header.php';
?>

<div class="form-container">
    <h2>Inloggen</h2>
    <?php if (isset($error)): ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST" action="login.php">
        <div class="form-group">
            <label for="email">E-mailadres</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="password">Wachtwoord</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <button type="submit">Inloggen</button>
    </form>
    
    <p class="form-footer">
        Nog geen account? <a href="register.php">Registreer hier</a>
    </p>
</div>

<?php include '../src/views/footer.php'; ?> 
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    $errors = [];
    
    if (empty($username)) {
        $errors[] = "Gebruikersnaam is verplicht";
    }
    if (empty($email)) {
        $errors[] = "E-mailadres is verplicht";
    }
    if (empty($password)) {
        $errors[] = "Wachtwoord is verplicht";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Wachtwoorden komen niet overeen";
    }
    
    if (empty($errors)) {
        if (register_user($username, $email, $password)) {
            header('Location: index.php?page=login');
            exit();
        } else {
            $errors[] = "Er is een fout opgetreden bij het registreren";
        }
    }
}
?>

<div class="form-container">
    <h2>Registreren</h2>
    <?php if (!empty($errors)): ?>
        <div class="error-message">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="index.php?page=register">
        <div class="form-group">
            <label for="username">Gebruikersnaam</label>
            <input type="text" id="username" name="username" required>
        </div>
        
        <div class="form-group">
            <label for="email">E-mailadres</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="password">Wachtwoord</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Bevestig wachtwoord</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        
        <button type="submit">Registreren</button>
    </form>
    
    <p class="form-footer">
        Heb je al een account? <a href="index.php?page=login">Log hier in</a>
    </p>
</div> 
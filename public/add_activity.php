<?php
session_start();
require_once '../config/database.php';
require_once '../src/includes/functions.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit();
}

$user = get_user($_SESSION['user_id']);
$categories = get_activity_categories($user['is_premium']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = $_POST['category_id'] ?? '';
    $duration = $_POST['duration'] ?? '';
    $date = $_POST['date'] ?? '';
    $notes = $_POST['notes'] ?? '';
    
    $errors = [];
    
    if (empty($category_id)) {
        $errors[] = "Selecteer een activiteit";
    }
    if (empty($duration)) {
        $errors[] = "Vul de duur in";
    }
    if (empty($date)) {
        $errors[] = "Selecteer een datum";
    }
    
    if (empty($errors)) {
        if (add_activity($_SESSION['user_id'], $category_id, $duration, $date, $notes)) {
            header('Location: dashboard.php');
            exit();
        } else {
            $errors[] = "Er is een fout opgetreden bij het toevoegen van de activiteit";
        }
    }
}

include '../src/views/header.php';
?>

<div class="form-container">
    <h2>Nieuwe Activiteit</h2>
    <?php if (!empty($errors)): ?>
        <div class="error-message">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="add_activity.php">
        <div class="form-group">
            <label for="category_id">Activiteit</label>
            <select id="category_id" name="category_id" required>
                <option value="">Selecteer een activiteit</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="duration">Duur (minuten)</label>
            <input type="number" id="duration" name="duration" min="1" required>
        </div>
        
        <div class="form-group">
            <label for="date">Datum</label>
            <input type="date" id="date" name="date" required>
        </div>
        
        <div class="form-group">
            <label for="notes">Notities (optioneel)</label>
            <textarea id="notes" name="notes" rows="3"></textarea>
        </div>
        
        <button type="submit">Toevoegen</button>
    </form>
</div>

<?php include '../src/views/footer.php'; ?> 
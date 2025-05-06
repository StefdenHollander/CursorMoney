<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $activity_type = $_POST['activity_type'] ?? '';
    $duration = $_POST['duration'] ?? '';
    $activity_date = $_POST['activity_date'] ?? '';
    $notes = $_POST['notes'] ?? '';
    
    $errors = [];
    
    if (empty($activity_type)) {
        $errors[] = "Activiteitstype is verplicht";
    }
    if (empty($duration)) {
        $errors[] = "Duur is verplicht";
    }
    if (empty($activity_date)) {
        $errors[] = "Datum is verplicht";
    }
    
    if (empty($errors)) {
        if (add_activity($_SESSION['user_id'], $activity_type, $duration, $activity_date, $notes)) {
            check_achievements($_SESSION['user_id']);
            header('Location: index.php?page=dashboard');
            exit();
        } else {
            $errors[] = "Er is een fout opgetreden bij het toevoegen van de activiteit";
        }
    }
}
?>

<div class="form-container">
    <h2>Nieuwe Activiteit Toevoegen</h2>
    <?php if (!empty($errors)): ?>
        <div class="error-message">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="index.php?page=add_activity">
        <div class="form-group">
            <label for="activity_type">Activiteitstype</label>
            <select id="activity_type" name="activity_type" required>
                <option value="">Selecteer type</option>
                <option value="running">Hardlopen</option>
                <option value="cycling">Fietsen</option>
                <option value="swimming">Zwemmen</option>
                <option value="gym">Gym</option>
                <option value="other">Anders</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="duration">Duur (in minuten)</label>
            <input type="number" id="duration" name="duration" min="1" required>
        </div>
        
        <div class="form-group">
            <label for="activity_date">Datum</label>
            <input type="date" id="activity_date" name="activity_date" required>
        </div>
        
        <div class="form-group">
            <label for="notes">Notities (optioneel)</label>
            <textarea id="notes" name="notes" rows="3"></textarea>
        </div>
        
        <button type="submit">Activiteit Toevoegen</button>
    </form>
</div> 
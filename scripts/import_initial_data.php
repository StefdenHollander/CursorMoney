<?php
require_once '../config/database.php';

// Read the initial data
$initial_data = json_decode(file_get_contents('../data/initial_data.json'), true);

// Import achievements
if (isset($initial_data['achievements'])) {
    $stmt = $conn->prepare("INSERT INTO achievements (name, description, achievement_condition, threshold) VALUES (?, ?, ?, ?)");
    foreach ($initial_data['achievements'] as $achievement) {
        $stmt->execute([
            $achievement['name'],
            $achievement['description'],
            $achievement['condition'],
            $achievement['threshold'] ?? null
        ]);
    }
    echo "Achievements imported successfully.\n";
}

// Import default categories
if (isset($initial_data['default_categories'])) {
    $stmt = $conn->prepare("INSERT INTO custom_categories (name, icon) VALUES (?, ?)");
    foreach ($initial_data['default_categories'] as $category) {
        $stmt->execute([
            $category['name'],
            $category['icon']
        ]);
    }
    echo "Default categories imported successfully.\n";
}

// Import goals
if (isset($initial_data['goals'])) {
    $stmt = $conn->prepare("INSERT INTO goals (name, description, target_activities, target_duration) VALUES (?, ?, ?, ?)");
    foreach ($initial_data['goals'] as $goal) {
        $stmt->execute([
            $goal['name'],
            $goal['description'],
            $goal['target_activities'],
            $goal['target_duration']
        ]);
    }
    echo "Goals imported successfully.\n";
}

echo "All initial data has been imported successfully!";
?> 
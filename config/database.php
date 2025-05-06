<?php
try {
    $db = new PDO(
        "mysql:host=localhost;dbname=sports_tracker;charset=utf8mb4",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?> 
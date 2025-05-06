<?php
session_start();
require_once '../config/database.php';
require_once '../src/includes/functions.php';

// Basic routing
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Header
include '../src/views/header.php';

// Main content
switch ($page) {
    case 'home':
        include '../src/views/home.php';
        break;
    case 'login':
        include '../src/views/login.php';
        break;
    case 'register':
        include '../src/views/register.php';
        break;
    case 'dashboard':
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit();
        }
        include '../src/views/dashboard.php';
        break;
    case 'add_activity':
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit();
        }
        include '../src/views/add_activity.php';
        break;
    case 'premium':
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit();
        }
        include '../src/views/premium.php';
        break;
    default:
        include '../src/views/home.php';
}

// Footer
include '../src/views/footer.php';
?> 
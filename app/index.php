<?php
require_once 'config.php';

// Simple language detection (optional)
// User can access either /login.php (English) or /login_id.php (Indonesian)

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    // Default to Indonesian
    header('Location: login_id.php');
    exit;
}

// Redirect to appropriate dashboard
// Check which language version the user was using
if (isset($_COOKIE['lang']) && $_COOKIE['lang'] === 'en') {
    header('Location: dashboard.php');
} else {
    header('Location: dashboard_id.php');
}
exit;
?>

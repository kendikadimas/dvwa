<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_db'])) {
    // Delete all comments except sample data
    $mysqli->query("DELETE FROM comments WHERE username NOT IN ('admin', 'user', 'test_user')");
    
    // Re-insert sample data to ensure we have clean state
    $mysqli->query("DELETE FROM comments");
    $mysqli->query("INSERT INTO comments (username, email, content, created_at) VALUES 
        ('admin', 'admin@dvwa.local', 'This is a test comment', NOW()),
        ('user', 'user@dvwa.local', 'Another comment for testing', NOW()),
        ('test_user', 'test@dvwa.local', 'Sample data in the database', NOW())");
    
    $_SESSION['reset_message'] = 'Database berhasil direset!';
}

// Redirect back to xss_stored page
$page = $_POST['page'] ?? 'xss_stored_id.php';
header('Location: ' . $page);
exit;
?>

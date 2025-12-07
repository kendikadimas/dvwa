<?php
require_once 'config.php';

// Allow access from login page without authentication
$from_login = isset($_GET['from']) && $_GET['from'] === 'login';

if (!$from_login && !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Handle reset from login page (GET request)
if ($from_login && $_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($mysqli) {
        // Clear all comments
        $mysqli->query("DELETE FROM comments");
        
        // Re-insert sample data
        $mysqli->query("INSERT INTO comments (id, username, content, email, created_at) VALUES 
            (1, 'admin', 'Surat pertama telah diarsipkan', 'admin@dvwa.local', NOW()),
            (2, 'john', 'Surat kedua berhasil diterima', 'john@example.com', NOW()),
            (3, 'jane', 'Arsip dokumen penting', 'jane@example.com', NOW())");
        
        $reset_msg = urlencode('✅ Database comments berhasil direset!');
    } else {
        $reset_msg = urlencode('❌ Error: Database tidak ditemukan');
    }
    
    $back_page = isset($_GET['lang']) && $_GET['lang'] === 'id' ? 'login_id.php' : 'login.php';
    header("Location: $back_page?reset_msg=$reset_msg");
    exit;
}

// Handle reset from XSS page (POST request)
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
    
    // Redirect back to xss_stored page
    $page = $_POST['page'] ?? 'xss_stored_id.php';
    header('Location: ' . $page);
    exit;
}
?>

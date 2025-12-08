<?php
require_once 'config.php';

// Allow access from login page
$from_login = isset($_GET['from']) && $_GET['from'] === 'login';

if (!$from_login && !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$message = '';
$error = '';

// Handle database reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_reset'])) {
    $confirmation = $_POST['confirmation'] ?? '';
    
    if ($confirmation !== 'RESET DATABASE') {
        $error = 'Konfirmasi salah! Ketik: RESET DATABASE';
    } else {
        // Drop all tables
        $tables_dropped = 0;
        $tables_created = 0;
        
        // Get all tables
        $result = $mysqli->query("SHOW TABLES");
        $tables = [];
        
        if ($result) {
            while ($row = $result->fetch_array()) {
                $tables[] = $row[0];
            }
            
            // Drop each table
            foreach ($tables as $table) {
                if ($mysqli->query("DROP TABLE IF EXISTS `$table`")) {
                    $tables_dropped++;
                }
            }
        }
        
        // Recreate tables
        // Users table
        $create_users = "CREATE TABLE IF NOT EXISTS users (
            id INT PRIMARY KEY AUTO_INCREMENT,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        if ($mysqli->query($create_users)) {
            $tables_created++;
            
            // Insert default users
            $default_users = [
                ['admin', md5('admin123'), 'admin@dvwa.local'],
                ['john', md5('john123'), 'john@dvwa.local'],
                ['jane', md5('jane123'), 'jane@dvwa.local'],
                ['bob', md5('bob123'), 'bob@dvwa.local']
            ];
            
            foreach ($default_users as $user) {
                $mysqli->query("INSERT INTO users (username, password, email) VALUES ('{$user[0]}', '{$user[1]}', '{$user[2]}')");
            }
        }
        
        // Comments table
        $create_comments = "CREATE TABLE IF NOT EXISTS comments (
            id INT PRIMARY KEY AUTO_INCREMENT,
            username VARCHAR(50) NOT NULL,
            email VARCHAR(100),
            content TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        if ($mysqli->query($create_comments)) {
            $tables_created++;
            
            // Insert sample comments
            $sample_comments = [
                ['admin', 'admin@dvwa.local', 'Surat pertama telah diarsipkan'],
                ['john', 'john@dvwa.local', 'Surat kedua berhasil diterima'],
                ['jane', 'jane@dvwa.local', 'Arsip dokumen penting']
            ];
            
            foreach ($sample_comments as $comment) {
                $mysqli->query("INSERT INTO comments (username, email, content, created_at) VALUES ('{$comment[0]}', '{$comment[1]}', '{$comment[2]}', NOW())");
            }
        }
        
        $message = "✅ Database berhasil direset!\n";
        $message .= "📊 $tables_dropped tabel dihapus\n";
        $message .= "📊 $tables_created tabel dibuat ulang\n";
        $message .= "👤 4 user ditambahkan (admin, john, jane, bob)\n";
        $message .= "💬 3 sample comments ditambahkan";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Database - Sistem Arsip Surat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1a1a1a;
            color: #fff;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .back-link {
            color: #f00;
            text-decoration: none;
            margin-bottom: 20px;
            display: inline-block;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        h1 {
            color: #f00;
        }
        .warning-box {
            background-color: #4a0000;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #f00;
        }
        .warning-box h2 {
            color: #ff4444;
            margin-top: 0;
        }
        .info-box {
            background-color: #222;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #f00;
        }
        .success-box {
            background-color: #004a00;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #00ff00;
            white-space: pre-line;
        }
        .error-box {
            background-color: #4a0000;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #ff0000;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            background-color: #333;
            color: #fff;
            border: 1px solid #555;
            border-radius: 3px;
            font-size: 16px;
        }
        button {
            padding: 12px 30px;
            background-color: #f00;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }
        button:hover {
            background-color: #c00;
        }
        .btn-secondary {
            background-color: #555;
            margin-left: 10px;
        }
        .btn-secondary:hover {
            background-color: #777;
        }
        .table-list {
            background-color: #222;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .table-list h3 {
            margin-top: 0;
            color: #f00;
        }
        .table-item {
            padding: 8px;
            margin: 5px 0;
            background-color: #333;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($from_login): ?>
            <a href="login.php" class="back-link">← Kembali ke Login</a>
        <?php else: ?>
            <a href="dashboard.php" class="back-link">← Kembali ke Dashboard</a>
        <?php endif; ?>
        
        <h1>🗑️ Reset Database</h1>
        
        <div class="warning-box">
            <h2>⚠️ PERINGATAN!</h2>
            <p><strong>Tindakan ini akan menghapus SEMUA data:</strong></p>
            <ul>
                <li>❌ Semua tabel akan di-DROP</li>
                <li>❌ Semua user (kecuali default) akan hilang</li>
                <li>❌ Semua comments akan hilang</li>
                <li>❌ Semua data testing XSS/SQLi akan hilang</li>
            </ul>
            <p><strong>Yang akan dibuat ulang:</strong></p>
            <ul>
                <li>✅ Tabel users & comments</li>
                <li>✅ 4 user default (admin, john, jane, bob)</li>
                <li>✅ 3 sample comments</li>
            </ul>
        </div>
        
        <?php if ($message): ?>
            <div class="success-box">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error-box">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <div class="table-list">
            <h3>📊 Tabel Saat Ini:</h3>
            <?php
            $result = $mysqli->query("SHOW TABLES");
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_array()) {
                    echo '<div class="table-item">📁 ' . htmlspecialchars($row[0]) . '</div>';
                }
            } else {
                echo '<p style="color: #999;">Tidak ada tabel</p>';
            }
            ?>
        </div>
        
        <div class="info-box">
            <p><strong>Untuk mengonfirmasi reset database, ketik:</strong></p>
            <p style="font-size: 20px; color: #f00; font-family: monospace; font-weight: bold;">RESET DATABASE</p>
        </div>
        
        <form method="POST">
            <div class="form-group">
                <label for="confirmation">Konfirmasi Reset:</label>
                <input type="text" id="confirmation" name="confirmation" placeholder="Ketik: RESET DATABASE" required autofocus>
            </div>
            
            <button type="submit" name="confirm_reset">🗑️ Reset Database</button>
            <a href="dashboard.php" class="btn-secondary" style="text-decoration: none; display: inline-block; padding: 12px 30px; border-radius: 3px;">❌ Batal</a>
        </form>
        
        <div class="info-box" style="margin-top: 30px;">
            <h3>📝 Catatan:</h3>
            <ul>
                <li>Reset ini berguna untuk cleanup setelah testing XSS/SQLi</li>
                <li>Semua payload yang tersimpan akan dihapus</li>
                <li>Database akan kembali ke state awal</li>
                <li>Login credentials default: admin/admin123</li>
            </ul>
        </div>
    </div>
</body>
</html>

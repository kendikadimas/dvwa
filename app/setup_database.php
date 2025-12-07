<?php
// Database Setup Script
// This script will create database and tables automatically

$setup_message = '';
$setup_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup_database'])) {
    
    // Connect to MySQL without selecting a database
    $mysqli_setup = new mysqli(
        getenv('DB_HOST') ?: 'localhost',
        getenv('DB_USER') ?: 'dvwa',
        getenv('DB_PASSWORD') ?: 'dvwa123',
        null, // No database selected yet
        getenv('DB_PORT') ?: 3306
    );
    
    if ($mysqli_setup->connect_error) {
        $setup_message = "Connection Error: " . $mysqli_setup->connect_error;
    } else {
        $db_name = getenv('DB_NAME') ?: 'dvwa';
        
        // Create database if not exists
        $create_db = "CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        
        if ($mysqli_setup->query($create_db)) {
            // Select the database
            $mysqli_setup->select_db($db_name);
            
            // Create users table
            $create_users = "CREATE TABLE IF NOT EXISTS `users` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `username` varchar(50) NOT NULL,
                `password` varchar(255) NOT NULL,
                `email` varchar(100) DEFAULT NULL,
                `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                PRIMARY KEY (`id`),
                UNIQUE KEY `username` (`username`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            // Create comments table (for SQLi and XSS labs)
            $create_comments = "CREATE TABLE IF NOT EXISTS `comments` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `username` varchar(50) NOT NULL,
                `content` text NOT NULL,
                `email` varchar(100) DEFAULT NULL,
                `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            // Insert default admin user
            $admin_password = md5('admin123');
            $insert_admin = "INSERT IGNORE INTO `users` (`username`, `password`, `email`) 
                            VALUES ('admin', '$admin_password', 'admin@dvwa.local')";
            
            // Insert sample users for SQLi testing
            $insert_users = "INSERT IGNORE INTO `users` (`username`, `password`, `email`) VALUES
                ('john', '" . md5('john123') . "', 'john@example.com'),
                ('jane', '" . md5('jane123') . "', 'jane@example.com'),
                ('bob', '" . md5('bob123') . "', 'bob@example.com')";
            
            // Insert sample comments for SQLi testing
            $insert_comments = "INSERT IGNORE INTO `comments` (`id`, `username`, `content`, `email`) VALUES
                (1, 'admin', 'Surat pertama telah diarsipkan', 'admin@dvwa.local'),
                (2, 'john', 'Surat kedua berhasil diterima', 'john@example.com'),
                (3, 'jane', 'Arsip dokumen penting', 'jane@example.com')";
            
            $errors = [];
            
            // Execute all queries
            if (!$mysqli_setup->query($create_users)) {
                $errors[] = "Error creating users table: " . $mysqli_setup->error;
            }
            
            if (!$mysqli_setup->query($create_comments)) {
                $errors[] = "Error creating comments table: " . $mysqli_setup->error;
            }
            
            if (!$mysqli_setup->query($insert_admin)) {
                $errors[] = "Error inserting admin user: " . $mysqli_setup->error;
            }
            
            if (!$mysqli_setup->query($insert_users)) {
                $errors[] = "Error inserting sample users: " . $mysqli_setup->error;
            }
            
            if (!$mysqli_setup->query($insert_comments)) {
                $errors[] = "Error inserting sample comments: " . $mysqli_setup->error;
            }
            
            if (empty($errors)) {
                $setup_success = true;
                $setup_message = "✅ Database setup completed successfully!<br><br>" .
                                "<strong>Database:</strong> $db_name<br>" .
                                "<strong>Tables created:</strong> users, comments<br>" .
                                "<strong>Default admin:</strong> admin / admin123<br><br>" .
                                "You can now login.";
            } else {
                $setup_message = "⚠️ Setup completed with warnings:<br>" . implode("<br>", $errors);
            }
            
        } else {
            $setup_message = "❌ Error creating database: " . $mysqli_setup->error;
        }
        
        $mysqli_setup->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>DVWA - Database Setup</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #333;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .setup-container {
            background-color: #444;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
            max-width: 600px;
            width: 100%;
        }
        h1 {
            text-align: center;
            color: #f00;
            margin: 0 0 30px 0;
        }
        h2 {
            color: #4CAF50;
            margin-top: 0;
        }
        .info-box {
            background-color: #555;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #2196F3;
        }
        .info-box h3 {
            margin-top: 0;
            color: #2196F3;
        }
        .info-box ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .info-box li {
            margin: 5px 0;
        }
        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .message.success {
            background-color: #2d5016;
            border: 1px solid #4CAF50;
            color: #a5d6a7;
        }
        .message.error {
            background-color: #5c2121;
            border: 1px solid #f44336;
            color: #ff6b6b;
        }
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        button, .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            flex: 1;
        }
        .btn-primary {
            background-color: #4CAF50;
            color: #fff;
        }
        .btn-primary:hover {
            background-color: #45a049;
        }
        .btn-secondary {
            background-color: #666;
            color: #fff;
        }
        .btn-secondary:hover {
            background-color: #555;
        }
        .warning {
            background-color: #5c4d1f;
            border: 1px solid #ff9800;
            color: #ffcc80;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .code {
            background-color: #2d2d2d;
            padding: 10px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            margin: 10px 0;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <h1>DVWA Database Setup</h1>
        
        <?php if ($setup_message): ?>
            <div class="message <?php echo $setup_success ? 'success' : 'error'; ?>">
                <?php echo $setup_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!$setup_message || !$setup_success): ?>
            <div class="info-box">
                <h3>📦 What will be created:</h3>
                <ul>
                    <li><strong>Database:</strong> <?php echo getenv('DB_NAME') ?: 'dvwa'; ?></li>
                    <li><strong>Tables:</strong> users, comments</li>
                    <li><strong>Default Admin:</strong> admin / admin123</li>
                    <li><strong>Sample Users:</strong> john, jane, bob (password: {username}123)</li>
                    <li><strong>Sample Data:</strong> 3 comment records for testing</li>
                </ul>
            </div>
            
            <div class="info-box">
                <h3>⚙️ Database Configuration:</h3>
                <div class="code">
                    Host: <?php echo getenv('DB_HOST') ?: 'localhost'; ?><br>
                    Port: <?php echo getenv('DB_PORT') ?: '3306'; ?><br>
                    User: <?php echo getenv('DB_USER') ?: 'dvwa'; ?><br>
                    Database: <?php echo getenv('DB_NAME') ?: 'dvwa'; ?>
                </div>
            </div>
            
            <div class="warning">
                ⚠️ <strong>Warning:</strong> This will create database tables. If tables already exist, existing data will be preserved.
            </div>
            
            <form method="POST">
                <div class="button-group">
                    <button type="submit" name="setup_database" class="btn-primary">
                        🚀 Create Database
                    </button>
                    <a href="login.php" class="btn btn-secondary">
                        ← Back to Login
                    </a>
                </div>
            </form>
        <?php else: ?>
            <div class="button-group">
                <a href="login.php" class="btn btn-primary">
                    ✅ Go to Login Page
                </a>
            </div>
        <?php endif; ?>
        
        <div class="info-box" style="margin-top: 20px;">
            <h3>📚 For Developers:</h3>
            <p>If you cloned this project from GitHub:</p>
            <ol>
                <li>Make sure Docker is running</li>
                <li>Run: <code style="background: #2d2d2d; padding: 2px 6px; border-radius: 3px;">docker compose up -d</code></li>
                <li>Click "Create Database" button above</li>
                <li>Login with admin / admin123</li>
            </ol>
        </div>
    </div>
</body>
</html>

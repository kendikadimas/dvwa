<?php
require_once 'config.php';

$error = '';
$success_msg = '';
$login_attempt = false;
$db_needs_setup = false;
$db_exists = false;

// Check for reset message
if (isset($_GET['reset_msg'])) {
    $success_msg = $_GET['reset_msg'];
}

// Check if database/tables exist
if (!$mysqli) {
    $db_needs_setup = true;
    $error = "Database not found. Please setup the database first.";
} else {
    // Check if users table exists
    $table_check = @$mysqli->query("SHOW TABLES LIKE 'users'");
    if (!$table_check || $table_check->num_rows === 0) {
        $db_needs_setup = true;
        $error = "Database tables not found. Please setup the database first.";
    } else {
        $db_exists = true;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$db_needs_setup) {
    $login_attempt = true;
    
    // VULNERABLE: SQL Injection in login form - NO ESCAPING
    // User input is NOT escaped or parameterized
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Error-based SQL Injection
    // Attacker can craft: admin' OR '1'='1
    // Query becomes: SELECT * FROM users WHERE username='admin' OR '1'='1' AND password='...'
    $query = "SELECT * FROM users WHERE username='" . $username . "' AND password='" . md5($password) . "'";
    
    $result = $mysqli->query($query);
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>DVWA - Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #333;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: #444;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
            width: 300px;
        }
        h1 {
            text-align: center;
            color: #f00;
            margin: 0 0 30px 0;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            background-color: #555;
            color: #fff;
            border: 1px solid #666;
            border-radius: 3px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #f00;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #c00;
        }
        .error {
            color: #ff6b6b;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #5c2121;
            border-radius: 3px;
        }
        .info {
            color: #aaa;
            font-size: 12px;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>DVWA</h1>
        
        <?php if ($success_msg): ?>
            <div style="background-color: #2d5016; border: 1px solid #4CAF50; color: #a5d6a7; padding: 10px; border-radius: 3px; margin-bottom: 15px;">
                <?php echo htmlspecialchars($success_msg); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($db_needs_setup): ?>
            <div style="background-color: #2d5016; border: 1px solid #4CAF50; color: #a5d6a7; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <strong>🚀 First Time Setup Required</strong><br>
                <p style="margin: 10px 0;">It looks like this is your first time running DVWA. Click the button below to automatically create the database and tables.</p>
            </div>
            <a href="setup_database.php" style="display: block; width: 100%; padding: 12px; background-color: #4CAF50; color: #fff; text-align: center; text-decoration: none; border-radius: 3px; font-size: 16px; margin-bottom: 15px;">
                📦 Setup Database
            </a>
            <div style="text-align: center; color: #888; font-size: 14px; margin-top: 15px;">
                <p>Or if database already exists:</p>
                <button type="button" onclick="location.reload()" style="background-color: #666; padding: 8px 16px; font-size: 14px;">
                    🔄 Retry Connection
                </button>
            </div>
        <?php else: ?>
            <form method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit">Login</button>
            </form>
            
            <div class="info">
                <p><strong>Default Credentials:</strong></p>
                <p>Username: admin</p>
                <p>Password: admin123</p>
            </div>
            
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #555;">
                <div style="display: flex; gap: 10px;">
                    <a href="setup_database.php" style="flex: 1; padding: 10px; background-color: #4CAF50; color: #fff; text-align: center; text-decoration: none; border-radius: 3px; font-size: 14px;">
                        📦 Create/Reset DB
                    </a>
                    <a href="reset_db.php?from=login&lang=en" style="flex: 1; padding: 10px; background-color: #ff9800; color: #fff; text-align: center; text-decoration: none; border-radius: 3px; font-size: 14px;">
                        🔄 Reset Data
                    </a>
                </div>
                <div style="margin-top: 10px;">
                    <a href="reset_database.php?from=login" style="display: block; padding: 10px; background-color: #c00; color: #fff; text-align: center; text-decoration: none; border-radius: 3px; font-size: 14px;">
                        🗑️ Reset Database (DROP ALL)
                    </a>
                </div>
                <p style="color: #888; font-size: 11px; text-align: center; margin-top: 10px;">
                    Create: Setup database | Reset Data: Clear comments | Reset DB: Drop all tables
                </p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

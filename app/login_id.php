<?php
require_once 'config.php';

$error = '';
$success_msg = '';
$login_attempt = false;
$db_needs_setup = false;
$db_exists = false;

// Cek pesan reset
if (isset($_GET['reset_msg'])) {
    $success_msg = $_GET['reset_msg'];
}

// Cek apakah database/tabel sudah ada
if (!$mysqli) {
    $db_needs_setup = true;
    $error = "Database tidak ditemukan. Silakan setup database terlebih dahulu.";
} else {
    // Cek apakah tabel users sudah ada
    $table_check = @$mysqli->query("SHOW TABLES LIKE 'users'");
    if (!$table_check || $table_check->num_rows === 0) {
        $db_needs_setup = true;
        $error = "Tabel database tidak ditemukan. Silakan setup database terlebih dahulu.";
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
        header('Location: dashboard_id.php');
        exit;
    } else {
        $error = "Nama pengguna atau kata sandi salah";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>DVWA - Masuk</title>
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
                <strong>🚀 Setup Awal Diperlukan</strong><br>
                <p style="margin: 10px 0;">Sepertinya ini pertama kali Anda menjalankan DVWA. Klik tombol di bawah untuk membuat database dan tabel secara otomatis.</p>
            </div>
            <a href="setup_database_id.php" style="display: block; width: 100%; padding: 12px; background-color: #4CAF50; color: #fff; text-align: center; text-decoration: none; border-radius: 3px; font-size: 16px; margin-bottom: 15px;">
                📦 Setup Database
            </a>
            <div style="text-align: center; color: #888; font-size: 14px; margin-top: 15px;">
                <p>Atau jika database sudah ada:</p>
                <button type="button" onclick="location.reload()" style="background-color: #666; padding: 8px 16px; font-size: 14px;">
                    🔄 Coba Koneksi Lagi
                </button>
            </div>
        <?php else: ?>
            <form method="POST">
                <div class="form-group">
                    <label for="username">Nama Pengguna</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Kata Sandi</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit">Masuk</button>
            </form>
            
            <div class="info">
                <p><strong>Kredensial Default:</strong></p>
                <p>Nama Pengguna: admin</p>
                <p>Kata Sandi: admin123</p>
            </div>
            
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #555;">
                <div style="display: flex; gap: 10px;">
                    <a href="setup_database_id.php" style="flex: 1; padding: 10px; background-color: #4CAF50; color: #fff; text-align: center; text-decoration: none; border-radius: 3px; font-size: 14px;">
                        📦 Buat/Reset DB
                    </a>
                    <a href="reset_db.php?from=login&lang=id" style="flex: 1; padding: 10px; background-color: #ff9800; color: #fff; text-align: center; text-decoration: none; border-radius: 3px; font-size: 14px;">
                        🔄 Reset Data
                    </a>
                </div>
                <div style="margin-top: 10px;">
                    <a href="reset_database_id.php?from=login" style="display: block; padding: 10px; background-color: #c00; color: #fff; text-align: center; text-decoration: none; border-radius: 3px; font-size: 14px;">
                        🗑️ Reset Database (HAPUS SEMUA)
                    </a>
                </div>
                <p style="color: #888; font-size: 11px; text-align: center; margin-top: 10px;">
                    Buat: Setup database | Reset Data: Hapus komentar | Reset DB: Hapus semua tabel
                </p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

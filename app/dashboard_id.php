<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>DVWA - Dasbor</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1a1a1a;
            color: #fff;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        .header {
            background-color: #222;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        h1 {
            margin: 0;
            color: #f00;
        }
        .logout-btn {
            background-color: #f00;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .logout-btn:hover {
            background-color: #c00;
        }
        .labs {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        .lab-card {
            background-color: #222;
            padding: 20px;
            border-radius: 5px;
            border-left: 4px solid #f00;
        }
        .lab-card h2 {
            margin: 0 0 10px 0;
            color: #f00;
        }
        .lab-card p {
            margin: 0 0 15px 0;
            color: #aaa;
            font-size: 14px;
        }
        .lab-card a {
            background-color: #f00;
            color: #fff;
            padding: 8px 15px;
            border-radius: 3px;
            text-decoration: none;
            display: inline-block;
        }
        .lab-card a:hover {
            background-color: #c00;
        }
        .welcome {
            background-color: #222;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            color: #aaa;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>DVWA - Dasbor</h1>
            <a href="logout.php" class="logout-btn">Keluar</a>
        </div>
        
        <div class="welcome">
            <p>Selamat datang, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>! Ini adalah lab pelatihan untuk kerentanan web.</p>
        </div>
        
        <div class="labs">
            <div class="lab-card">
                <h2>SQL Injection</h2>
                <p>Pelajari serangan SQL injection pada formulir login dan pencarian. Termasuk SQLi berbasis error dan blind.</p>
                <a href="sqli_id.php">Buka Lab</a>
            </div>
            
            <div class="lab-card">
                <h2>XSS - Tercermin</h2>
                <p>Eksploitasi kerentanan cross-site scripting yang direfleksikan melalui parameter URL.</p>
                <a href="xss_reflected_id.php">Buka Lab</a>
            </div>
            
            <div class="lab-card">
                <h2>XSS - Tersimpan</h2>
                <p>Latih serangan XSS yang tersimpan di mana skrip berbahaya disimpan di database.</p>
                <a href="xss_stored_id.php">Buka Lab</a>
            </div>
            
            <div class="lab-card">
                <h2>CSRF</h2>
                <p>Ubah kata sandi tanpa token CSRF. Permintaan yang mengubah keadaan tidak terlindungi.</p>
                <a href="csrf_id.php">Buka Lab</a>
            </div>
        </div>
    </div>
</body>
</html>

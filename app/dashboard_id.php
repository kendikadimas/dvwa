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
    <title>Sistem Arsip Surat - Dasbor</title>
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
            <h1>Sistem Arsip Surat</h1>
            <a href="logout.php" class="logout-btn">Keluar</a>
        </div>
        <div class="welcome">
            <p>Selamat datang, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>! Kelola arsip surat masuk/keluar dan fitur lain di bawah ini.</p>
        </div>
        <div class="labs">
            <div class="lab-card">
                <h2>Pencarian Surat</h2>
                <p>Cari surat masuk/keluar berdasarkan nomor surat. Fitur ini rentan terhadap SQL Injection.</p>
                <a href="sqli_id.php">Cari Surat</a>
            </div>
            <div class="lab-card">
                <h2>Komentar Surat</h2>
                <p>Tambahkan komentar pada surat. Komentar akan tampil ke semua user. Fitur ini rentan terhadap Stored XSS.</p>
                <a href="xss_stored_id.php">Komentar Surat</a>
            </div>
            <div class="lab-card">
                <h2>Feedback Sistem</h2>
                <p>Kirim masukan/feedback ke admin. Fitur ini rentan terhadap Reflected XSS.</p>
                <a href="xss_reflected_id.php">Kirim Feedback</a>
            </div>
            <div class="lab-card">
                <h2>Ganti Password</h2>
                <p>Ubah password akun Anda. Fitur ini rentan terhadap serangan CSRF.</p>
                <a href="csrf_id.php">Ganti Password</a>
            </div>
        </div>
    </div>
</body>
</html>

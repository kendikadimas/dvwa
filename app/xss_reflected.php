<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// VULNERABLE: Reflected XSS
// User input from URL parameter is directly echoed without escaping
$name = $_GET['name'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>DVWA - XSS Reflected</title>
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
        input[type="text"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            background-color: #333;
            color: #fff;
            border: 1px solid #555;
            border-radius: 3px;
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
        .greeting {
            background-color: #222;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 18px;
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
        
        <h1>XSS - Reflected Lab</h1>
        
        <div class="info-box">
            <h2>Reflected Cross-Site Scripting</h2>
            <p>The application reflects user input from URL parameters without escaping. This allows injecting malicious JavaScript.</p>
        </div>
        
        <form method="GET">
            <div class="form-group">
                <label for="name">Enter Your Name</label>
                <input type="text" id="name" name="name" placeholder="Your name..." value="<?php echo htmlspecialchars($name); ?>">
            </div>
            <button type="submit">Greet</button>
        </form>
        
        <div class="hint">
            <strong>Exploitation Hints:</strong><br>
            • Try: <code>&lt;script&gt;alert('XSS')&lt;/script&gt;</code><br>
            • Try: <code>&lt;img src=x onerror="alert('XSS')"&gt;</code><br>
            • Try: <code>&lt;svg onload="alert('XSS')"&gt;</code><br>
            • Share malicious link: <code>http://localhost:8000/xss_reflected.php?name=&lt;script&gt;alert('XSS')&lt;/script&gt;</code>
        </div>
        
        <?php if ($name): ?>
            <div class="greeting">
                <p>Hello, <?php echo $name; ?>!</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

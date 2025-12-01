<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$result = null;
$search_query = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search_query = $_POST['search'] ?? '';
    
    // VULNERABLE: SQL Injection - No parameterized queries, NO ESCAPING
    // Attacker can use: 1 OR 1=1
    // Attacker can use: 1 UNION SELECT 1,2,3,4 -- 
    // Attacker can use: 1 UNION SELECT username, password, 3, created_at FROM users -- 
    // The query is directly concatenated without any sanitization
    $query = "SELECT id, username, email, created_at FROM comments WHERE id = " . $search_query;
    
    $result = $mysqli->query($query);
    
    if (!$result) {
        // Error-based SQLi - returns MySQL errors
        $error = "SQL Error: " . $mysqli->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>DVWA - SQL Injection</title>
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
        input[type="text"],
        textarea {
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
        .error {
            color: #ff6b6b;
            background-color: #5c2121;
            padding: 10px;
            border-radius: 3px;
            margin-bottom: 15px;
        }
        .results {
            background-color: #222;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .results table {
            width: 100%;
            border-collapse: collapse;
        }
        .results th,
        .results td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #555;
        }
        .results th {
            background-color: #333;
            font-weight: bold;
            color: #f00;
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
        
        <h1>SQL Injection Lab</h1>
        
        <div class="info-box">
            <h2>Search by ID</h2>
            <p>Search comments by ID number. The search is vulnerable to SQL injection.</p>
        </div>
        
        <form method="POST">
            <div class="form-group">
                <label for="search">Search ID</label>
                <input type="text" id="search" name="search" placeholder="Enter ID or SQL injection...">
            </div>
            <button type="submit">Search</button>
        </form>
        
        <div class="hint">
            <strong>Exploitation Hints:</strong><br>
            • Try: <code>1 OR 1=1</code> - to show all records<br>
            • Try: <code>1 UNION SELECT 1,2,3,4</code> - to test UNION columns<br>
            • Try: <code>1 UNION SELECT username, password, 3, created_at FROM users</code> - to extract user credentials<br>
            • Try: <code>1 AND SLEEP(5)</code> - time-based blind SQLi (response will be delayed 5 seconds)
        </div>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($result && $result->num_rows > 0): ?>
            <div class="results">
                <h3>Search Results</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars((string)$row['id']); ?></td>
                                <td><?php echo htmlspecialchars((string)$row['username']); ?></td>
                                <td><?php echo htmlspecialchars((string)($row['email'] ?? 'N/A')); ?></td>
                                <td><?php echo htmlspecialchars((string)$row['created_at']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif (!$error && $search_query): ?>
            <div class="error">No results found for: <?php echo htmlspecialchars($search_query); ?></div>
        <?php endif; ?>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do App in PHP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            line-height: 1.6;
        }
        h1 {
            color: #333;
        }
        ul {
            list-style-type: circle;
        }
        a {
            color: #0066cc;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>To-Do App</h1>
    <h2>To-Do List</h2>
    <ul>
    <?php
        // Include the MySQL connection
        require_once 'mysql/index.php';
        
        // Query the database
        $stmt = $pdo->query("SELECT name FROM todos");
        
        // Display the to-do items
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<li>" . htmlspecialchars($row['name']) . "</li>";
        }
    ?>
    </ul>
    <p><a href="/">Return to Home</a></p>
</body>
</html>
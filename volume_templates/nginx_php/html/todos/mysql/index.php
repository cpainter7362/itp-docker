<?php
// Retrieve database connection parameters from environment variables
$host = getenv('MYSQL_HOST');
$port = getenv('MYSQL_TCP_PORT');
$database = getenv('MYSQL_DATABASE');
$username = getenv('MYSQL_USER');
$password = getenv('MYSQL_PASSWORD');

// Function to get MySQL server information
function getMySQLInfo($pdo) {
    $info = [];
    $info['MySQL Version'] = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
    $info['Connection Status'] = $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS);
    $info['Server Info'] = $pdo->getAttribute(PDO::ATTR_SERVER_INFO);
    $info['Client Version'] = $pdo->getAttribute(PDO::ATTR_CLIENT_VERSION);
    $info['Driver Name'] = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    
    return $info;
}

// Function to get database statistics
function getDatabaseStats($pdo, $database) {
    $stats = [];
    
    // Get tables in the database
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $stats['Total Tables'] = count($tables);
    
    // Table-specific statistics
    $tableStats = [];
    foreach($tables as $table) {
        $rowCount = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        $tableStats[$table] = [
            'Rows' => $rowCount,
            'Created' => $pdo->query("SELECT CREATE_TIME FROM information_schema.TABLES WHERE TABLE_SCHEMA = '$database' AND TABLE_NAME = '$table'")->fetchColumn()
        ];
    }
    $stats['Tables'] = $tableStats;
    
    return $stats;
}

try {
    // Create PDO connection with timeout
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$database",
        $username,
        $password,
        array(PDO::ATTR_TIMEOUT => 3)
    );
    
    // Set error mode to throw exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get server information and statistics
    $serverInfo = getMySQLInfo($pdo);
    $databaseStats = getDatabaseStats($pdo, $database);
    
    // Success!
    $connectionSuccessful = true;
} catch (PDOException $e) {
    $errorMessage = $e->getMessage();
    $connectionSuccessful = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MySQL Connection Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            line-height: 1.6;
            color: #333;
        }
        h1, h2, h3 {
            color: #0066cc;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .card {
            background-color: #f9f9f9;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .success {
            background-color: #e6ffe6;
            border-left: 5px solid #33cc33;
        }
        .error {
            background-color: #ffe6e6;
            border-left: 5px solid #cc3333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        code {
            background: #f4f4f4;
            padding: 2px 5px;
            border-radius: 3px;
            font-family: monospace;
        }
        .actions {
            margin-top: 20px;
        }
        .btn {
            display: inline-block;
            padding: 8px 15px;
            background-color: #0066cc;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 10px;
        }
        .btn:hover {
            background-color: #0052a3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>MySQL Connection Test</h1>
        
        <?php if ($connectionSuccessful): ?>
            <div class="card success">
                <h2>✅ Connection Successful</h2>
                <p>Successfully connected to MySQL database <code><?php echo htmlspecialchars($database); ?></code> 
                   on <code><?php echo htmlspecialchars($host); ?>:<?php echo htmlspecialchars($port); ?></code>
                   as user <code><?php echo htmlspecialchars($username); ?></code>.</p>
            </div>
            
            <div class="card">
                <h2>MySQL Server Information</h2>
                <table>
                    <tr>
                        <th>Property</th>
                        <th>Value</th>
                    </tr>
                    <?php foreach ($serverInfo as $property => $value): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($property); ?></td>
                            <td><?php echo htmlspecialchars($value ?? 'N/A'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            
            <div class="card">
                <h2>Database Statistics</h2>
                <p>Database <code><?php echo htmlspecialchars($database); ?></code> contains 
                   <strong><?php echo htmlspecialchars($databaseStats['Total Tables']); ?></strong> tables.</p>
                
                <?php if (!empty($databaseStats['Tables'])): ?>
                    <h3>Table Details</h3>
                    <table>
                        <tr>
                            <th>Table Name</th>
                            <th>Rows</th>
                            <th>Created</th>
                        </tr>
                        <?php foreach ($databaseStats['Tables'] as $table => $stats): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($table); ?></td>
                                <td><?php echo htmlspecialchars($stats['Rows']); ?></td>
                                <td><?php echo htmlspecialchars($stats['Created'] ?? 'N/A'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                    
                    <?php if (isset($databaseStats['Tables']['todos'])): ?>
                        <h3>Sample Data from <code>todos</code> Table</h3>
                        <table>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                            </tr>
                            <?php
                            $todoItems = $pdo->query("SELECT * FROM todos LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($todoItems as $item): 
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['id']); ?></td>
                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            
            <div class="card">
                <h2>Connection Details</h2>
                <p>These connection details are provided via environment variables to the PHP container:</p>
                <table>
                    <tr>
                        <th>Parameter</th>
                        <th>Environment Variable</th>
                        <th>Value</th>
                    </tr>
                    <tr>
                        <td>Host</td>
                        <td><code>MYSQL_HOST</code></td>
                        <td><?php echo htmlspecialchars($host); ?></td>
                    </tr>
                    <tr>
                        <td>Port</td>
                        <td><code>MYSQL_TCP_PORT</code></td>
                        <td><?php echo htmlspecialchars($port); ?></td>
                    </tr>
                    <tr>
                        <td>Database</td>
                        <td><code>MYSQL_DATABASE</code></td>
                        <td><?php echo htmlspecialchars($database); ?></td>
                    </tr>
                    <tr>
                        <td>Username</td>
                        <td><code>MYSQL_USER</code></td>
                        <td><?php echo htmlspecialchars($username); ?></td>
                    </tr>
                    <tr>
                        <td>Password</td>
                        <td><code>MYSQL_PASSWORD</code></td>
                        <td>********</td>
                    </tr>
                </table>
            </div>
        <?php else: ?>
            <div class="card error">
                <h2>❌ Connection Failed</h2>
                <p>Error: <?php echo htmlspecialchars($errorMessage); ?></p>
            </div>
            
            <div class="card">
                <h2>Connection Details Used</h2>
                <table>
                    <tr>
                        <th>Parameter</th>
                        <th>Environment Variable</th>
                        <th>Value</th>
                    </tr>
                    <tr>
                        <td>Host</td>
                        <td><code>MYSQL_HOST</code></td>
                        <td><?php echo htmlspecialchars($host ?? 'Not set'); ?></td>
                    </tr>
                    <tr>
                        <td>Port</td>
                        <td><code>MYSQL_TCP_PORT</code></td>
                        <td><?php echo htmlspecialchars($port ?? 'Not set'); ?></td>
                    </tr>
                    <tr>
                        <td>Database</td>
                        <td><code>MYSQL_DATABASE</code></td>
                        <td><?php echo htmlspecialchars($database ?? 'Not set'); ?></td>
                    </tr>
                    <tr>
                        <td>Username</td>
                        <td><code>MYSQL_USER</code></td>
                        <td><?php echo htmlspecialchars($username ?? 'Not set'); ?></td>
                    </tr>
                </table>
            </div>
            
            <div class="card">
                <h2>Troubleshooting</h2>
                <ul>
                    <li>Verify Docker containers are running: <code>docker-compose ps</code></li>
                    <li>Check MySQL container logs: <code>docker-compose logs db_svc</code></li>
                    <li>Verify environment variables in the PHP container: <code>docker-compose exec php_svc env | grep MYSQL</code></li>
                    <li>Try restarting the containers: <code>docker-compose restart</code></li>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="actions">
            <a href="/todos/" class="btn">Go to Todo App</a>
            <a href="/" class="btn">Return to Homepage</a>
        </div>
    </div>
</body>
</html>
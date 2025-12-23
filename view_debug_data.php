<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Debug Viewer</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
            background: #f0f0f0;
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #66785F;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .status-ok {
            color: green;
            font-weight: bold;
        }

        .status-err {
            color: red;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Wedding Planner - Database Debugger</h1>
        <p>This page helps you verify the database content since the IDE connection is having issues.</p>

        <?php
        require_once 'config.php';

        if (isset($pdo)) {
            echo "<div class='status-ok'>✅ Database Connection Successful</div>";

            try {
                $stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo "<h3>Registered Users (" . count($users) . ")</h3>";

                if (count($users) > 0) {
                    echo "<table>";
                    echo "<thead><tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Role</th><th>Age</th></tr></thead>";
                    echo "<tbody>";
                    foreach ($users as $user) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($user['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['first_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['last_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['role']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['age']) . "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody></table>";
                } else {
                    echo "<p>No users found in the table.</p>";
                }
            } catch (PDOException $e) {
                echo "<div class='status-err'>Query Error: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        } else {
            echo "<div class='status-err'>❌ Connection Error: \$pdo is not set in config.php</div>";
        }
        ?>
    </div>
</body>

</html>
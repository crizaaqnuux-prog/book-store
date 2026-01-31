<?php
require_once 'config.php';

echo "<h2>User Debug Info</h2>";
try {
    $stmt = $pdo->query("SELECT id, username, email, role FROM users");
    $users = $stmt->fetchAll();
    
    echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
            </tr>";
    foreach ($users as $user) {
        echo "<tr>
                <td>{$user['id']}</td>
                <td>{$user['username']}</td>
                <td>{$user['email']}</td>
                <td>{$user['role']}</td>
              </tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

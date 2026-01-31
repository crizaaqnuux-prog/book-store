<?php
require_once 'config.php';

$admin_user = 'admin';
$admin_pass = 'admin123';
$admin_email = 'admin@bookstore.com';
$hashed_pass = password_hash($admin_pass, PASSWORD_DEFAULT);

try {
    // 1. Check if admin exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$admin_user]);
    $user = $stmt->fetch();

    if ($user) {
        // Update existing admin
        $stmt = $pdo->prepare("UPDATE users SET password = ?, role = 'admin', email = ? WHERE id = ?");
        $stmt->execute([$hashed_pass, $admin_email, $user['id']]);
        echo "✅ Admin account updated successfully!<br>";
    } else {
        // Create new admin
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'admin')");
        $stmt->execute([$admin_user, $hashed_pass, $admin_email]);
        echo "✅ Admin account created successfully!<br>";
    }
    
    echo "<b>Username:</b> $admin_user<br>";
    echo "<b>Password:</b> $admin_pass<br>";
    echo "<p><a href='/Bookstore/auth/login.php'>Go to Login</a></p>";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>

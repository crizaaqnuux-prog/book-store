<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'bookstore');

try {
    // 1. Connect to MySQL without choosing a database
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>Bookstore System Setup</h2>";

    // 2. Read the SQL file
    $sql_file = 'database.sql';
    if (!file_exists($sql_file)) {
        die("Error: database.sql file not found.");
    }

    $sql = file_get_contents($sql_file);

    // 3. Execute the SQL (which contains CREATE DATABASE and table creation)
    echo "Creating database and tables...<br>";
    // Use multi_query for the whole script or execute it carefully
    $pdo->exec($sql);
    
    // Check if delivery_status column exists, if not add it (migration for existing users)
    $check_column = $pdo->query("SHOW COLUMNS FROM orders LIKE 'delivery_status'");
    if (!$check_column->fetch()) {
        $pdo->exec("ALTER TABLE orders ADD COLUMN delivery_status ENUM('Ordered', 'Shipped', 'Delivered') DEFAULT 'Ordered' AFTER status");
        echo "Migration: Added delivery_status column to orders table.<br>";
    }

    // Check if payment_number column exists in payments table
    $check_pay_col = $pdo->query("SHOW COLUMNS FROM payments LIKE 'payment_number'");
    if (!$check_pay_col->fetch()) {
        $pdo->exec("ALTER TABLE payments ADD COLUMN payment_number VARCHAR(20) AFTER payment_method");
        echo "Migration: Added payment_number column to payments table.<br>";
    }

    echo "<div style='color: green; font-weight: bold; margin-top: 10px;'>
            ✅ Setup completed successfully! Featured books have been added.
          </div>";
    echo "<p><a href='index.php'>Go to Home Page</a> | <a href='auth/login.php'>Login</a></p>";
    echo "<p>Admin Credentials: <b>admin</b> / <b>admin123</b></p>";

} catch (PDOException $e) {
    echo "<div style='color: red; font-weight: bold;'>
            ❌ Error: " . $e->getMessage() . "
          </div>";
    echo "<p>Please ensure XAMPP MySQL is running and your credentials in <code>config.php</code> are correct.</p>";
}
?>

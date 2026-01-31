<?php
require_once 'config.php';

echo "<h2>Database Synchronization</h2>";

try {
    // 1. Add delivery_status to orders
    $check = $pdo->query("SHOW COLUMNS FROM orders LIKE 'delivery_status'");
    if (!$check->fetch()) {
        $pdo->exec("ALTER TABLE orders ADD COLUMN delivery_status ENUM('Ordered', 'Shipped', 'Delivered') DEFAULT 'Ordered' AFTER status");
        echo "✅ Added 'delivery_status' to orders table.<br>";
    } else {
        echo "ℹ️ 'delivery_status' already exists in orders table.<br>";
    }
    
    // 2. Add payment_number to payments
    $check = $pdo->query("SHOW COLUMNS FROM payments LIKE 'payment_number'");
    if (!$check->fetch()) {
        $pdo->exec("ALTER TABLE payments ADD COLUMN payment_number VARCHAR(20) AFTER payment_method");
        echo "✅ Added 'payment_number' to payments table.<br>";
    } else {
        echo "ℹ️ 'payment_number' already exists in payments table.<br>";
    }
    
    echo "<h3>Success! All columns are synchronized.</h3>";
    echo "<p><a href='/Bookstore/admin/manage_orders.php'>Go back to Order Logistics</a></p>";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>

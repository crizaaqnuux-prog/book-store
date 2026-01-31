<?php
require_once '../config.php';

if (!isAdmin()) {
    redirect('../auth/login.php');
}

$message = '';

// Handle Status Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_delivery'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['delivery_status'];
    
    $stmt = $pdo->prepare("UPDATE orders SET delivery_status = ? WHERE id = ?");
    if ($stmt->execute([$new_status, $order_id])) {
        $message = "Order #$order_id status updated to $new_status!";
    }
}

// Fetch Orders with User Info
$orders = $pdo->query("
    SELECT o.*, u.username, u.email 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC
")->fetchAll();

include '../includes/header.php';
?>

<div class="animate-up">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">
        <div>
            <h1 style="font-size: 2.5rem; font-weight: 800; letter-spacing: -0.02em;">Order Logistics</h1>
            <p style="color: var(--text-muted); font-size: 1.1rem;">Manage shipments and track delivery status for all customers.</p>
        </div>
        <a href="dashboard.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Dashboard</a>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success animate-fade"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Order Info</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Order Status</th>
                    <th>Delivery Progress</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): 
                    // Fallback for missing delivery_status
                    $o['delivery_status'] = $o['delivery_status'] ?? 'Ordered';
                ?>
                    <tr>
                        <td>
                            <p style="font-weight: 700; margin-bottom: 0.25rem;">#<?php echo $o['id']; ?></p>
                            <p style="font-size: 0.75rem; color: var(--text-muted);"><?php echo date('M d, Y', strtotime($o['created_at'])); ?></p>
                        </td>
                        <td>
                            <p style="font-weight: 600;"><?php echo htmlspecialchars($o['username']); ?></p>
                            <p style="font-size: 0.75rem; color: var(--text-muted);"><?php echo htmlspecialchars($o['email']); ?></p>
                        </td>
                        <td style="font-weight: 700; color: var(--primary);">$<?php echo number_format($o['total_amount'], 2); ?></td>
                        <td>
                            <span style="background: <?php echo $o['status'] === 'completed' ? '#dcfce7' : '#fee2e2'; ?>; color: <?php echo $o['status'] === 'completed' ? '#16a34a' : '#ef4444'; ?>; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase;">
                                <?php echo $o['status']; ?>
                            </span>
                        </td>
                        <td>
                            <?php
                            $prog_color = '#4f46e5';
                            if ($o['delivery_status'] === 'Shipped') $prog_color = '#f59e0b';
                            if ($o['delivery_status'] === 'Delivered') $prog_color = '#10b981';
                            ?>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <div style="width: 8px; height: 8px; border-radius: 50%; background: <?php echo $prog_color; ?>;"></div>
                                <span style="font-weight: 700; font-size: 0.85rem; color: <?php echo $prog_color; ?>;"><?php echo $o['delivery_status']; ?></span>
                            </div>
                        </td>
                        <td>
                            <form method="POST" style="display: flex; gap: 0.5rem;">
                                <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                <select name="delivery_status" class="form-control" style="padding: 0.4rem; font-size: 0.8rem; height: 35px; width: 120px;">
                                    <option value="Ordered" <?php echo $o['delivery_status'] === 'Ordered' ? 'selected' : ''; ?>>Ordered</option>
                                    <option value="Shipped" <?php echo $o['delivery_status'] === 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="Delivered" <?php echo $o['delivery_status'] === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                </select>
                                <button type="submit" name="update_delivery" class="btn btn-primary" style="padding: 0 0.75rem; height: 35px;">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

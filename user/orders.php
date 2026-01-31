<?php
require_once '../config.php';

if (!isLoggedIn()) redirect('../auth/login.php');

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT o.*, p.payment_method, p.transaction_id, p.status as payment_status
    FROM orders o
    LEFT JOIN payments p ON o.id = p.order_id
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

include '../includes/header.php';
?>

<div class="animate-fade">
    <h1 style="margin-bottom: 2rem;">My Orders</h1>

    <?php if (empty($orders)): ?>
        <div class="card" style="text-align: center; padding: 4rem;">
            <i class="fas fa-box-open" style="font-size: 3rem; color: var(--border); margin-bottom: 1rem;"></i>
            <h3>No orders yet.</h3>
            <p style="color: var(--text-muted); margin-bottom: 2rem;">When you buy books, they will appear here.</p>
            <a href="../index.php" class="btn btn-primary">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="grid" style="display: grid; gap: 1.5rem;">
            <?php foreach ($orders as $order): ?>
                <div class="card" style="padding: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
                        <div>
                            <h3 style="margin-bottom: 0.25rem;">Order #<?php echo $order['id']; ?></h3>
                            <p style="color: var(--text-muted); font-size: 0.875rem;">Placed on <?php echo date('M d, Y', strtotime($order['created_at'])); ?></p>
                        </div>
                        <div style="text-align: right;">
                            <span style="background: #dcfce7; color: #059669; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">
                                <?php echo $order['status']; ?>
                            </span>
                            <h3 style="margin-top: 0.5rem;">$<?php echo number_format($order['total_amount'], 2); ?></h3>
                        </div>
                    </div>
                    
                    <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; font-size: 0.875rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span style="color: var(--text-muted);">Delivery Status</span>
                            <span style="font-weight: 700; color: var(--primary);"><?php echo htmlspecialchars($order['delivery_status'] ?? 'Ordered'); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span style="color: var(--text-muted);">Payment Method</span>
                            <span style="font-weight: 600;"><?php echo htmlspecialchars($order['payment_method']); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: var(--text-muted);">Transaction ID</span>
                            <span style="font-family: monospace; font-weight: 600;"><?php echo htmlspecialchars($order['transaction_id']); ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>

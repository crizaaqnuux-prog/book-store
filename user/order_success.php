<?php
require_once '../config.php';

if (!isLoggedIn()) redirect('../auth/login.php');

$order_id = $_SESSION['success_order'] ?? null;
if (!$order_id) redirect('../index.php');

// Fetch order details
$stmt = $pdo->prepare("
    SELECT o.*, u.username, u.email, p.payment_method, p.payment_number, p.transaction_id, p.created_at as payment_date
    FROM orders o
    JOIN users u ON o.user_id = u.id
    LEFT JOIN payments p ON o.id = p.order_id
    WHERE o.id = ? AND o.user_id = ?
");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) redirect('../index.php');

// Fallback for missing delivery_status
$order['delivery_status'] = $order['delivery_status'] ?? 'Ordered';

// Fetch order items
$stmt = $pdo->prepare("
    SELECT oi.*, b.title, b.author
    FROM order_items oi
    JOIN books b ON oi.book_id = b.id
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();

// We keep the session variable until the end of the script in case of errors, 
// but logically this page is only for the immediate success.
unset($_SESSION['success_order']);

include '../includes/header.php';
?>

<div class="animate-up" style="max-width: 800px; margin: 2rem auto;">
    <div style="text-align: center; margin-bottom: 3rem;">
        <div style="background: #dcfce7; color: #059669; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin: 0 auto 1.5rem;">
            <i class="fas fa-check"></i>
        </div>
        <h1 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 0.5rem;">Order Confirmed!</h1>
        <p style="color: var(--text-muted); font-size: 1.1rem;">Thank you for your purchase. Your order is being processed.</p>
    </div>

    <!-- Visual Tracking Section -->
    <div class="card" style="margin-bottom: 2rem; padding: 2.5rem;">
        <h3 style="margin-bottom: 2rem; font-weight: 800; text-align: center;">Delivery Progress</h3>
        <div style="display: flex; justify-content: space-between; position: relative; padding: 0 1rem;">
            <!-- Progress Line -->
            <div style="position: absolute; top: 15px; left: 10%; right: 10%; height: 4px; background: #e2e8f0; z-index: 1;">
                <?php
                $width = '0%';
                if ($order['delivery_status'] === 'Shipped') $width = '50%';
                if ($order['delivery_status'] === 'Delivered') $width = '100%';
                ?>
                <div style="width: <?php echo $width; ?>; height: 100%; background: var(--primary); transition: width 1s ease-in-out;"></div>
            </div>

            <!-- Steps -->
            <div style="z-index: 2; text-align: center; width: 80px;">
                <div style="width: 34px; height: 34px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem; border: 4px solid white; box-shadow: 0 0 0 1px var(--primary);">
                    <i class="fas fa-check" style="font-size: 0.8rem;"></i>
                </div>
                <span style="font-size: 0.75rem; font-weight: 700; color: var(--text-main);">Ordered</span>
            </div>

            <div style="z-index: 2; text-align: center; width: 80px;">
                <div style="width: 34px; height: 34px; border-radius: 50%; background: <?php echo ($order['delivery_status'] === 'Shipped' || $order['delivery_status'] === 'Delivered') ? 'var(--primary)' : '#f1f5f9'; ?>; color: <?php echo ($order['delivery_status'] === 'Shipped' || $order['delivery_status'] === 'Delivered') ? 'white' : 'var(--text-muted)'; ?>; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem; border: 4px solid white; box-shadow: 0 0 0 1px <?php echo ($order['delivery_status'] === 'Shipped' || $order['delivery_status'] === 'Delivered') ? 'var(--primary)' : '#e2e8f0'; ?>;">
                    <i class="fas fa-truck" style="font-size: 0.8rem;"></i>
                </div>
                <span style="font-size: 0.75rem; font-weight: 700; color: <?php echo ($order['delivery_status'] === 'Shipped' || $order['delivery_status'] === 'Delivered') ? 'var(--text-main)' : 'var(--text-muted)'; ?>;">Shipped</span>
            </div>

            <div style="z-index: 2; text-align: center; width: 80px;">
                <div style="width: 34px; height: 34px; border-radius: 50%; background: <?php echo ($order['delivery_status'] === 'Delivered') ? 'var(--success)' : '#f1f5f9'; ?>; color: <?php echo ($order['delivery_status'] === 'Delivered') ? 'white' : 'var(--text-muted)'; ?>; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem; border: 4px solid white; box-shadow: 0 0 0 1px <?php echo ($order['delivery_status'] === 'Delivered') ? 'var(--success)' : '#e2e8f0'; ?>;">
                    <i class="fas fa-home" style="font-size: 0.8rem;"></i>
                </div>
                <span style="font-size: 0.75rem; font-weight: 700; color: <?php echo ($order['delivery_status'] === 'Delivered') ? 'var(--text-main)' : 'var(--text-muted)'; ?>;">Delivered</span>
            </div>
        </div>
    </div>

    <!-- Order Report / Invoice -->
    <div class="card" style="padding: 0; overflow: hidden; border: 1px solid var(--border);">
        <div style="background: linear-gradient(135deg, var(--primary) 0%, #7c3aed 100%); padding: 2rem; color: white;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <h2 style="font-weight: 800; font-size: 1.5rem; margin-bottom: 0.5rem;">INVOICE</h2>
                    <p style="opacity: 0.9;">Order #<?php echo $order['id']; ?></p>
                </div>
                <div style="text-align: right;">
                    <p style="font-weight: 700; margin-bottom: 0.25rem;">BookStore Store</p>
                    <p style="font-size: 0.875rem; opacity: 0.8;">123 Library Avenue<br>Digital City, 54321</p>
                </div>
            </div>
        </div>

        <div style="padding: 2.5rem;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 3rem;">
                <div>
                    <h4 style="color: var(--text-muted); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; margin-bottom: 1rem;">Billed To</h4>
                    <p style="font-weight: 700; font-size: 1.1rem; margin-bottom: 0.25rem;"><?php echo htmlspecialchars($order['username']); ?></p>
                    <p style="color: var(--text-muted);"><?php echo htmlspecialchars($order['email']); ?></p>
                </div>
                <div style="text-align: right;">
                    <h4 style="color: var(--text-muted); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; margin-bottom: 1rem;">Payment Details</h4>
                    <p style="font-weight: 700; margin-bottom: 0.25rem;"><?php echo htmlspecialchars($order['payment_method']); ?></p>
                    <?php if ($order['payment_number']): ?>
                        <p style="font-size: 0.875rem; color: var(--text-muted);">Number: <?php echo htmlspecialchars($order['payment_number']); ?></p>
                    <?php endif; ?>
                    <p style="font-size: 0.875rem; color: var(--text-muted);">ID: <?php echo htmlspecialchars($order['transaction_id']); ?></p>
                    <p style="font-size: 0.875rem; color: var(--text-muted);"><?php echo date('M d, Y', strtotime($order['payment_date'])); ?></p>
                </div>
            </div>

            <div class="table-container" style="border: none; box-shadow: none;">
                <table style="width: 100%;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--bg-main);">
                            <th style="background: transparent; padding-left: 0;">Description</th>
                            <th style="background: transparent;">Price</th>
                            <th style="background: transparent; text-align: center;">Qty</th>
                            <th style="background: transparent; text-align: right; padding-right: 0;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td style="padding-left: 0;">
                                    <p style="font-weight: 700; margin-bottom: 0.25rem;"><?php echo htmlspecialchars($item['title']); ?></p>
                                    <p style="font-size: 0.8rem; color: var(--text-muted);">by <?php echo htmlspecialchars($item['author']); ?></p>
                                </td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td style="text-align: center;"><?php echo $item['quantity']; ?></td>
                                <td style="text-align: right; padding-right: 0; font-weight: 700;">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 3rem; padding-top: 2rem; border-top: 2px solid var(--bg-main); display: flex; flex-direction: column; align-items: flex-end;">
                <div style="width: 250px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                        <span style="color: var(--text-muted);">Subtotal</span>
                        <span style="font-weight: 600;">$<?php echo number_format($order['total_amount'], 2); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem;">
                        <span style="color: var(--text-muted);">Shipping</span>
                        <span style="color: var(--success); font-weight: 600;">FREE</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-top: 1px solid var(--border); padding-top: 1.5rem;">
                        <span style="font-size: 1.25rem; font-weight: 800;">Total Amount</span>
                        <span style="font-size: 1.25rem; font-weight: 800; color: var(--primary);">$<?php echo number_format($order['total_amount'], 2); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div style="background: #f8fafc; padding: 1.5rem; text-align: center; color: var(--text-muted); font-size: 0.875rem;">
            A copy of this report has also been saved to your <a href="orders.php" style="color: var(--primary); font-weight: 600;">Order History</a>.
        </div>
    </div>

    <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 3rem;">
        <button onclick="window.print()" class="btn btn-outline"><i class="fas fa-print"></i> Print Invoice</button>
        <a href="../index.php" class="btn btn-primary"><i class="fas fa-shopping-bag"></i> Continue Shopping</a>
    </div>
</div>

<style>
@media print {
    header, footer, .btn, .nav-container { display: none !important; }
    .card { box-shadow: none !important; border: none !important; }
    .animate-up { transform: none !important; animation: none !important; }
    body { background: white !important; }
}
</style>

<?php include '../includes/footer.php'; ?>

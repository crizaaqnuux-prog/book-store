<?php
require_once '../config.php';

if (!isAdmin()) {
    redirect('../auth/login.php');
}

// Sales Report
$sales = $pdo->query("
    SELECT o.id, u.username, o.total_amount, o.status, o.created_at 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC
")->fetchAll();

// Inventory Report
$inventory = $pdo->query("SELECT title, author, category, stock, price FROM books WHERE stock < 10 ORDER BY stock ASC")->fetchAll();

include '../includes/header.php';
?>

<div class="animate-fade">
    <h1 style="margin-bottom: 2rem;">Reports & Analytics</h1>

    <div style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
        <!-- Sales Report -->
        <div class="card">
            <h3 style="margin-bottom: 1.5rem;">Recent Sales</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sales as $s): ?>
                            <tr>
                                <td>#<?php echo $s['id']; ?></td>
                                <td><?php echo htmlspecialchars($s['username']); ?></td>
                                <td style="font-weight: 700;">$<?php echo number_format($s['total_amount'], 2); ?></td>
                                <td>
                                    <span style="background: <?php echo $s['status'] === 'completed' ? '#dcfce7' : '#fef3c7'; ?>; color: <?php echo $s['status'] === 'completed' ? '#059669' : '#d97706'; ?>; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 700;">
                                        <?php echo strtoupper($s['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($s['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($sales)): ?>
                            <tr><td colspan="5" style="text-align: center;">No sales recorded yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Inventory Report -->
        <div class="card">
            <h3 style="margin-bottom: 1.5rem; color: var(--accent);">Low Stock Alert</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Book Title</th>
                            <th>Category</th>
                            <th>Current Stock</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inventory as $i): ?>
                            <tr>
                                <td style="font-weight: 600;"><?php echo htmlspecialchars($i['title']); ?></td>
                                <td><?php echo htmlspecialchars($i['category']); ?></td>
                                <td style="color: <?php echo $i['stock'] < 5 ? 'var(--accent)' : 'inherit'; ?>; font-weight: 700;">
                                    <?php echo $i['stock']; ?> units
                                </td>
                                <td>$<?php echo number_format($i['price'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($inventory)): ?>
                            <tr><td colspan="4" style="text-align: center;">All items are well stocked.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

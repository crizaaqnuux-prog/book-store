<?php
require_once '../config.php';

if (!isLoggedIn() || empty($_SESSION['cart'])) {
    redirect('cart.php');
}

$grand_total = 0;
foreach ($_SESSION['cart'] as $item) {
    $grand_total += $item['price'] * $item['quantity'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'];
    $payment_number = isset($_POST['payment_number']) ? trim($_POST['payment_number']) : '';
    
    try {
        $pdo->beginTransaction();
        
        // 1. Create Order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'completed')");
        $stmt->execute([$_SESSION['user_id'], $grand_total]);
        $order_id = $pdo->lastInsertId();
        
        // 2. Add Order Items & Update Stock
        $item_stmt = $pdo->prepare("INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stock_stmt = $pdo->prepare("UPDATE books SET stock = stock - ? WHERE id = ?");
        
        foreach ($_SESSION['cart'] as $book_id => $item) {
            $item_stmt->execute([$order_id, $book_id, $item['quantity'], $item['price']]);
            $stock_stmt->execute([$item['quantity'], $book_id]);
        }
        
        // 3. Create Payment
        $pay_stmt = $pdo->prepare("INSERT INTO payments (order_id, payment_method, payment_number, transaction_id, status, amount) VALUES (?, ?, ?, ?, 'success', ?)");
        $transaction_id = 'TRANS-' . strtoupper(uniqid());
        $pay_stmt->execute([$order_id, $payment_method, $payment_number, $transaction_id, $grand_total]);
        
        $pdo->commit();
        
        // Clear cart
        $_SESSION['cart'] = [];
        $_SESSION['success_order'] = $order_id;
        redirect('order_success.php');
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Failed to process order. " . $e->getMessage();
    }
}

include '../includes/header.php';
?>

<div class="animate-fade" style="max-width: 900px; margin: 0 auto;">
    <h1 style="margin-bottom: 2rem;">Checkout</h1>

    <div style="display: grid; grid-template-columns: 1fr 350px; gap: 2rem;">
        <div>
            <div class="card" style="margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1.5rem;">Shipping Information</h3>
                <form id="checkout-form" method="POST">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" class="form-control" required placeholder="John Doe">
                    </div>
                    <div class="form-group">
                        <label>Shipping Address</label>
                        <textarea class="form-control" required rows="3" placeholder="123 Book St, Library City"></textarea>
                    </div>
                    
                    <h3 style="margin: 2rem 0 1.5rem;">Payment Method</h3>
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <label class="card" style="padding: 1rem; display: flex; align-items: center; gap: 1rem; cursor: pointer;">
                            <input type="radio" name="payment_method" value="EVC-Plus" checked>
                            <i class="fas fa-mobile-alt"></i> EVC-Plus
                        </label>
                        <label class="card" style="padding: 1rem; display: flex; align-items: center; gap: 1rem; cursor: pointer;">
                            <input type="radio" name="payment_method" value="E-Dahab">
                            <i class="fas fa-wallet"></i> E-Dahab
                        </label>
                    </div>

                    <div class="form-group" style="margin-top: 1.5rem;">
                        <label>Mobile Number (EVC / E-Dahab)</label>
                        <input type="text" name="payment_number" class="form-control" required placeholder="e.g. 61xxxxxxx">
                    </div>
                </form>
            </div>
        </div>

        <div>
            <div class="card" style="position: sticky; top: 100px;">
                <h3 style="margin-bottom: 1.5rem;">Your Order</h3>
                <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                    <div style="display: flex; justify-content: space-between; font-size: 0.875rem; margin-bottom: 0.75rem;">
                        <span style="color: var(--text-muted);"><?php echo $item['quantity']; ?>x <?php echo htmlspecialchars($item['title']); ?></span>
                        <span style="font-weight: 600;">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                    </div>
                <?php endforeach; ?>
                
                <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 2rem;">
                        <span style="font-size: 1.25rem; font-weight: 800;">Total</span>
                        <span style="font-size: 1.25rem; font-weight: 800; color: var(--primary);">$<?php echo number_format($grand_total, 2); ?></span>
                    </div>
                    <button type="submit" form="checkout-form" class="btn btn-primary" style="width: 100%; padding: 1rem;">Place Order</button>
                    <p style="text-align: center; font-size: 0.75rem; color: var(--text-muted); margin-top: 1rem;">
                        <i class="fas fa-lock"></i> Secure Payment Processing
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

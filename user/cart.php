<?php
require_once '../config.php';

if (!isLoggedIn()) {
    $_SESSION['error'] = "Please login to add items to cart.";
    redirect('../auth/login.php');
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add to cart
if (isset($_GET['add'])) {
    $book_id = (int)$_GET['add'];
    
    // Validate book
    $stmt = $pdo->prepare("SELECT id, title, price, stock FROM books WHERE id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch();
    
    if ($book && $book['stock'] > 0) {
        if (isset($_SESSION['cart'][$book_id])) {
            if ($_SESSION['cart'][$book_id]['quantity'] < $book['stock']) {
                $_SESSION['cart'][$book_id]['quantity']++;
            }
        } else {
            $_SESSION['cart'][$book_id] = [
                'title' => $book['title'],
                'price' => $book['price'],
                'quantity' => 1
            ];
        }
    }
    redirect('cart.php');
}

// Remove from cart
if (isset($_GET['remove'])) {
    $book_id = (int)$_GET['remove'];
    unset($_SESSION['cart'][$book_id]);
    redirect('cart.php');
}

// Empty cart
if (isset($_GET['empty'])) {
    $_SESSION['cart'] = [];
    redirect('cart.php');
}

include '../includes/header.php';
?>

<div class="animate-fade">
    <h1 style="margin-bottom: 2rem;">Your Shopping Cart</h1>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="card" style="text-align: center; padding: 4rem;">
            <i class="fas fa-shopping-cart" style="font-size: 3rem; color: var(--border); margin-bottom: 1rem;"></i>
            <h3>Your cart is empty.</h3>
            <p style="color: var(--text-muted); margin-bottom: 2rem;">Explore our collection and add some books!</p>
            <a href="../index.php" class="btn btn-primary">Browse Books</a>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
            <div class="card">
                <div class="table-container" style="border: none;">
                    <table>
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $grand_total = 0;
                            foreach ($_SESSION['cart'] as $id => $item): 
                                $total = $item['price'] * $item['quantity'];
                                $grand_total += $total;
                            ?>
                                <tr>
                                    <td style="font-weight: 600;"><?php echo htmlspecialchars($item['title']); ?></td>
                                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td style="font-weight: 700;">$<?php echo number_format($total, 2); ?></td>
                                    <td>
                                        <a href="?remove=<?php echo $id; ?>" style="color: var(--accent);"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div style="margin-top: 2rem; display: flex; justify-content: space-between;">
                    <a href="../index.php" class="btn btn-outline">Continue Shopping</a>
                    <a href="?empty=1" class="btn btn-outline" style="color: var(--accent);">Empty Cart</a>
                </div>
            </div>

            <div class="card" style="height: fit-content;">
                <h3 style="margin-bottom: 1.5rem;">Order Summary</h3>
                <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border);">
                    <span style="color: var(--text-muted);">Subtotal</span>
                    <span style="font-weight: 600;">$<?php echo number_format($grand_total, 2); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border);">
                    <span style="color: var(--text-muted);">Shipping</span>
                    <span style="color: var(--success); font-weight: 600;">FREE</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 2rem;">
                    <span style="font-size: 1.25rem; font-weight: 800;">Total</span>
                    <span style="font-size: 1.25rem; font-weight: 800; color: var(--primary);">$<?php echo number_format($grand_total, 2); ?></span>
                </div>
                <a href="checkout.php" class="btn btn-primary" style="width: 100%; padding: 1rem;">Proceed to Checkout</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>

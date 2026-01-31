<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookStore - Online Bookstore</title>
    <link rel="stylesheet" href="/Bookstore/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="container nav-container">
            <a href="/Bookstore/index.php" class="logo">
                <img src="/Bookstore/assets/images/logo.png" alt="BookStore Logo" style="height: 45px; width: 45px; object-fit: contain;"> BookStore
            </a>
            <nav class="nav-links">
                <a href="/Bookstore/index.php">Browse Books</a>
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <a href="/Bookstore/admin/dashboard.php">Admin Panel</a>
                    <?php else: ?>
                        <a href="/Bookstore/user/orders.php">My Orders</a>
                    <?php endif; ?>
                    <a href="/Bookstore/user/cart.php" class="btn btn-outline">
                        <i class="fas fa-shopping-cart"></i>
                    </a>
                    <a href="/Bookstore/auth/logout.php" class="btn btn-outline">Logout</a>
                <?php else: ?>
                    <a href="/Bookstore/auth/login.php">Login</a>
                    <a href="/Bookstore/auth/register.php" class="btn btn-primary">Sign Up</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main class="container" style="padding: 2rem 1.5rem;">

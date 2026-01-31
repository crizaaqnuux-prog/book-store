<?php
require_once '../config.php';

if (!isAdmin()) {
    redirect('../auth/login.php');
}

// Stats
$user_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$book_count = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
$order_count = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$revenue = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status = 'completed'")->fetchColumn() ?: 0;

include '../includes/header.php';
?>

<div class="animate-up">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">
        <div>
            <h1 style="font-size: 2.5rem; font-weight: 800; letter-spacing: -0.02em;">Admin Overview</h1>
            <p style="color: var(--text-muted); font-size: 1.1rem;">Welcome back, <?php echo $_SESSION['username']; ?>. Here's what's happening today.</p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <a href="reports.php" class="btn btn-outline"><i class="fas fa-file-export"></i> Reports</a>
            <a href="manage_books.php?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> New Book</a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 2rem; margin-bottom: 4rem;">
        <div class="card stat-card">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
                <div style="background: #eef2ff; color: var(--primary); padding: 1rem; border-radius: 12px; font-size: 1.25rem;">
                    <i class="fas fa-users"></i>
                </div>
                <span style="color: var(--success); font-weight: 700; font-size: 0.875rem;">+12% <i class="fas fa-arrow-up"></i></span>
            </div>
            <h2 style="font-size: 2rem; font-weight: 800; margin-bottom: 0.25rem;"><?php echo $user_count; ?></h2>
            <p style="color: var(--text-muted); font-weight: 600; font-size: 0.9rem;">Total Customers</p>
        </div>
        
        <div class="card stat-card">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
                <div style="background: #fffbeb; color: #d97706; padding: 1rem; border-radius: 12px; font-size: 1.25rem;">
                    <i class="fas fa-book"></i>
                </div>
            </div>
            <h2 style="font-size: 2rem; font-weight: 800; margin-bottom: 0.25rem;"><?php echo $book_count; ?></h2>
            <p style="color: var(--text-muted); font-weight: 600; font-size: 0.9rem;">Live Inventory</p>
        </div>

        <div class="card stat-card">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
                <div style="background: #f0fdf4; color: #16a34a; padding: 1rem; border-radius: 12px; font-size: 1.25rem;">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <span style="color: var(--success); font-weight: 700; font-size: 0.875rem;">+8% <i class="fas fa-arrow-up"></i></span>
            </div>
            <h2 style="font-size: 2rem; font-weight: 800; margin-bottom: 0.25rem;"><?php echo $order_count; ?></h2>
            <p style="color: var(--text-muted); font-weight: 600; font-size: 0.9rem;">Active Orders</p>
        </div>

        <div class="card stat-card">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
                <div style="background: #fdf2f8; color: #db2777; padding: 1rem; border-radius: 12px; font-size: 1.25rem;">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <span style="color: var(--success); font-weight: 700; font-size: 0.875rem;">+24% <i class="fas fa-arrow-up"></i></span>
            </div>
            <h2 style="font-size: 2rem; font-weight: 800; margin-bottom: 0.25rem;">$<?php echo number_format($revenue, 2); ?></h2>
            <p style="color: var(--text-muted); font-weight: 600; font-size: 0.9rem;">Total Revenue</p>
        </div>
    </div>

    <h3 style="margin-bottom: 2rem; font-weight: 800; display: flex; align-items: center; gap: 0.75rem;">
        <i class="fas fa-bolt" style="color: #f59e0b;"></i> Management Hub
    </h3>
    
    <div class="action-grid" style="margin-bottom: 4rem;">
        <a href="manage_orders.php" class="action-btn">
            <i class="fas fa-truck-loading"></i>
            <span>Order Logistics</span>
        </a>
        <a href="manage_books.php" class="action-btn">
            <i class="fas fa-book-medical"></i>
            <span>Books Database</span>
        </a>
        <a href="manage_users.php" class="action-btn">
            <i class="fas fa-user-shield"></i>
            <span>User Access</span>
        </a>
        <a href="reports.php" class="action-btn">
            <i class="fas fa-chart-pie"></i>
            <span>Sales Analytics</span>
        </a>
    </div>

    <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 2.5rem;">
        <div class="card">
            <h3 style="margin-bottom: 2rem; font-weight: 800;">Recent Platform Activity</h3>
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <div style="width: 10px; height: 10px; border-radius: 50%; background: var(--success);"></div>
                    <p style="font-size: 0.95rem; font-weight: 500;">New order #<?php echo rand(1000, 9999); ?> received</p>
                    <span style="margin-left: auto; font-size: 0.8rem; color: var(--text-muted);">2 mins ago</span>
                </div>
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <div style="width: 10px; height: 10px; border-radius: 50%; background: var(--primary);"></div>
                    <p style="font-size: 0.95rem; font-weight: 500;">Stock updated for "The Great Gatsby"</p>
                    <span style="margin-left: auto; font-size: 0.8rem; color: var(--text-muted);">15 mins ago</span>
                </div>
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <div style="width: 10px; height: 10px; border-radius: 50%; background: #f59e0b;"></div>
                    <p style="font-size: 0.95rem; font-weight: 500;">Admin login detected from 192.168.1.1</p>
                    <span style="margin-left: auto; font-size: 0.8rem; color: var(--text-muted);">1 hour ago</span>
                </div>
            </div>
        </div>

        <div class="card" style="background: linear-gradient(135deg, rgba(79, 70, 229, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%);">
            <h3 style="margin-bottom: 1.5rem; font-weight: 800;">System Intelligence</h3>
            <p style="color: var(--text-muted); margin-bottom: 2rem; font-size: 0.95rem;">All systems are nominal. <b>99.9%</b> uptime achieved this month.</p>
            <div class="btn btn-primary" style="width: 100%;">Run System Diagnostic</div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

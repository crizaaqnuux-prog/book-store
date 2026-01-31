<?php
require_once '../config.php';

if (isLoggedIn()) {
    redirect('../index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            if ($user['role'] === 'admin') {
                redirect('../admin/dashboard.php');
            } else {
                redirect('../index.php');
            }
        } else {
            $error = "Invalid username or password.";
        }
    }
}

include '../includes/header.php';
?>

<div class="animate-fade" style="max-width: 400px; margin: 4rem auto;">
    <div class="card">
        <h2 style="margin-bottom: 0.5rem; text-align: center;">Welcome Back</h2>
        <p style="color: var(--text-muted); text-align: center; margin-bottom: 2rem;">Log in to your account to continue.</p>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" class="form-control" required placeholder="Enter your username">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required placeholder="Enter your password">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Log In</button>
        </form>
        
        <p style="text-align: center; margin-top: 1.5rem; font-size: 0.875rem;">
            Don't have an account? <a href="register.php" style="color: var(--primary); font-weight: 600;">Sign up now</a>
        </p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

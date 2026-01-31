<?php
require_once '../config.php';

if (isLoggedIn()) {
    redirect('../index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username) || empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $error = "Username or email already exists.";
        } else {
            // Hash password and insert
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $email, $hashed_password])) {
                $_SESSION['success'] = "Registration successful! You can now login.";
                redirect('login.php');
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}

include '../includes/header.php';
?>

<div class="animate-fade" style="max-width: 450px; margin: 2rem auto;">
    <div class="card">
        <h2 style="margin-bottom: 0.5rem; text-align: center;">Create Account</h2>
        <p style="color: var(--text-muted); text-align: center; margin-bottom: 2rem;">Join our community of book lovers.</p>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" class="form-control" required placeholder="Choose a username">
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" required placeholder="your@email.com">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required placeholder="Create a strong password">
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required placeholder="Repeat your password">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Create Account</button>
        </form>
        
        <p style="text-align: center; margin-top: 1.5rem; font-size: 0.875rem;">
            Already have an account? <a href="login.php" style="color: var(--primary); font-weight: 600;">Log in here</a>
        </p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

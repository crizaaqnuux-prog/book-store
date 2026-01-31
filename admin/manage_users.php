<?php
require_once '../config.php';

if (!isAdmin()) {
    redirect('../auth/login.php');
}

$id = isset($_GET['delete']) ? (int)$_GET['delete'] : 0;
$message = '';

if ($id) {
    if ($id == $_SESSION['user_id']) {
        $message = "You cannot delete yourself!";
    } else {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $message = "User deleted successfully!";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {
    $user_id = (int)$_POST['user_id'];
    $role = $_POST['role'];
    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->execute([$role, $user_id]);
    $message = "User role updated!";
}

$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();

include '../includes/header.php';
?>

<div class="animate-up">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">
        <div>
            <h1 style="font-size: 2.5rem; font-weight: 800; letter-spacing: -0.02em;">Access Governance</h1>
            <p style="color: var(--text-muted); font-size: 1.1rem;">Manage user permissions and security roles for your Bookstore team.</p>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo strpos($message, 'not') !== false ? 'error' : 'success'; ?> animate-fade"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>User Identity</th>
                    <th>Email Address</th>
                    <th>Security Role</th>
                    <th>Join Date</th>
                    <th>Account Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr <?php echo $u['id'] == $_SESSION['user_id'] ? 'style="background: rgba(79, 70, 229, 0.02);"' : ''; ?>>
                        <td>
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #e0e7ff 0%, #ede9fe 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; color: var(--primary);">
                                    <?php echo strtoupper(substr($u['username'], 0, 1)); ?>
                                </div>
                                <div>
                                    <p style="font-weight: 700; margin-bottom: -2px;"><?php echo htmlspecialchars($u['username']); ?></p>
                                    <?php if ($u['id'] == $_SESSION['user_id']): ?>
                                        <span style="font-size: 0.65rem; background: var(--primary); color: white; padding: 1px 6px; border-radius: 4px; font-weight: 800;">YOU</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td style="color: var(--text-muted); font-size: 0.9rem;"><?php echo htmlspecialchars($u['email']); ?></td>
                        <td>
                            <form method="POST" style="display: flex; gap: 0.5rem; align-items: center;">
                                <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                <select name="role" class="form-control" style="padding: 0.4rem; font-size: 0.8rem; width: 110px; height: 35px;">
                                    <option value="user" <?php echo $u['role'] === 'user' ? 'selected' : ''; ?>>Customer</option>
                                    <option value="admin" <?php echo $u['role'] === 'admin' ? 'selected' : ''; ?>>Administrator</option>
                                </select>
                                <button type="submit" name="update_role" class="btn btn-primary" style="padding: 0; width: 35px; height: 35px;"><i class="fas fa-save" style="font-size: 0.8rem;"></i></button>
                            </form>
                        </td>
                        <td style="font-size: 0.9rem; font-weight: 600; color: var(--text-muted);">
                            <?php echo date('M d, Y', strtotime($u['created_at'])); ?>
                        </td>
                        <td>
                            <a href="?delete=<?php echo $u['id']; ?>" class="btn btn-outline" 
                               style="padding: 0.5rem; width: 35px; height: 35px; color: var(--accent); opacity: <?php echo $u['id'] == $_SESSION['user_id'] ? '0.3' : '1'; ?>; cursor: <?php echo $u['id'] == $_SESSION['user_id'] ? 'not-allowed' : 'pointer'; ?>" 
                               onclick="return <?php echo $u['id'] == $_SESSION['user_id'] ? 'false' : "confirm('Revoke access for this user?')"; ?>">
                                <i class="fas fa-trash-alt" style="font-size: 0.8rem;"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

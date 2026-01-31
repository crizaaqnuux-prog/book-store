<?php
require_once '../config.php';

if (!isAdmin()) {
    redirect('../auth/login.php');
}

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_book']) || isset($_POST['edit_book'])) {
        $title = trim($_POST['title']);
        $author = trim($_POST['author']);
        $category = trim($_POST['category']);
        $price = (float)$_POST['price'];
        $stock = (int)$_POST['stock'];
        $description = trim($_POST['description']);
        $image_url = trim($_POST['image_url']);

        if (isset($_POST['add_book'])) {
            $stmt = $pdo->prepare("INSERT INTO books (title, author, category, price, stock, description, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $author, $category, $price, $stock, $description, $image_url]);
            $message = "Book added successfully!";
        } else {
            $stmt = $pdo->prepare("UPDATE books SET title = ?, author = ?, category = ?, price = ?, stock = ?, description = ?, image_url = ? WHERE id = ?");
            $stmt->execute([$title, $author, $category, $price, $stock, $description, $image_url, $id]);
            $message = "Book updated successfully!";
        }
        $action = 'list';
    }
}

if ($action === 'delete' && $id) {
    $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
    $stmt->execute([$id]);
    $message = "Book deleted successfully!";
    $action = 'list';
}

// Fetch book for editing
$book = null;
if ($action === 'edit' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$id]);
    $book = $stmt->fetch();
}

// Fetch all books for listing
$books = $pdo->query("SELECT * FROM books ORDER BY created_at DESC")->fetchAll();

include '../includes/header.php';
?>

<div class="animate-up">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">
        <div>
            <h1 style="font-size: 2.5rem; font-weight: 800; letter-spacing: -0.02em;">Inventory Explorer</h1>
            <p style="color: var(--text-muted); font-size: 1.1rem;">Manage your digital library and keep track of stock levels.</p>
        </div>
        <?php if ($action === 'list'): ?>
            <a href="?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Book</a>
        <?php else: ?>
            <a href="?action=list" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to Catalog</a>
        <?php endif; ?>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success animate-fade"><?php echo $message; ?></div>
    <?php endif; ?>

    <?php if ($action === 'list'): ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Book Information</th>
                        <th>Category</th>
                        <th>Pricing</th>
                        <th>Inventory</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $b): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <div style="width: 45px; height: 60px; background: #f1f5f9; border-radius: 6px; display: flex; align-items: center; justify-content: center; overflow: hidden; border: 1px solid var(--border);">
                                        <?php if ($b['image_url']): ?>
                                            <img src="<?php echo htmlspecialchars($b['image_url']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <i class="fas fa-book" style="color: var(--border);"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <p style="font-weight: 700; margin-bottom: 0.15rem;"><?php echo htmlspecialchars($b['title']); ?></p>
                                        <p style="font-size: 0.8rem; color: var(--text-muted);">by <?php echo htmlspecialchars($b['author']); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td><span style="background: rgba(79, 70, 229, 0.08); color: var(--primary); padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase;"><?php echo htmlspecialchars($b['category']); ?></span></td>
                            <td><p style="font-weight: 800; color: var(--text-main);">$<?php echo number_format($b['price'], 2); ?></p></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <div style="flex: 1; height: 6px; background: #f1f5f9; border-radius: 3px; width: 60px; overflow: hidden;">
                                        <div style="width: <?php echo min(($b['stock'] / 50) * 100, 100); ?>%; height: 100%; background: <?php echo $b['stock'] < 5 ? 'var(--accent)' : 'var(--success)'; ?>;"></div>
                                    </div>
                                    <span style="font-size: 0.8rem; font-weight: 700; color: <?php echo $b['stock'] < 5 ? 'var(--accent)' : 'var(--text-muted)'; ?>;"><?php echo $b['stock']; ?></span>
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="?action=edit&id=<?php echo $b['id']; ?>" class="btn btn-outline" style="padding: 0.5rem; width: 35px; height: 35px;"><i class="fas fa-pen" style="font-size: 0.8rem;"></i></a>
                                    <a href="?action=delete&id=<?php echo $b['id']; ?>" class="btn btn-outline" style="padding: 0.5rem; width: 35px; height: 35px; color: var(--accent);" onclick="return confirm('Archive this book?')"><i class="fas fa-trash-alt" style="font-size: 0.8rem;"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div style="max-width: 900px; margin: 0 auto;">
            <div class="card" style="padding: 3rem;">
                <h3 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 2rem;"><?php echo $action === 'add' ? 'Publish New Title' : 'Refine Book Details'; ?></h3>
                <form method="POST">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                        <div class="form-group">
                            <label>Book Title</label>
                            <input type="text" name="title" class="form-control" required placeholder="e.g. The Midnight Library" value="<?php echo $book ? htmlspecialchars($book['title']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Author / Writer</label>
                            <input type="text" name="author" class="form-control" required placeholder="e.g. Matt Haig" value="<?php echo $book ? htmlspecialchars($book['author']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Genre / Category</label>
                            <input type="text" name="category" class="form-control" required placeholder="e.g. Contemporary Fiction" value="<?php echo $book ? htmlspecialchars($book['category']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Cover Image URL (HD)</label>
                            <input type="text" name="image_url" class="form-control" placeholder="https://..." value="<?php echo $book ? htmlspecialchars($book['image_url']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>List Price ($)</label>
                            <input type="number" step="0.01" name="price" class="form-control" required placeholder="0.00" value="<?php echo $book ? $book['price'] : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Current Stock</label>
                            <input type="number" name="stock" class="form-control" required placeholder="0" value="<?php echo $book ? $book['stock'] : ''; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Synopsis / Description</label>
                        <textarea name="description" class="form-control" rows="5" placeholder="Tell the world what this book is about..."><?php echo $book ? htmlspecialchars($book['description']) : ''; ?></textarea>
                    </div>
                    <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                        <button type="submit" name="<?php echo $action === 'add' ? 'add_book' : 'edit_book'; ?>" class="btn btn-primary" style="flex: 1; padding: 1rem;">
                            <?php echo $action === 'add' ? '<i class="fas fa-plus"></i> Publish Book' : '<i class="fas fa-save"></i> Save Changes'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>

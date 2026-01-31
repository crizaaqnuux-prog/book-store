<?php
require_once 'config.php';

$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$category = isset($_GET['cat']) ? trim($_GET['cat']) : '';

// Fetch categories for search filter
$cat_stmt = $pdo->query("SELECT DISTINCT category FROM books");
$categories = $cat_stmt->fetchAll(PDO::FETCH_COLUMN);

// Fetch IT Books specifically
$it_stmt = $pdo->query("SELECT * FROM books WHERE category = 'IT' ORDER BY created_at DESC");
$it_books = $it_stmt->fetchAll();

// Build general search query (excluding IT books if not searched)
$sql = "SELECT * FROM books WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (title LIKE ? OR author LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($category)) {
    $sql .= " AND category = ?";
    $params[] = $category;
} else if (empty($search)) {
    // If not searching, just show non-IT books in the featured section
    $sql .= " AND category != 'IT'";
}

$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll();

include 'includes/header.php';
?>

<section class="hero animate-up">
    <div class="container" style="text-align: center; color: white;">
        <h1 style="font-size: 3.5rem; font-weight: 900; margin-bottom: 1rem;">WELCOME TO BOOKSTORE</h1>
        <p style="font-size: 1.25rem; opacity: 0.9; margin-bottom: 3rem;">Your digital gateway to thousands of stories, now with a dedicated IT hub.</p>
        
        <form action="index.php" method="GET" style="max-width: 650px; margin: 0 auto; display: flex; gap: 0.75rem; background: rgba(255,255,255,0.15); padding: 0.75rem; border-radius: 50px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
            <input type="text" name="q" class="form-control" style="background: white; border-radius: 30px; border: none; padding: 0.8rem 1.5rem;" placeholder="Search by title, author, or technology..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-primary" style="padding: 0 2.5rem; border-radius: 30px; font-weight: 800; background: white; color: var(--primary);">Search</button>
        </form>
    </div>
</section>

<div class="container">
    <!-- IT Books Section -->
    <?php if (empty($category) && empty($search) && !empty($it_books)): ?>
        <div style="margin-bottom: 5rem;">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2.5rem;">
                <div style="background: var(--primary); color: white; width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fas fa-code"></i>
                </div>
                <div>
                    <h2 style="font-size: 2rem; font-weight: 800; letter-spacing: -0.02em;">IT & Programming Hub</h2>
                    <p style="color: var(--text-muted); font-weight: 600;">Master technology with our curated collection of IT books.</p>
                </div>
            </div>
            
            <div class="book-grid">
                <?php foreach ($it_books as $book): ?>
                    <div class="book-card animate-up">
                        <div class="book-image">
                            <?php if ($book['image_url']): ?>
                                <img src="<?php echo htmlspecialchars($book['image_url']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                            <?php else: ?>
                                <i class="fas fa-laptop-code" style="font-size: 4rem; color: var(--border);"></i>
                            <?php endif; ?>
                        </div>
                        <div class="book-info">
                            <span style="font-size: 0.7rem; font-weight: 800; color: white; background: var(--primary); padding: 2px 8px; border-radius: 4px; text-transform: uppercase;">IT Specialist</span>
                            <h3 class="book-title" style="margin-top: 0.75rem;"><?php echo htmlspecialchars($book['title']); ?></h3>
                            <p class="book-author">by <?php echo htmlspecialchars($book['author']); ?></p>
                            <div class="book-footer">
                                <span class="book-price">$<?php echo number_format($book['price'], 2); ?></span>
                                <a href="/Bookstore/user/cart.php?add=<?php echo $book['id']; ?>" class="btn btn-primary" style="padding: 0.5rem 1rem;">
                                    <i class="fas fa-plus"></i> Add
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div style="height: 2px; background: linear-gradient(to right, transparent, var(--border), transparent); margin: 4rem 0;"></div>
        </div>
    <?php endif; ?>

    <!-- Featured Books Section -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">
        <div>
            <h2 style="font-size: 2rem; font-weight: 800; letter-spacing: -0.02em;"><?php echo !empty($search) ? 'Search Results' : (!empty($category) ? htmlspecialchars($category) . ' Collection' : 'Featured Books'); ?></h2>
            <p style="color: var(--text-muted); font-weight: 600;">Discover our hand-picked selection of top-rated literature.</p>
        </div>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <select onchange="location = this.value;" class="form-control" style="width: auto; padding: 0.5rem 1.5rem; border-radius: 12px; font-weight: 700;">
                <option value="index.php">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="index.php?cat=<?php echo urlencode($cat); ?>" <?php echo $category == $cat ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <?php if (empty($books)): ?>
        <div class="card" style="text-align: center; padding: 4rem;">
            <i class="fas fa-search" style="font-size: 3rem; color: var(--border); margin-bottom: 1rem;"></i>
            <h3>No books found.</h3>
            <p style="color: var(--text-muted);">Try adjusting your search or category filters.</p>
        </div>
    <?php else: ?>
        <div class="book-grid">
            <?php foreach ($books as $book): ?>
                <div class="book-card animate-fade">
                    <div class="book-image">
                        <?php if ($book['image_url']): ?>
                            <img src="<?php echo htmlspecialchars($book['image_url']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                        <?php else: ?>
                            <i class="fas fa-book" style="font-size: 4rem; color: var(--border);"></i>
                        <?php endif; ?>
                    </div>
                    <div class="book-info">
                        <span style="font-size: 0.75rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.1em;">
                            <?php echo htmlspecialchars($book['category']); ?>
                        </span>
                        <h3 class="book-title"><?php echo htmlspecialchars($book['title']); ?></h3>
                        <p class="book-author">by <?php echo htmlspecialchars($book['author']); ?></p>
                        <div class="book-footer">
                            <span class="book-price">$<?php echo number_format($book['price'], 2); ?></span>
                            <?php if ($book['stock'] > 0): ?>
                                <a href="/Bookstore/user/cart.php?add=<?php echo $book['id']; ?>" class="btn btn-primary" style="padding: 0.5rem 1rem;">
                                    <i class="fas fa-plus"></i> Add
                                </a>
                            <?php else: ?>
                                <span style="color: var(--accent); font-weight: 600; font-size: 0.875rem;">Out of Stock</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

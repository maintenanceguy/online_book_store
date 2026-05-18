<?php
session_start();
require_once 'config/database.php';

$categories = [];
$books      = [];
$cartCount  = 0;

$catResult = $conn->query("SELECT * FROM categories");
while ($row = $catResult->fetch_assoc()) $categories[] = $row;

$bookResult = $conn->query("SELECT * FROM books ORDER BY created_at DESC");
while ($row = $bookResult->fetch_assoc()) $books[] = $row;

if (isset($_SESSION['user_id'])) {
    $uid       = $_SESSION['user_id'];
    $cartStmt  = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id=?");
    $cartStmt->bind_param("i", $uid);
    $cartStmt->execute();
    $cartCount = $cartStmt->get_result()->fetch_assoc()['total'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Book Store</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<!-- NAVBAR -->
<nav>
    <div class="logo">📚 <span>Store</span></div>

    <div class="nav-links">
        <a href="index.php">Home</a>

        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="views/customer/cart.php">🛒 Cart (<?= $cartCount ?>)</a>
            <a href="views/customer/order_history.php">Orders</a>
            <a href="views/customer/profile.php">Profile</a>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="views/admin/dashboard.php">Admin</a>
            <?php endif; ?>
            <a href="controllers/logoutController.php">Logout</a>
        <?php else: ?>
            <a href="views/auth/login.php">Login</a>
            <a href="views/auth/register.php">Register</a>
        <?php endif; ?>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <h1 style="font-family:'Playfair Display',serif;">
        Discover Your Next<br>
        <span>Great Read</span>
    </h1>
    <p>Browse thousands of books across every genre. Find your next favourite masterpiece today.</p>
    <div class="hero-buttons">
        <a href="#books" class="btn btn-primary">Browse Books</a>
        <a href="#categories" class="btn btn-outline">Explore Genres</a>
    </div>
</section>

<!-- CATEGORIES -->
<section class="section" id="categories">
    <h2 class="section-title" style="font-family:'Playfair Display',serif;">Browse Categories</h2>
    <div class="category-grid">
        <?php foreach ($categories as $cat): ?>
        <a href="views/customer/category_books.php?category_id=<?= $cat['id'] ?>"
           style="text-decoration:none;">
            <div class="category-card">
                <h3><?= htmlspecialchars($cat['name']) ?></h3>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- BOOKS -->
<section class="section" id="books">
    <h2 class="section-title" style="font-family:'Playfair Display',serif;">Featured Books</h2>
    <div class="books-grid">
        <?php foreach ($books as $book): ?>
        <div class="book-card">
            <img src="<?= $book['image_path'] ? 'public/uploads/books/'.$book['image_path'] : 'https://via.placeholder.com/300x380/1a1f2e/e2a85a?text=📖' ?>"
                 alt="<?= htmlspecialchars($book['title']) ?>">
            <div class="book-content">
                <div class="book-title"><?= htmlspecialchars($book['title']) ?></div>
                <div class="book-author">by <?= htmlspecialchars($book['author']) ?></div>
                <div class="book-price">৳<?= number_format($book['price'], 2) ?></div>
                <div class="stock <?= $book['stock'] > 0 ? '' : 'out' ?>">
                    <?= $book['stock'] > 0 ? '✓ In Stock' : '✗ Out of Stock' ?>
                </div>
                <div class="card-buttons">
                    <?php if ($book['stock'] > 0): ?>
                        <button class="small-btn cart-btn" onclick="addToCart(<?= $book['id'] ?>)">
                            Add to Cart
                        </button>
                    <?php endif; ?>
                    <a href="views/customer/book_details.php?id=<?= $book['id'] ?>"
                       class="small-btn view-btn">View</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <p>© 2026 Online Book Store · Full Stack Web Technologies Project</p>
</footer>

<script>
function addToCart(bookId) {

    fetch('api/add_to_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `book_id=${bookId}&quantity=1`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {

            const cartLink = document.querySelector('.nav-links a[href*="cart"]');
            if (cartLink) cartLink.textContent = `🛒 Cart (${data.cart_count})`;
        }
        alert(data.message);
    })
    .catch(() => alert('Could not add to cart. Please try again.'));
}
</script>

</body>
</html>

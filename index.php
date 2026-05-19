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
    $uid      = $_SESSION['user_id'];
    $cartStmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id=?");
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

    <!-- SEARCH BOX on home page -->
    <div style="display:flex; gap:10px; justify-content:center; flex-wrap:wrap; margin-bottom:28px;">
        <input type="text" id="homeSearch" placeholder="Search books by title, author..."
               style="width:320px; margin:0; background:#1a1f2e; border-color:#2a2f3e;"
               onkeydown="if(event.key==='Enter') goSearch()" oninput="debounceHome()">
        <select id="homeFilter" style="width:140px; margin:0; background:#1a1f2e; border-color:#2a2f3e;">
            <option value="all">All Fields</option>
            <option value="title">By Title</option>
            <option value="author">By Author</option>
        </select>
        <button onclick="goSearch()" style="padding:11px 24px;">Search</button>
    </div>

    <div class="hero-buttons">
        <a href="views/customer/books.php" class="btn btn-primary">Browse All Books</a>
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

<!-- FEATURED BOOKS -->
<section class="section" id="books">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:28px; flex-wrap:wrap; gap:12px;">
        <h2 class="section-title" style="font-family:'Playfair Display',serif; margin-bottom:0;">Featured Books</h2>
        <a href="views/customer/books.php" style="color:#e2a85a; font-size:0.9rem;">View all books →</a>
    </div>

    <div id="homeBookList" class="books-grid">
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

    <div id="homeSearchStatus" style="font-size:0.85rem; color:#64748b; margin:8px 0; min-height:18px;"></div>
</section>

<footer>
    <p>© 2026 Online Book Store · Web Technologies Project</p>
</footer>

<script>
let homeDebounce = null;

function debounceHome() {
    clearTimeout(homeDebounce);
    homeDebounce = setTimeout(goSearch, 400);
}

function goSearch() {
    const q      = document.getElementById('homeSearch').value.trim();
    const filter = document.getElementById('homeFilter').value;
    const status = document.getElementById('homeSearchStatus');

    if (!q) {
        status.textContent = '';
        location.href = 'views/customer/books.php';
        return;
    }

    status.textContent = 'Searching...';

    fetch('api/search_books.php?q=' + encodeURIComponent(q) + '&filter=' + encodeURIComponent(filter))
        .then(r => r.json())
        .then(data => {
            const count = data.length;
            status.textContent = count + ' result' + (count !== 1 ? 's' : '') + ' for "' + q + '"';

            if (count === 0) {
                document.getElementById('homeBookList').innerHTML =
                    '<div class="card" style="grid-column:1/-1; text-align:center; padding:40px;">' +
                    '<p style="color:#64748b; margin-bottom:16px;">No books match "' + q + '".</p>' +
                    '<a href="views/customer/books.php"><button>Browse All Books</button></a></div>';
                return;
            }

            document.getElementById('homeBookList').innerHTML = data.map(book => {
                const img   = book.image_path ? 'public/uploads/books/' + book.image_path
                                              : 'https://via.placeholder.com/300x380/1a1f2e/e2a85a?text=📖';
                const title  = book.title.replace(/</g,'&lt;').replace(/>/g,'&gt;');
                const author = book.author.replace(/</g,'&lt;').replace(/>/g,'&gt;');
                const stock  = parseInt(book.stock);
                const price  = parseFloat(book.price).toFixed(2);
                return `
                <div class="book-card">
                    <img src="${img}" alt="${title}">
                    <div class="book-content">
                        <div class="book-title">${title}</div>
                        <div class="book-author">by ${author}</div>
                        <div class="book-price">৳${price}</div>
                        <div class="stock ${stock > 0 ? '' : 'out'}">${stock > 0 ? '✓ In Stock' : '✗ Out of Stock'}</div>
                        <div class="card-buttons">
                            ${stock > 0 ? `<button class="small-btn cart-btn" onclick="addToCart(${book.id})">Add to Cart</button>` : ''}
                            <a href="views/customer/book_details.php?id=${book.id}" class="small-btn view-btn">View</a>
                        </div>
                    </div>
                </div>`;
            }).join('');
        })
        .catch(() => {
            status.textContent = 'Search failed. Please try again.';
        });
}

function addToCart(bookId) {
    fetch('api/add_to_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'book_id=' + bookId + '&quantity=1'
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const cartLink = document.querySelector('.nav-links a[href*="cart"]');
            if (cartLink) cartLink.textContent = '🛒 Cart (' + data.cart_count + ')';
        }
        alert(data.message);
    })
    .catch(() => alert('Could not add to cart. Please try again.'));
}
</script>

</body>
</html>

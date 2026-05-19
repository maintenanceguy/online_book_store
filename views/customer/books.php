<?php
require_once '../../config/database.php';
require_once '../../config/helpers.php';

$books      = $conn->query("
    SELECT books.*, categories.name AS category_name
    FROM books
    JOIN categories ON books.category_id = categories.id
    ORDER BY books.id DESC
");

$categories = $conn->query("SELECT * FROM categories ORDER BY name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Books</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<nav>
    <div class="logo">📚 <span>BookStore</span></div>
    <div class="nav-links">
        <a href="../../index.php">Home</a>
        <a href="books.php">Books</a>
        <?php if (isLoggedIn()): ?>
            <a href="cart.php">🛒 Cart</a>
            <a href="order_history.php">Orders</a>
            <a href="profile.php">Profile</a>
            <?php if (isAdmin()): ?><a href="../admin/dashboard.php">Admin</a><?php endif; ?>
            <a href="../../controllers/logoutController.php">Logout</a>
        <?php else: ?>
            <a href="../auth/login.php">Login</a>
            <a href="../auth/register.php">Register</a>
        <?php endif; ?>
    </div>
</nav>

<div class="container">

    <h1>Browse Books</h1>

    <div class="card" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <input type="text" id="searchInput" placeholder="Search by title or author..."
               style="flex:1; min-width:200px; margin:0;" oninput="debounceSearch()" onkeydown="if(event.key==='Enter') searchBooks()">

        <select id="filterType" style="width:140px; margin:0;" onchange="searchBooks()">
            <option value="all">All Fields</option>
            <option value="title">By Title</option>
            <option value="author">By Author</option>
        </select>

        <select id="categoryFilter" style="width:180px; margin:0;" onchange="searchBooks()">
            <option value="">All Categories</option>
            <?php while ($cat = $categories->fetch_assoc()): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endwhile; ?>
        </select>

        <button onclick="searchBooks()" style="margin:0;">Search</button>
        <button onclick="clearSearch()" style="margin:0; background:#2a2f3e; color:#e2e8f0;">Clear</button>
    </div>

    <div id="searchStatus" style="font-size:0.85rem; color:#64748b; margin:8px 0; min-height:18px;"></div>

    <div class="books-grid" id="bookList">
        <?php while ($book = $books->fetch_assoc()): ?>
        <div class="book-card">
            <img src="<?= $book['image_path'] ? '../../public/uploads/books/'.$book['image_path'] : 'https://via.placeholder.com/300x380/1a1f2e/e2a85a?text=📖' ?>"
                 alt="<?= htmlspecialchars($book['title']) ?>">
            <div class="book-content">
                <div class="book-title"><?= htmlspecialchars($book['title']) ?></div>
                <div class="book-author">by <?= htmlspecialchars($book['author']) ?></div>
                <div style="font-size:0.78rem; color:#475569; margin-bottom:8px;"><?= htmlspecialchars($book['category_name']) ?></div>
                <div class="book-price">৳<?= number_format($book['price'], 2) ?></div>
                <a href="book_details.php?id=<?= $book['id'] ?>">
                    <button style="width:100%;">View Details</button>
                </a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

</div>

<?php require_once '../layouts/footer.php'; ?>

<script>
let debounceTimer = null;

function debounceSearch() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(searchBooks, 350);
}

function searchBooks() {
    const q        = document.getElementById('searchInput').value.trim();
    const filter   = document.getElementById('filterType').value;
    const category = document.getElementById('categoryFilter').value;
    const status   = document.getElementById('searchStatus');

    status.textContent = 'Searching...';

    const url = '../../api/search_books.php'
        + '?q='        + encodeURIComponent(q)
        + '&filter='   + encodeURIComponent(filter)
        + '&category=' + encodeURIComponent(category);

    fetch(url)
        .then(r => {
            if (!r.ok) throw new Error('Server error: ' + r.status);
            return r.json();
        })
        .then(data => {
            if (data.error) {
                status.textContent = 'Error: ' + data.error;
                return;
            }

            const count = data.length;
            status.textContent = count === 0
                ? 'No books found.'
                : count + ' book' + (count !== 1 ? 's' : '') + ' found.';

            if (count === 0) {
                document.getElementById('bookList').innerHTML =
                    '<div class="card" style="grid-column:1/-1; text-align:center; padding:40px;">' +
                    '<p style="color:#64748b; margin-bottom:16px;">No books match your search.</p>' +
                    '<button onclick="clearSearch()">Show All Books</button></div>';
                return;
            }

            document.getElementById('bookList').innerHTML = data.map(book => {
                const img = book.image_path
                    ? '../../public/uploads/books/' + book.image_path
                    : 'https://via.placeholder.com/300x380/1a1f2e/e2a85a?text=📖';
                const title  = book.title.replace(/</g,'&lt;').replace(/>/g,'&gt;');
                const author = book.author.replace(/</g,'&lt;').replace(/>/g,'&gt;');
                const cat    = (book.category_name || '').replace(/</g,'&lt;').replace(/>/g,'&gt;');
                const price  = parseFloat(book.price).toFixed(2);
                return `
                <div class="book-card">
                    <img src="${img}" alt="${title}">
                    <div class="book-content">
                        <div class="book-title">${title}</div>
                        <div class="book-author">by ${author}</div>
                        <div style="font-size:0.78rem; color:#475569; margin-bottom:8px;">${cat}</div>
                        <div class="book-price">৳${price}</div>
                        <a href="book_details.php?id=${book.id}">
                            <button style="width:100%;">View Details</button>
                        </a>
                    </div>
                </div>`;
            }).join('');
        })
        .catch(err => {
            status.textContent = 'Search failed. Please try again.';
            console.error('Search error:', err);
        });
}

function clearSearch() {
    document.getElementById('searchInput').value   = '';
    document.getElementById('filterType').value    = 'all';
    document.getElementById('categoryFilter').value = '';
    document.getElementById('searchStatus').textContent = '';
    searchBooks();
}
</script>

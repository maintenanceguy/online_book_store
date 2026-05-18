<?php
require_once '../../config/database.php';
require_once '../../config/helpers.php';

if (!isset($_GET['category_id'])) {
    redirect('../../index.php');
}

$category_id   = (int) $_GET['category_id'];
$categoryStmt  = $conn->prepare("SELECT * FROM categories WHERE id = ?");
$categoryStmt->bind_param("i", $category_id);
$categoryStmt->execute();
$category = $categoryStmt->get_result()->fetch_assoc();

if (!$category) {
    redirect('../../index.php');
}

$booksStmt = $conn->prepare("
    SELECT books.*, categories.name AS category_name
    FROM books
    JOIN categories ON books.category_id = categories.id
    WHERE books.category_id = ?
    ORDER BY books.id DESC
");
$booksStmt->bind_param("i", $category_id);
$booksStmt->execute();
$books = $booksStmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($category['name']) ?> Books</title>
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
    <h1><?= htmlspecialchars($category['name']) ?> Books</h1>

    <?php if ($books->num_rows === 0): ?>
        <div class="card" style="text-align:center; padding:50px;">
            <p style="color:#64748b; margin-bottom:20px;">No books found in this category yet.</p>
            <a href="books.php"><button>Browse All Books</button></a>
        </div>
    <?php else: ?>

    <div class="books-grid">
        <?php while ($book = $books->fetch_assoc()): ?>
        <div class="book-card">
            <img src="<?= $book['image_path'] ? '../../public/uploads/books/'.$book['image_path'] : 'https://via.placeholder.com/300x380/1a1f2e/e2a85a?text=📖' ?>"
                 alt="<?= htmlspecialchars($book['title']) ?>">
            <div class="book-content">
                <div class="book-title"><?= htmlspecialchars($book['title']) ?></div>
                <div class="book-author">by <?= htmlspecialchars($book['author']) ?></div>
                <p style="font-size:0.8rem; color:#475569; margin-bottom:10px; line-height:1.5;">
                    <?= htmlspecialchars(substr($book['description'] ?? '', 0, 80)) ?>...
                </p>
                <div class="book-price">৳<?= number_format($book['price'], 2) ?></div>
                <div class="stock <?= $book['stock'] > 0 ? '' : 'out' ?>">
                    <?= $book['stock'] > 0 ? '✓ In Stock' : '✗ Out of Stock' ?>
                </div>
                <a href="book_details.php?id=<?= $book['id'] ?>">
                    <button style="width:100%;">View Details</button>
                </a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <?php endif; ?>
</div>

<?php require_once '../layouts/footer.php'; ?>

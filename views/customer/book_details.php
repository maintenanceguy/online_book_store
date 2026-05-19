<?php
require_once '../../config/database.php';
require_once '../../config/helpers.php';

if (!isset($_GET['id'])) {
    redirect('books.php');
}

$id   = (int) $_GET['id'];
$stmt = $conn->prepare("
    SELECT books.*, categories.name AS category_name
    FROM books
    JOIN categories ON books.category_id = categories.id
    WHERE books.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();

if (!$book) {
    redirect('books.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($book['title']) ?></title>
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
    <div class="card" style="display:flex; gap:36px; flex-wrap:wrap;">

        <img src="<?= $book['image_path'] ? '../../public/uploads/books/'.$book['image_path'] : 'https://via.placeholder.com/260x360/1a1f2e/e2a85a?text=📖' ?>"
             style="width:260px; height:360px; object-fit:cover; border-radius:10px; flex-shrink:0;">

        <div style="flex:1; min-width:250px;">
            <h1 style="font-size:1.8rem;"><?= htmlspecialchars($book['title']) ?></h1>
            <p style="color:#64748b; margin-bottom:8px;">by <strong><?= htmlspecialchars($book['author']) ?></strong></p>
            <p style="color:#475569; font-size:0.85rem; margin-bottom:20px;">
                Category: <?= htmlspecialchars($book['category_name']) ?>
            </p>

            <p style="color:#94a3b8; line-height:1.8; margin-bottom:24px;">
                <?= htmlspecialchars($book['description']) ?>
            </p>

            <div class="book-price" style="font-size:1.6rem;">৳<?= number_format($book['price'], 2) ?></div>

            <p style="margin-bottom:20px;">
                <?php if ($book['stock'] > 0): ?>
                    <span class="stock">✓ In Stock (<?= $book['stock'] ?> available)</span>
                <?php else: ?>
                    <span class="stock out">✗ Out of Stock</span>
                <?php endif; ?>
            </p>

            <?php if (isLoggedIn() && $book['stock'] > 0): ?>
                <div style="display:flex; gap:12px; align-items:center; margin-bottom:12px;">
                    <label style="margin:0; white-space:nowrap;">Quantity:</label>
                    <input type="number" id="quantity" value="1" min="1"
                           max="<?= $book['stock'] ?>"
                           style="width:90px; margin:0;">
                    <button onclick="addToCart(<?= $book['id'] ?>)">Add to Cart</button>
                </div>
                <p id="cartMessage" style="font-size:0.88rem;"></p>

            <?php elseif (!isLoggedIn()): ?>
                <p><a href="../auth/login.php">Login</a> to add this book to your cart.</p>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php require_once '../layouts/footer.php'; ?>

<script>
function addToCart(bookId) {
    const qty = document.getElementById("quantity").value;
    if (qty < 1) { alert("Quantity must be at least 1"); return; }

    fetch("../../api/add_to_cart.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "book_id=" + bookId + "&quantity=" + qty
    })
    .then(r => r.json())
    .then(data => {
        const msg = document.getElementById("cartMessage");
        msg.style.color = data.success ? "#4ade80" : "#f87171";
        msg.textContent = data.success ? data.message + " (Cart: " + data.cart_count + " items)" : data.message;
    });
}
</script>

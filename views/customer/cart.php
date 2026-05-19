<?php
require_once '../../config/database.php';
require_once '../../config/helpers.php';

if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

$user_id = $_SESSION['user_id'];
$stmt    = $conn->prepare("
    SELECT cart.*, books.title, books.price, books.image_path, books.stock
    FROM cart
    JOIN books ON cart.book_id = books.id
    WHERE cart.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cartItems = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<nav>
    <div class="logo">📚 <span>BookStore</span></div>
    <div class="nav-links">
        <a href="../../index.php">Home</a>
        <a href="books.php">Books</a>
        <a href="cart.php">🛒 Cart</a>
        <a href="order_history.php">Orders</a>
        <a href="profile.php">Profile</a>
        <a href="../../controllers/logoutController.php">Logout</a>
    </div>
</nav>

<div class="container">
    <h1>My Cart</h1>

    <?php
    $total    = 0;
    $itemRows = [];
    while ($item = $cartItems->fetch_assoc()) {
        $item['subtotal'] = $item['price'] * $item['quantity'];
        $total += $item['subtotal'];
        $itemRows[] = $item;
    }
    ?>

    <?php if (empty($itemRows)): ?>
        <div class="card" style="text-align:center; padding:50px;">
            <p style="color:#64748b; margin-bottom:20px;">Your cart is empty.</p>
            <a href="books.php"><button>Browse Books</button></a>
        </div>
    <?php else: ?>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Cover</th>
                    <th>Book</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($itemRows as $item): ?>
                <tr id="row-<?= $item['id'] ?>">
                    <td>
                        <?php if ($item['image_path']): ?>
                            <img src="../../public/uploads/books/<?= $item['image_path'] ?>"
                                 style="width:50px; height:66px; object-fit:cover; border-radius:4px;">
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($item['title']) ?></td>
                    <td>৳<?= number_format($item['price'], 2) ?></td>
                    <td>
                        <div style="display:flex; gap:8px; align-items:center;">
                            <input type="number" value="<?= $item['quantity'] ?>" min="1"
                                   max="<?= $item['stock'] ?>" id="qty-<?= $item['id'] ?>"
                                   style="width:72px; margin:0;">
                            <button onclick="updateCart(<?= $item['id'] ?>)"
                                    style="padding:6px 12px; font-size:0.8rem;">Update</button>
                        </div>
                    </td>
                    <td>৳<?= number_format($item['subtotal'], 2) ?></td>
                    <td>
                        <button class="btn-danger" onclick="removeCartItem(<?= $item['id'] ?>)"
                                style="padding:6px 12px; font-size:0.8rem;">Remove</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card" style="display:flex; justify-content:space-between; align-items:center;">
        <h2>Total: <span style="color:#e2a85a;">৳<?= number_format($total, 2) ?></span></h2>
        <a href="checkout.php"><button>Proceed to Checkout →</button></a>
    </div>

    <?php endif; ?>
</div>

<?php require_once '../layouts/footer.php'; ?>

<script>
function updateCart(cartId) {
    const qty = document.getElementById("qty-" + cartId).value;
    fetch("../../api/update_cart.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "cart_id=" + cartId + "&quantity=" + qty
    })
    .then(r => r.json())
    .then(data => { if (data.success) location.reload(); else alert(data.message); });
}

function removeCartItem(cartId) {
    if (!confirm("Remove this item from cart?")) return;
    fetch("../../api/remove_cart_item.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "cart_id=" + cartId
    })
    .then(r => r.json())
    .then(data => { if (data.success) location.reload(); else alert(data.message); });
}
</script>

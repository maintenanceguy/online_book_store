<?php
require_once '../../config/database.php';
require_once '../../config/helpers.php';

if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

$user_id  = $_SESSION['user_id'];
$userStmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$userStmt->bind_param("i", $user_id);
$userStmt->execute();
$user = $userStmt->get_result()->fetch_assoc();

$cartStmt = $conn->prepare("
    SELECT cart.*, books.title, books.price
    FROM cart
    JOIN books ON cart.book_id = books.id
    WHERE cart.user_id = ?
");
$cartStmt->bind_param("i", $user_id);
$cartStmt->execute();
$cartItems = $cartStmt->get_result();

$items = [];
$total = 0;
while ($item = $cartItems->fetch_assoc()) {
    $item['subtotal'] = $item['price'] * $item['quantity'];
    $total += $item['subtotal'];
    $items[] = $item;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
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
        <a href="profile.php">Profile</a>
        <a href="../../controllers/logoutController.php">Logout</a>
    </div>
</nav>

<div class="container">

    <?php if (isset($_SESSION['errors'])): ?>
        <div class="alert alert-error">
            <?php foreach ($_SESSION['errors'] as $e): ?><p><?= $e ?></p><?php endforeach; unset($_SESSION['errors']); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($items)): ?>
        <div class="card" style="text-align:center; padding:50px;">
            <p style="color:#64748b; margin-bottom:20px;">Your cart is empty.</p>
            <a href="books.php"><button>Browse Books</button></a>
        </div>
    <?php else: ?>

    <div style="display:grid; grid-template-columns:1fr 380px; gap:28px; flex-wrap:wrap;">

        <!-- ORDER SUMMARY -->
        <div class="card">
            <h2>Order Summary</h2>
            <table>
                <thead>
                    <tr><th>Book</th><th>Qty</th><th>Subtotal</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['title']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>৳<?= number_format($item['subtotal'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div style="padding:16px 0 0; font-size:1.1rem;">
                <strong>Total: <span style="color:#e2a85a;">৳<?= number_format($total, 2) ?></span></strong>
            </div>
        </div>

        <!-- CHECKOUT FORM -->
        <div class="card">
            <h2>Payment Details</h2>
            <form method="POST" action="../../controllers/placeOrderController.php"
                  onsubmit="return validateCheckout()">

                <label>Delivery Address</label>
                <textarea name="address" id="address" rows="3"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>

                <label>Payment Method</label>
                <select name="payment_method" id="payment_method">
                    <option value="">Select method...</option>
                    <option value="Credit Card">Credit Card</option>
                    <option value="bKash">bKash</option>
                    <option value="Nagad">Nagad</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                    <option value="Cash on Delivery">Cash on Delivery</option>
                </select>

                <label>Transaction ID <span style="color:#475569;">(optional for COD)</span></label>
                <input type="text" name="transaction_id" placeholder="e.g. BKSH12345">

                <button type="submit" style="width:100%;">Place Order →</button>
            </form>
        </div>

    </div>

    <?php endif; ?>
</div>

<?php require_once '../layouts/footer.php'; ?>

<script>
function validateCheckout() {
    const address = document.getElementById("address").value.trim();
    const payment = document.getElementById("payment_method").value;
    if (!address) { alert("Delivery address is required."); return false; }
    if (!payment) { alert("Please select a payment method."); return false; }
    return true;
}
</script>

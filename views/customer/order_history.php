<?php
require_once '../../config/database.php';
require_once '../../config/helpers.php';

if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

$user_id     = $_SESSION['user_id'];
$ordersStmt  = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
$ordersStmt->bind_param("i", $user_id);
$ordersStmt->execute();
$orders = $ordersStmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
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
    <h1>My Orders</h1>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <?php if ($orders->num_rows === 0): ?>
        <div class="card" style="text-align:center; padding:50px;">
            <p style="color:#64748b; margin-bottom:20px;">You haven't placed any orders yet.</p>
            <a href="books.php"><button>Browse Books</button></a>
        </div>
    <?php endif; ?>

    <?php while ($order = $orders->fetch_assoc()):
        $order_id    = $order['id'];
        $itemsStmt   = $conn->prepare("
            SELECT order_items.*, books.title
            FROM order_items
            JOIN books ON order_items.book_id = books.id
            WHERE order_items.order_id = ?
        ");
        $itemsStmt->bind_param("i", $order_id);
        $itemsStmt->execute();
        $items = $itemsStmt->get_result();
    ?>
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
            <div>
                <h2 style="margin-bottom:4px;">Order #<?= $order['id'] ?></h2>
                <p style="color:#64748b; font-size:0.85rem;"><?= $order['order_date'] ?></p>
            </div>
            <span class="status <?= $order['status'] ?>" style="font-size:0.85rem;">
                <?= ucfirst($order['status']) ?>
            </span>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Book</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = $items->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($item['title']) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>৳<?= number_format($item['unit_price'], 2) ?></td>
                    <td>৳<?= number_format($item['unit_price'] * $item['quantity'], 2) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:16px; padding-top:16px; border-top:1px solid #1e2535; flex-wrap:wrap; gap:10px;">
            <div style="color:#64748b; font-size:0.88rem;">
                Payment: <strong style="color:#94a3b8;"><?= htmlspecialchars($order['payment_method']) ?></strong>
            </div>
            <div style="font-size:1.05rem;">
                Total: <strong style="color:#e2a85a;">৳<?= number_format($order['total_amount'], 2) ?></strong>
            </div>
        </div>
    </div>
    <?php endwhile; ?>

</div>

<?php require_once '../layouts/footer.php'; ?>

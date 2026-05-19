<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/helpers.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../../index.php');
}

$orders = $conn->query("
    SELECT
        orders.id,
        users.name AS customer_name,
        GROUP_CONCAT(books.title SEPARATOR ', ') AS book_titles,
        orders.total_amount,
        orders.status,
        orders.payment_method,
        orders.order_date
    FROM orders
    JOIN users       ON orders.user_id = users.id
    JOIN order_items ON order_items.order_id = orders.id
    JOIN books       ON order_items.book_id = books.id
    GROUP BY orders.id
    ORDER BY orders.order_date DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<div class="topbar">
    <h1>📦 Order History</h1>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="manage_books.php">Books</a>
        <a href="View_users.php">Users</a>
        <a href="../../controllers/logoutController.php">Logout</a>
    </div>
</div>

<div style="padding:36px 40px;">
    <div class="table-card">
        <h3 style="margin-bottom:20px;">All Orders</h3>
        <table>
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Books</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Date</th>
                    <th>Update Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($h = $orders->fetch_assoc()): ?>
                <tr>
                    <td>#<?= $h['id'] ?></td>
                    <td><?= htmlspecialchars($h['customer_name']) ?></td>
                    <td style="max-width:220px; font-size:0.82rem;"><?= htmlspecialchars($h['book_titles']) ?></td>
                    <td><strong>৳<?= number_format($h['total_amount'], 2) ?></strong></td>
                    <td><span class="status <?= $h['status'] ?>"><?= ucfirst($h['status']) ?></span></td>
                    <td><?= htmlspecialchars($h['payment_method']) ?></td>
                    <td style="font-size:0.82rem;"><?= $h['order_date'] ?></td>
                    <td>
                        <form method="POST" action="../../controllers/updateOrderStatusController.php"
                              style="display:flex; gap:6px; align-items:center;">
                            <input type="hidden" name="order_id" value="<?= $h['id'] ?>">
                            <select name="status" style="width:auto; margin:0; padding:5px 8px; font-size:0.8rem;">
                                <?php foreach (['pending','confirmed','shipped','delivered'] as $s): ?>
                                    <option value="<?= $s ?>" <?= $h['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" style="padding:5px 12px; font-size:0.8rem;">Save</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>

<?php

require_once '../../config/database.php';
require_once '../../config/helpers.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../../index.php');
}

$orders = $conn->query("
    SELECT 
        orders.*,
        users.name AS customer_name,
        users.email AS customer_email
    FROM orders
    JOIN users ON orders.user_id = users.id
    ORDER BY orders.id DESC
");

?>

<!DOCTYPE html>
<html>
<head>
    <title>All Orders</title>
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body>

<div class="navbar">
    <a href="../../index.php">🏠 Home</a>
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="add_book.php">➕📚 Add Book</a>
    <a href="manage_books.php">📚⚙️ Manage Books</a>
    <a href="users.php">👥 Users</a>
    <a href="orders.php">🛍️ Orders</a>
    <a href="/online_book_store/controllers/logoutController.php">🔓 Logout</a>
</div>

<div class="container">
    <h1>All Purchase History / Orders</h1>

    <table border="1" cellpadding="10" cellspacing="0" width="100%">
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Books</th>
            <th>Total</th>
            <th>Payment</th>
            <th>Status</th>
            <th>Date</th>
            <th>Update</th>
        </tr>

        <?php while($order = $orders->fetch_assoc()): ?>

            <?php
                $order_id = $order['id'];

                $itemsStmt = $conn->prepare("
                    SELECT books.title, order_items.quantity, order_items.unit_price
                    FROM order_items
                    JOIN books ON order_items.book_id = books.id
                    WHERE order_items.order_id = ?
                ");

                $itemsStmt->bind_param("i", $order_id);
                $itemsStmt->execute();
                $items = $itemsStmt->get_result();
            ?>

            <tr>
                <td>#<?php echo $order['id']; ?></td>

                <td>
                    <?php echo $order['customer_name']; ?><br>
                    <small><?php echo $order['customer_email']; ?></small>
                </td>

                <td>
                    <?php while($item = $items->fetch_assoc()): ?>
                        <?php echo $item['title']; ?>
                        × <?php echo $item['quantity']; ?>
                        <br>
                    <?php endwhile; ?>
                </td>

                <td>৳ <?php echo $order['total_amount']; ?></td>

                <td><?php echo $order['payment_method']; ?></td>

                <td><?php echo $order['status']; ?></td>

                <td><?php echo $order['order_date']; ?></td>

                <td>
                    <form method="POST" action="../../controllers/updateOrderStatusController.php">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">

                        <select name="status">
                            <option value="pending" <?php if($order['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                            <option value="confirmed" <?php if($order['status'] == 'confirmed') echo 'selected'; ?>>Confirmed</option>
                            <option value="shipped" <?php if($order['status'] == 'shipped') echo 'selected'; ?>>Shipped</option>
                            <option value="delivered" <?php if($order['status'] == 'delivered') echo 'selected'; ?>>Delivered</option>
                        </select>

                        <button type="submit">Update</button>
                    </form>
                </td>
            </tr>

        <?php endwhile; ?>
    </table>
</div>
    <?php require_once '../layouts/footer.php'; ?>
</body>
</html>
<<<<<<< HEAD
<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}

$totalUsers  = $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'];
$totalBooks  = $conn->query("SELECT COUNT(*) AS c FROM books")->fetch_assoc()['c'];
$totalOrders = $conn->query("SELECT COUNT(*) AS c FROM orders")->fetch_assoc()['c'];

$recentOrders = $conn->query("
    SELECT orders.id, users.name, orders.total_amount, orders.status, orders.order_date
    FROM orders
    JOIN users ON orders.user_id = users.id
    ORDER BY orders.order_date DESC
    LIMIT 10
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<div class="topbar">
    <h1>📊 Admin Dashboard</h1>
    <div>
        <a href="../../index.php">Home</a>
        <a href="../customer/books.php">Books</a>
        <a href="manage_books.php">Manage Books</a>
        <a href="view_orders.php">Orders</a>
        <a href="View_users.php">Users</a>
        <!-- FIX: Was pointing to '../../controller/logoutController.php' (typo: controller vs controllers) -->
        <a href="../../controllers/logoutController.php">Logout</a>
        <a href="../auth/admin_register.php" style="background:#4f46e5; padding:6px 14px; border-radius:6px; color:white; font-size:0.82rem;">+ New Admin</a>
    </div>
</div>

<div class="dashboard">

    <div class="stats-grid">
        <div class="stat-card">
            <h2><?= $totalUsers ?></h2>
            <p>Total Users</p>
        </div>
        <div class="stat-card">
            <h2><?= $totalBooks ?></h2>
            <p>Books Available</p>
        </div>
        <div class="stat-card">
            <h2><?= $totalOrders ?></h2>
            <p>Total Orders</p>
        </div>
    </div>

    <div class="table-card">
        <h2 style="margin-bottom:20px;">Recent Orders</h2>
        <table>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
            <?php while ($order = $recentOrders->fetch_assoc()): ?>
            <tr>
                <td>#<?= $order['id'] ?></td>
                <td><?= htmlspecialchars($order['name']) ?></td>
                <td>৳<?= number_format($order['total_amount'], 2) ?></td>
                <td><span class="status <?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span></td>
                <td><?= $order['order_date'] ?></td>
                <td>
                    <form method="POST" action="../../controllers/updateOrderStatusController.php"
                          style="display:flex; gap:8px; align-items:center;">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <select name="status" style="width:auto; margin:0; padding:6px 10px; font-size:0.82rem;">
                            <?php foreach (['pending','confirmed','shipped','delivered'] as $s): ?>
                                <option value="<?= $s ?>" <?= $order['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" style="padding:6px 14px; font-size:0.82rem;">Update</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

</div>

</body>
</html>
=======
<?php

require_once '../../config/database.php';
require_once '../../config/helpers.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../../index.php');
}

$totalBooks = $conn->query("SELECT COUNT(*) as total FROM books")
                    ->fetch_assoc()['total'];

$totalUsers = $conn->query("SELECT COUNT(*) as total FROM users")
                    ->fetch_assoc()['total'];

$totalOrders = $conn->query("SELECT COUNT(*) as total FROM orders")
                    ->fetch_assoc()['total'];

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body>

<div class="navbar">

    <a href="../../index.php">🏠 Home</a>
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="add_book.php">➕📚 Add Book</a>
    <a href="manage_books.php">📚⚙️ Manage Books</a>
    <a href="users.php">👥 Users </a>
    <a href="orders.php">🛍️ Orders</a>
    <a href="/online_book_store/controllers/logoutController.php">🔓 Logout</a>

</div>

<div class="container">

    <h1>Admin Dashboard</h1>

    <div class="book-grid">

        <div class="book-card">
            <h2>Total Books</h2>
            <h1><?php echo $totalBooks; ?></h1>
        </div>

        <div class="book-card">
            <h2>Total Users</h2>
            <h1><?php echo $totalUsers; ?></h1>
        </div>

        <div class="book-card">
            <h2>Total Orders</h2>
            <h1><?php echo $totalOrders; ?></h1>
        </div>

    </div>

</div>
    <?php require_once '../layouts/footer.php'; ?>
</body>
</html>
>>>>>>> 39d169da5cb392a9426e101f27d5e0cc80344f93

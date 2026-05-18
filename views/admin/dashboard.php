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
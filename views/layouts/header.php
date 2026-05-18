<?php require_once '../../config/helpers.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Book Store</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<nav>
    <div class="logo">
        📚 <span>BookStore</span>
    </div>

    <div class="nav-links">
        <a href="../../index.php">Home</a>
        <a href="../customer/books.php">Books</a>

        <?php if (isLoggedIn()): ?>
            <a href="../customer/cart.php">🛒 Cart</a>
            <a href="../customer/order_history.php">Orders</a>
            <a href="../customer/profile.php">Profile</a>

            <?php if (isAdmin()): ?>
                <a href="../admin/dashboard.php">Admin</a>
            <?php endif; ?>

            <a href="../../controllers/logoutController.php">Logout</a>

        <?php else: ?>
            <a href="../auth/login.php">Login</a>
            <a href="../auth/register.php">Register</a>
        <?php endif; ?>
    </div>
</nav>

<div class="container">

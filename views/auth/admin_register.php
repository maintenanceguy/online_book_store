<?php
require_once '../../config/helpers.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<nav>
    <div class="logo">📚 <span>BookStore</span></div>
    <div class="nav-links">
        <a href="../../index.php">Home</a>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    </div>
</nav>

<div class="container">
    <div style="max-width:460px; margin:50px auto;">
        <div class="card">

            <div style="text-align:center; margin-bottom:24px;">
                <div style="font-size:2rem; margin-bottom:8px;">🔐</div>
                <h2 style="margin-bottom:4px;">Admin Registration</h2>
                <p style="color:#64748b; font-size:0.85rem;">This page is restricted. A secret key is required.</p>
            </div>

            <?php if (isset($_SESSION['errors'])): ?>
                <div class="alert alert-error">
                    <?php foreach ($_SESSION['errors'] as $e): ?><p><?= $e ?></p><?php endforeach;
                    unset($_SESSION['errors']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>

            <form method="POST" action="../../controllers/adminRegisterController.php">

                <label>Secret Admin Key</label>
                <input type="password" name="secret_key" placeholder="Enter secret key" required>

                <label>Full Name</label>
                <input type="text" name="name" placeholder="Admin name" required>

                <label>Email</label>
                <input type="email" name="email" placeholder="admin@example.com" required>

                <label>Password <span style="color:#475569;">(min 8 characters)</span></label>
                <input type="password" name="password" placeholder="Password" required>

                <label>Address</label>
                <input type="text" name="address" placeholder="Address (optional)">

                <label>Phone</label>
                <input type="text" name="phone" placeholder="Phone (optional)">

                <br>
                <button type="submit" style="width:100%; background:#4f46e5;">Create Admin Account</button>
            </form>

            <p style="text-align:center; margin-top:20px; color:#64748b; font-size:0.85rem;">
                Not an admin? <a href="register.php">Register as customer</a>
            </p>

        </div>
    </div>
</div>

<?php require_once '../layouts/footer.php'; ?>

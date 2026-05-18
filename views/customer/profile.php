<?php
require_once '../../config/database.php';
require_once '../../config/helpers.php';

if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

$user_id = $_SESSION['user_id'];
$stmt    = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
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
        <?php if (isAdmin()): ?><a href="../admin/dashboard.php">Admin</a><?php endif; ?>
        <a href="../../controllers/logoutController.php">Logout</a>
    </div>
</nav>

<div class="container">
    <div style="max-width:560px; margin:auto;">
        <div class="card">
            <h2>My Profile</h2>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>

            <?php if (isset($_SESSION['errors'])): ?>
                <div class="alert alert-error">
                    <?php foreach ($_SESSION['errors'] as $e): ?><p><?= $e ?></p><?php endforeach;
                    unset($_SESSION['errors']); ?>
                </div>
            <?php endif; ?>

            <?php if ($user['profile_picture']): ?>
                <div style="text-align:center; margin-bottom:24px;">
                    <img src="../../public/uploads/profiles/<?= htmlspecialchars($user['profile_picture']) ?>"
                         style="width:100px; height:100px; border-radius:50%; object-fit:cover; border:3px solid #2a2f3e;">
                </div>
            <?php endif; ?>

            <!-- FIX: In the original, method/action/enctype attributes were placed OUTSIDE
                 the <form> tag as plain text, so the form always GET-submitted with no action. -->
            <form method="POST"
                  action="../../controllers/profileController.php"
                  enctype="multipart/form-data">

                <label>Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

                <label>Address</label>
                <textarea name="address" rows="3"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>

                <label>Phone</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">

                <label>Profile Picture <span style="color:#475569;">(JPG/PNG, max 2MB)</span></label>
                <input type="file" name="profile_picture"
                       style="background:transparent; border:none; padding:0; color:#94a3b8;">

                <br>
                <button type="submit" style="width:100%;">Save Changes</button>
            </form>
        </div>
    </div>
</div>

<?php require_once '../layouts/footer.php'; ?>

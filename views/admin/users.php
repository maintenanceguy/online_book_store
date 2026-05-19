<?php

require_once '../../config/database.php';
require_once '../../config/helpers.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../../index.php');
}

$users = $conn->query("SELECT * FROM users ORDER BY id DESC");

?>

<!DOCTYPE html>
<html>
<head>
    <title>All Users</title>
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
    <h1>All Registered Users</h1>

    <table border="1" cellpadding="10" cellspacing="0" width="100%">
        <tr>
            <th>ID</th>
            <th>Profile</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Phone</th>
            <th>Registered</th>
            <th>Action</th>
        </tr>

        <?php while($user = $users->fetch_assoc()): ?>
            <tr>
                <td><?php echo $user['id']; ?></td>

                <td>
                    <?php if($user['profile_picture']): ?>
                        <img src="../../public/uploads/profiles/<?php echo $user['profile_picture']; ?>" width="50" height="50" style="object-fit:cover; border-radius:50%;">
                    <?php else: ?>
                        No Image
                    <?php endif; ?>
                </td>

                <td><?php echo $user['name']; ?></td>
                <td><?php echo $user['email']; ?></td>
                <td><?php echo $user['role']; ?></td>
                <td><?php echo $user['phone']; ?></td>
                <td><?php echo $user['created_at']; ?></td>

                <td>
                    <?php if($user['role'] === 'customer'): ?>
                        <a 
                            href="../../controllers/deleteCustomerController.php?id=<?php echo $user['id']; ?>"
                            onclick="return confirm('Are you sure you want to remove this customer?')"
                        >
                            Delete
                        </a>
                    <?php else: ?>
                        Admin
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>
    <?php require_once '../layouts/footer.php'; ?>
</body>
</html>
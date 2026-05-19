<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/helpers.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../../index.php');
}

$categories = [];
$catResult  = $conn->query("SELECT * FROM categories ORDER BY name ASC");
while ($row = $catResult->fetch_assoc()) $categories[] = $row;

$books = [];
$bookResult = $conn->query("
    SELECT books.*, categories.name AS category_name
    FROM books
    LEFT JOIN categories ON books.category_id = categories.id
    ORDER BY books.id DESC
");
while ($row = $bookResult->fetch_assoc()) $books[] = $row;

$editBook = null;
if (isset($_GET['edit_book'])) {
    $id = (int) $_GET['edit_book'];
    $stmt = $conn->prepare("SELECT * FROM books WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $editBook = $stmt->get_result()->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<div class="topbar">
    <h1>📚 Manage Books</h1>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="view_orders.php">Orders</a>
        <a href="View_users.php">Users</a>
        <a href="../../controllers/logoutController.php">Logout</a>
    </div>
</div>

<div style="padding:36px 40px; display:flex; gap:28px; align-items:flex-start; flex-wrap:wrap;">

    <!-- ADD / EDIT FORM -->
    <div class="card" style="width:380px; flex-shrink:0;">
        <h3><?= $editBook ? 'Edit Book' : 'Add New Book' ?></h3>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['errors'])): ?>
            <div class="alert alert-error">
                <?php foreach ($_SESSION['errors'] as $e): ?><p><?= $e ?></p><?php endforeach; unset($_SESSION['errors']); ?>
            </div>
        <?php endif; ?>

        <form method="POST"
              action="<?= $editBook ? '../../controllers/updateBookController.php' : '../../controllers/bookController.php' ?>"
              enctype="multipart/form-data"
              id="bookForm">

            <?php if ($editBook): ?>
                <input type="hidden" name="id" value="<?= $editBook['id'] ?>">
            <?php endif; ?>

            <label>Title</label>
            <input type="text" name="title" value="<?= htmlspecialchars($editBook['title'] ?? '') ?>" required>

            <label>Author</label>
            <input type="text" name="author" value="<?= htmlspecialchars($editBook['author'] ?? '') ?>" required>

            <label>Description</label>
            <textarea name="description" rows="3"><?= htmlspecialchars($editBook['description'] ?? '') ?></textarea>

            <label>Price (৳)</label>
            <input type="number" step="0.01" name="price" id="book_price"
                   value="<?= $editBook['price'] ?? '' ?>" required>

            <label>Category</label>
            <select name="category_id">
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"
                        <?= ($editBook && $editBook['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Stock</label>
            <input type="number" name="stock" value="<?= $editBook['stock'] ?? '' ?>" required>

            <label>Cover Image</label>
            <input type="file" name="image" style="background:transparent; border:none; padding:0; color:#94a3b8;">
            <?php if ($editBook && $editBook['image_path']): ?>
                <p style="font-size:0.8rem; color:#64748b;">Current: <?= $editBook['image_path'] ?></p>
            <?php endif; ?>

            <br>
            <button type="submit" style="width:100%;">
                <?= $editBook ? 'Save Changes' : 'Add Book' ?>
            </button>

            <?php if ($editBook): ?>
                <a href="manage_books.php" style="display:block; text-align:center; margin-top:12px; color:#64748b; font-size:0.85rem;">Cancel</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- BOOK LIST -->
    <div class="table-card" style="flex:1; min-width:300px;">
        <h3 style="margin-bottom:20px;">All Books (<?= count($books) ?>)</h3>
        <table>
            <thead>
                <tr>
                    <th>Cover</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $b): ?>
                <tr>
                    <td>
                        <img src="<?= $b['image_path'] ? '../../public/uploads/books/'.$b['image_path'] : 'https://via.placeholder.com/50x65/1a1f2e/e2a85a?text=📖' ?>"
                             style="width:45px; height:60px; object-fit:cover; border-radius:4px;">
                    </td>
                    <td><?= htmlspecialchars($b['title']) ?></td>
                    <td><?= htmlspecialchars($b['author']) ?></td>
                    <td>৳<?= number_format($b['price'], 2) ?></td>
                    <td><?= $b['stock'] ?></td>
                    <td>
                        <a href="manage_books.php?edit_book=<?= $b['id'] ?>" style="color:#60a5fa; margin-right:12px;">Edit</a>
                        <a href="../../controllers/deleteBookController.php?id=<?= $b['id'] ?>"
                           onclick="return confirm('Delete this book?')"
                           style="color:#ef4444;">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

<script>
document.getElementById('bookForm').addEventListener('submit', function (e) {
    const price = parseFloat(document.getElementById('book_price').value);
    if (isNaN(price) || price <= 0) {
        alert('Price must be a positive number.');
        e.preventDefault();
    }
});
</script>

</body>
</html>

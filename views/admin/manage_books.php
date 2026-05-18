<?php

require_once '../../config/database.php';
require_once '../../config/helpers.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../../index.php');
}

$books = $conn->query("
    SELECT books.*, categories.name AS category_name
    FROM books
    JOIN categories ON books.category_id = categories.id
    ORDER BY books.id DESC
");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Books</title>
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body>

<div class="navbar">

    <a href="../../index.php">🏠 Home</a>
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="add_book.php">➕📚 Add Book</a>
    <a href="manage_books.php">📚⚙️ Manage Books</a>
    <a href="/online_book_store/controllers/logoutController.php">🔓 Logout</a>

</div>

<div class="container">

    <h1>Manage Books</h1>

    <table border="1" cellpadding="10" cellspacing="0" width="100%">

        <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Title</th>
            <th>Author</th>
            <th>Category</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Action</th>
        </tr>

        <?php while($book = $books->fetch_assoc()): ?>

        <tr>

            <td><?php echo $book['id']; ?></td>

            <td>

                <?php if($book['image_path']): ?>

                    <img
                        src="../../public/uploads/books/<?php echo $book['image_path']; ?>"
                        width="70"
                    >

                <?php endif; ?>

            </td>

            <td><?php echo $book['title']; ?></td>

            <td><?php echo $book['author']; ?></td>

            <td><?php echo $book['category_name']; ?></td>

            <td>৳ <?php echo $book['price']; ?></td>

            <td><?php echo $book['stock']; ?></td>

            <td>

                <a href="edit_book.php?id=<?php echo $book['id']; ?>">
                    Edit
                </a>

                |

                <a
                    href="../../controllers/deleteBookController.php?id=<?php echo $book['id']; ?>"
                    onclick="return confirm('Delete this book?')"
                >
                    Delete
                </a>

            </td>

        </tr>

        <?php endwhile; ?>

    </table>

</div>
    <?php require_once '../layouts/footer.php'; ?>
</body>
</html>
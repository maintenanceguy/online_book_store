<?php

require_once '../config/database.php';
require_once '../config/helpers.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../index.php');
}

$id = (int) ($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM books WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

redirect('../views/admin/manage_books.php');

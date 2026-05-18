<?php

require_once '../config/database.php';
require_once '../config/helpers.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../index.php');
}

$id          = (int) $_POST['id'];
$title       = sanitize($_POST['title']);
$author      = sanitize($_POST['author']);
$description = sanitize($_POST['description']);
$price       = (float) $_POST['price'];
$stock       = (int) $_POST['stock'];
$category_id = (int) $_POST['category_id'];

if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {

    $imageName = time() . "_" . basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], "../public/uploads/books/" . $imageName);

    $sql  = "UPDATE books SET title=?, author=?, description=?, price=?, stock=?, category_id=?, image_path=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdisi", $title, $author, $description, $price, $stock, $category_id, $imageName, $id);

} else {

    $sql  = "UPDATE books SET title=?, author=?, description=?, price=?, stock=?, category_id=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdiii", $title, $author, $description, $price, $stock, $category_id, $id);
}

$stmt->execute();
redirect('../views/admin/manage_books.php');

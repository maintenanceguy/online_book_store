<?php

require_once '../config/database.php';
require_once '../config/helpers.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../index.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title       = sanitize($_POST['title']);
    $author      = sanitize($_POST['author']);
    $description = sanitize($_POST['description']);
    $price       = (float) $_POST['price'];
    $stock       = (int) $_POST['stock'];
    $category_id = (int) $_POST['category_id'];

    if (empty($title))  $errors[] = "Title required.";
    if (empty($author)) $errors[] = "Author required.";
    if ($price <= 0)    $errors[] = "Price must be positive.";
    if ($stock < 0)     $errors[] = "Invalid stock.";

    $imageName = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {

        $allowed = ['image/jpeg', 'image/png'];

        if (!in_array($_FILES['image']['type'], $allowed)) {
            $errors[] = "Only JPG and PNG allowed.";
        }
        if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            $errors[] = "Image must be less than 2MB.";
        }

        if (empty($errors)) {
            $imageName = time() . "_" . basename($_FILES['image']['name']);
            $uploadDir = __DIR__ . "/../public/uploads/books/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            move_uploaded_file(
                $_FILES['image']['tmp_name'],
                $uploadDir . $imageName
            );
        }
    }

    if (empty($errors)) {

        $sql  = "INSERT INTO books (title, author, description, price, category_id, image_path, stock)
                 VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssdisi", $title, $author, $description, $price, $category_id, $imageName, $stock);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Book added successfully.";
        } else {
            $_SESSION['errors'] = ["Failed to add book."];
        }

    } else {
        $_SESSION['errors'] = $errors;
    }

    redirect('../views/admin/manage_books.php');
}

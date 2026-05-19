<?php

require_once '../config/database.php';
require_once '../config/helpers.php';

if (!isLoggedIn()) {
    redirect('../views/auth/login.php');
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name    = sanitize($_POST['name']);
    $email   = sanitize($_POST['email']);
    $address = sanitize($_POST['address']);
    $phone   = sanitize($_POST['phone']);

    $errors = [];

    if (empty($name)) {
        $errors[] = "Name is required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email required.";
    }

    $profile_picture = null;

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {

        $allowed = ['image/jpeg', 'image/png'];

        if (!in_array($_FILES['profile_picture']['type'], $allowed)) {
            $errors[] = "Only JPG and PNG allowed.";
        }
        if ($_FILES['profile_picture']['size'] > 2 * 1024 * 1024) {
            $errors[] = "Image size must be less than 2MB.";
        }

        if (empty($errors)) {
            $uploadDir = __DIR__ . "/../public/uploads/profiles/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $filename = time() . "_" . basename($_FILES['profile_picture']['name']);
            move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadDir . $filename);
            $profile_picture = $filename;
        }
    }

    if (empty($errors)) {

        if ($profile_picture) {
            $sql  = "UPDATE users SET name=?, email=?, address=?, phone=?, profile_picture=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssi", $name, $email, $address, $phone, $profile_picture, $user_id);
        } else {
            $sql  = "UPDATE users SET name=?, email=?, address=?, phone=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $name, $email, $address, $phone, $user_id);
        }

        if ($stmt->execute()) {
            $_SESSION['name']    = $name;
            $_SESSION['success'] = "Profile updated successfully.";
        } else {
            $_SESSION['errors'] = ["Update failed."];
        }

    } else {
        $_SESSION['errors'] = $errors;
    }

    redirect('../views/customer/profile.php');
}

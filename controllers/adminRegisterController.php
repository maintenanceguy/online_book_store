<?php
require_once '../config/database.php';
require_once '../config/helpers.php';
require_once '../models/user.php';

define('ADMIN_SECRET_KEY', 'bookstore@admin2026');

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $secret   = $_POST['secret_key'] ?? '';
    $name     = sanitize($_POST['name']);
    $email    = sanitize($_POST['email']);
    $password = $_POST['password'];
    $address  = sanitize($_POST['address'] ?? '');
    $phone    = sanitize($_POST['phone'] ?? '');

    if ($secret !== ADMIN_SECRET_KEY) {
        $errors[] = "Invalid secret key.";
    }

    if (empty($name)) {
        $errors[] = "Name is required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }

    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }

    if (empty($errors) && User::findByEmail($conn, $email)) {
        $errors[] = "An account with this email already exists.";
    }

    if (empty($errors)) {

        $success = User::register($conn, $name, $email, $password, 'admin', $address, $phone);

        if ($success) {
            $_SESSION['success'] = "Admin account created successfully. Please login.";
            redirect('../views/auth/login.php');
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
    }

    $_SESSION['errors'] = $errors;
    redirect('../views/auth/admin_register.php');
}

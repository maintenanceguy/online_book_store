<?php

require_once '../config/database.php';
require_once '../config/helpers.php';
require_once '../models/user.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = sanitize($_POST['email']);
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    if (empty($errors)) {
        $user = User::findByEmail($conn, $email);

        if ($user && password_verify($password, $user['password_hash'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name']    = $user['name'];
            $_SESSION['role']    = $user['role'];

            if (isset($_POST['remember'])) {

                setcookie("remember_email", $email, time() + (86400 * 7), "/", "", false, true);
            }

            redirect('../index.php');

        } else {
            $errors[] = "Invalid email or password.";
        }
    }

    $_SESSION['errors'] = $errors;
    redirect('../views/auth/login.php');
}

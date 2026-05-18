<?php

require_once '../config/database.php';
require_once '../config/helpers.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../index.php');
}

define('ADMIN_SECRET_KEY', 'bookstore@admin2026');

$id         = (int) ($_POST['id'] ?? 0);
$secret_key = $_POST['secret_key'] ?? '';

if ($id <= 0) {
    redirect('../views/admin/View_users.php');
}

if ($secret_key !== ADMIN_SECRET_KEY) {
    $_SESSION['errors'] = ["Invalid secret key. Admin account was not deleted."];
    redirect('../views/admin/View_users.php');
}

if ((int)$id === (int)$_SESSION['user_id']) {
    $_SESSION['errors'] = ["You cannot delete your own account."];
    redirect('../views/admin/View_users.php');
}

$stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'admin'");
$stmt->bind_param("i", $id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $_SESSION['success'] = "Admin account deleted successfully.";
} else {
    $_SESSION['errors'] = ["Admin account not found or could not be deleted."];
}

redirect('../views/admin/View_users.php');

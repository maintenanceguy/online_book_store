<?php

require_once '../config/database.php';
require_once '../config/helpers.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../index.php');
}

if (!isset($_GET['id'])) {
    redirect('../views/admin/View_users.php');
}

$id = (int) $_GET['id'];

$stmt = $conn->prepare("DELETE FROM users WHERE id=? AND role='customer'");
$stmt->bind_param("i", $id);
$stmt->execute();

redirect('../views/admin/View_users.php');

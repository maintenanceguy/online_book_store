<?php
require_once '../config/database.php';
require_once '../config/helpers.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(["success" => false, "message" => "Login required."]);
    exit();
}

$user_id = $_SESSION['user_id'];
$cart_id = (int) $_POST['cart_id'];

$stmt = $conn->prepare("DELETE FROM cart WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $cart_id, $user_id);
$stmt->execute();

echo json_encode(["success" => true, "message" => "Item removed from cart."]);
?>

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
$quantity = (int) $_POST['quantity'];

if ($quantity <= 0) {
    echo json_encode(["success" => false, "message" => "Quantity must be positive."]);
    exit();
}

$stmt = $conn->prepare("
    SELECT cart.*, books.stock
    FROM cart
    JOIN books ON cart.book_id = books.id
    WHERE cart.id = ? AND cart.user_id = ?
");

$stmt->bind_param("ii", $cart_id, $user_id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();

if (!$item) {
    echo json_encode(["success" => false, "message" => "Cart item not found."]);
    exit();
}

if ($quantity > $item['stock']) {
    echo json_encode(["success" => false, "message" => "Quantity exceeds stock."]);
    exit();
}

$update = $conn->prepare("UPDATE cart SET quantity=? WHERE id=? AND user_id=?");
$update->bind_param("iii", $quantity, $cart_id, $user_id);
$update->execute();

echo json_encode(["success" => true, "message" => "Cart updated successfully."]);
?>

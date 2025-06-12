<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['customer_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Not logged in"]);
    exit();
}

$customer_id = $_SESSION['customer_id'];

// Read JSON cart items from request
$data = json_decode(file_get_contents('php://input'), true);

if (empty($data)) {
    http_response_code(400);
    echo json_encode(["error" => "No cart data received"]);
    exit();
}

// Step 1: Insert into purchases table
$purchase_date = date('Y-m-d H:i:s');
$stmt = $conn->prepare("INSERT INTO purchases (customer_id, purchase_date) VALUES (?, ?)");
$stmt->bind_param("is", $customer_id, $purchase_date);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to create purchase"]);
    exit();
}

$purchase_id = $stmt->insert_id;
$stmt->close();

// Step 2: Insert into purchase_items
$itemStmt = $conn->prepare("INSERT INTO purchase_items (purchase_id, product_name, price, quantity, image) VALUES (?, ?, ?, ?, ?)");

foreach ($data as $item) {
    $name = $item['name'];
    $price = $item['price'];
    $quantity = $item['quantity'];
    $image = $item['image'];

    $itemStmt->bind_param("isdss", $purchase_id, $name, $price, $quantity, $image);
    $itemStmt->execute();
}
$itemStmt->close();

// Step 3: Clear the cart
$delStmt = $conn->prepare("DELETE FROM cart WHERE customer_id = ?");
$delStmt->bind_param("i", $customer_id);
$delStmt->execute();
$delStmt->close();

echo json_encode(["success" => true, "purchase_id" => $purchase_id]);

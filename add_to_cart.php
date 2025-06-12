<?php
session_start();
require_once 'db.php';

// Ensure user is logged in
if (!isset($_SESSION['customer_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(["error" => "Not logged in"]);
    exit();
}

$customer_id = $_SESSION['customer_id'];

// Read JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($data['name'], $data['price'], $data['image'])) {
    http_response_code(400); // Bad request
    echo json_encode(["error" => "Missing product data"]);
    exit();
}

$product_name = trim($data['name']);
$price = floatval($data['price']);
$image = trim($data['image']);

// Check if product already exists in the cart
$stmt = $conn->prepare("SELECT cart_id, quantity FROM cart WHERE customer_id = ? AND product_name = ?");
$stmt->bind_param("is", $customer_id, $product_name);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    // Product already in cart – update quantity
    $row = $result->fetch_assoc();
    $newQty = $row['quantity'] + 1;

    $update = $conn->prepare("UPDATE cart SET quantity = ? WHERE cart_id = ?");
    $update->bind_param("ii", $newQty, $row['cart_id']);
    $update->execute();
    $update->close();
} else {
    // Insert new product
    $insert = $conn->prepare("INSERT INTO cart (customer_id, product_name, price, image, quantity, created_at) VALUES (?, ?, ?, ?, 1, NOW())");
    $insert->bind_param("isds", $customer_id, $product_name, $price, $image);
    $insert->execute();
    $insert->close();
}

$stmt->close();
echo json_encode(["success" => true]);

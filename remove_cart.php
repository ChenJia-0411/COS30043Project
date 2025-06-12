<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['customer_id'])) {
  http_response_code(401);
  echo json_encode(['error' => 'Unauthorized']);
  exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$cart_id = $data['id'] ?? null;

if (!$cart_id) {
  http_response_code(400);
  echo json_encode(['error' => 'Missing cart ID']);
  exit();
}

// Delete from DB
$stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ? AND customer_id = ?");
$stmt->bind_param("ii", $cart_id, $_SESSION['customer_id']);
$success = $stmt->execute();
$stmt->close();

echo json_encode(['success' => $success]);

<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['count' => 0]);
    exit;
}

$customer_id = $_SESSION['customer_id'];

$sql = "SELECT SUM(quantity) as total FROM cart WHERE customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

$count = $result['total'] ?? 0;
echo json_encode(['count' => (int)$count]);
?>

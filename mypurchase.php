<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

// Fetch all purchases for this customer
$purchases = [];
$purchaseQuery = $conn->prepare("SELECT purchase_id, purchase_date FROM purchases WHERE customer_id = ? ORDER BY purchase_date DESC");
$purchaseQuery->bind_param("i", $customer_id);
$purchaseQuery->execute();
$purchaseResult = $purchaseQuery->get_result();

while ($purchase = $purchaseResult->fetch_assoc()) {
    // Get items for this purchase
    $itemsQuery = $conn->prepare("SELECT product_name, price, quantity, image FROM purchase_items WHERE purchase_id = ?");
    $itemsQuery->bind_param("i", $purchase['purchase_id']);
    $itemsQuery->execute();
    $itemsResult = $itemsQuery->get_result();

    $purchase['items'] = $itemsResult->fetch_all(MYSQLI_ASSOC);
    $purchases[] = $purchase;

    $itemsQuery->close();
}

$purchaseQuery->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>My Purchases</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="css/styles.css" rel="stylesheet" />
</head>
<body>
 <!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container px-4 px-lg-5">
    <a class="navbar-brand" href="#">ShoeStore</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
        <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="products.php">Shop</a></li>
      
          <li class="nav-item"><a class="nav-link" href="myaccount.php">My Account</a></li>
            <li class="nav-item"><a class="nav-link" href="mypurchase.php">My Purchase</a></li>
          <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
          <li class="nav-item"><span class="nav-link">Welcome, <?= htmlspecialchars($_SESSION['customer_name']) ?></span></li>
      </ul>

        <form class="d-flex">
          <a href="cart.php" class="btn btn-outline-dark position-relative">
            <i class="bi-cart-fill me-1"></i> Cart
            <span id="cart-count" class="badge bg-dark text-white ms-1 rounded-pill">0</span>
          </a>
        </form>
    
    </div>
  </div>
</nav>
  <!-- Purchase History -->
  <div class="container py-5">
    <h2 class="text-center mb-4">My Purchase History</h2>

    <?php if (empty($purchases)): ?>
      <div class="alert alert-info text-center">You haven’t made any purchases yet.</div>
    <?php else: ?>
      <?php foreach ($purchases as $purchase): ?>
        <div class="card mb-4 shadow-sm">
          <div class="card-header bg-dark text-white">
            <strong>Purchase #<?= htmlspecialchars($purchase['purchase_id']) ?></strong> —
            <?= date('F j, Y, g:i A', strtotime($purchase['purchase_date'])) ?>
          </div>
          <div class="card-body">
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-3">
              <?php foreach ($purchase['items'] as $item): ?>
                <div class="col">
                  <div class="card h-100 text-center">
                    <img src="<?= htmlspecialchars($item['image']) ?>" class="card-img-top" style="height:200px; object-fit:cover;" alt="<?= htmlspecialchars($item['product_name']) ?>">
                    <div class="card-body">
                      <h5 class="card-title"><?= htmlspecialchars($item['product_name']) ?></h5>
                      <p class="mb-1">Price: $<?= number_format($item['price'], 2) ?></p>
                      <p class="mb-1">Qty: <?= $item['quantity'] ?></p>
                      <p class="fw-bold">Subtotal: $<?= number_format($item['price'] * $item['quantity'], 2) ?></p>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <footer class="py-5 bg-dark">
    <div class="container"><p class="m-0 text-center text-white">© 2025 ShoeStore. All rights reserved.</p></div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
function updateCartCount() {
  fetch('cart_count.php')
    .then(res => res.json())
    .then(data => {
      document.getElementById('cart-count').textContent = data.count;
    });
}
document.addEventListener('DOMContentLoaded', updateCartCount);
</script>
</body>
</html>

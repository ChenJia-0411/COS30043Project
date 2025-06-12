<?php
session_start();
require_once 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php');
    exit();
}

$customer_id = $_SESSION['customer_id'];
$success = '';
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']);
    $phone   = trim($_POST['phone']);
    $address = trim($_POST['address']);

    if (empty($name) || empty($phone) || empty($address)) {
        $errors[] = "All fields are required.";
    } else {
        $stmt = $conn->prepare("UPDATE customers SET name = ?, phone = ?, address = ? WHERE customer_id = ?");
        $stmt->bind_param("sssi", $name, $phone, $address, $customer_id);
        if ($stmt->execute()) {
            $success = "Profile updated successfully.";
            $_SESSION['customer_name'] = $name;
        } else {
            $errors[] = "Failed to update profile.";
        }
        $stmt->close();
    }
}

// Fetch current customer data
$stmt = $conn->prepare("SELECT email, name, phone, address FROM customers WHERE customer_id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Account - Shoe Store</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/styles.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation-->
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container px-4 px-lg-5">
      <a class="navbar-brand" href="#">ShoeStore</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
        aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span></button>
      <div class="collapse navbar-collapse" id="navbarContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
          <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="products.php">Shop</a></li>
          <?php if (isset($_SESSION['customer_id'])): ?>
            <li class="nav-item"><a class="nav-link" href="myaccount.php">My Account</a></li>
            <li class="nav-item"><a class="nav-link" href="mypurchase.php">My Purchase</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            <li class="nav-item">
              <span class="nav-link">Welcome, <?= htmlspecialchars($_SESSION['customer_name']) ?></span>
            </li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
            <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
          <?php endif; ?>

        </ul>
        <?php if (isset($_SESSION['customer_id'])): ?>
          <form class="d-flex">
          <a href="cart.php" class="btn btn-outline-dark position-relative">
            <i class="bi-cart-fill me-1"></i> Cart
            <span id="cart-count" class="badge bg-dark text-white ms-1 rounded-pill">0</span>
          </a>
        </form>
        <?php else: ?>
        <?php endif; ?>
      </div>
    </div>
  </nav>


  <!-- Page Content -->
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow">
          <div class="card-header bg-dark text-white">
            <h4 class="mb-0">My Account</h4>
          </div>
          <div class="card-body">
            <?php if (!empty($errors)): ?>
              <div class="alert alert-danger">
                <ul class="mb-0">
                  <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>

            <?php if ($success): ?>
              <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="POST" action="myaccount.php" novalidate>
              <div class="mb-3">
                <label class="form-label">Email (read-only)</label>
                <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" readonly />
              </div>
              <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" required><?= htmlspecialchars($user['address']) ?></textarea>
              </div>
              <div class="d-grid">
                <button type="submit" class="btn btn-dark">Update Profile</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="py-5 bg-dark mt-auto">
    <div class="container">
      <p class="m-0 text-center text-white">© 2025 ShoeStore. All rights reserved.</p>
    </div>
  </footer>
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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

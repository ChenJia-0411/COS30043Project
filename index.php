<?php
session_start();
require_once 'db.php'; // This will auto-create DB + tables if missing
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="Shoe Store Homepage" />
  <title>Home - Shoe Store</title>
  <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="css/styles.css" rel="stylesheet" />
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

  <!-- Hero Banner -->
  <header class="bg-dark py-5">
    <div class="container px-4 px-lg-5 my-5">
      <div class="text-center text-white">
        <h1 class="display-4 fw-bolder">Step Into Style</h1>
        <p class="lead fw-normal text-white-50 mb-0">Shop the latest shoes for every occasion</p>
      </div>
    </div>
  </header>

  <!-- Featured Categories -->
  <section class="py-5">
    <div class="container">
      <h2 class="text-center mb-4">Explore Our Categories</h2>
      <div class="row text-center">
        <div class="col-md-4 mb-4">
          <div class="card h-100 shadow">
            <img src="image/mens.jpg" class="card-img-top" alt="Men's Shoes" />
            <div class="card-body">
              <h5 class="card-title">Men's Collection</h5>
              <a href="products.php?category=men" class="btn btn-outline-dark">Shop Now</a>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card h-100 shadow">
            <img src="image/womens.jpg" class="card-img-top" alt="Women's Shoes" />
            <div class="card-body">
              <h5 class="card-title">Women's Collection</h5>
              <a href="products.php?category=women" class="btn btn-outline-dark">Shop Now</a>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card h-100 shadow">
            <img src="image/sports.jpg" class="card-img-top" alt="Sports Shoes" />
            <div class="card-body">
              <h5 class="card-title">Sports & Sneakers</h5>
              <a href="products.php?category=sports" class="btn btn-outline-dark">Shop Now</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Promo Banner -->
  <section class="py-5 bg-light">
    <div class="container text-center">
      <h3 class="mb-3">🔥 Limited Time Offer!</h3>
      <p class="lead mb-4">Get 20% off selected sneakers. Don’t miss out!</p>
      <a href="products.php" class="btn btn-dark">Browse Sale</a>
    </div>
  </section>

  <!-- Newsletter Signup -->
  <section class="py-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-6 text-center">
          <h4>Join Our Newsletter</h4>
          <p>Get updates on new arrivals and special offers.</p>
          <form>
            <div class="input-group mb-3">
              <input type="email" class="form-control" placeholder="Enter your email" aria-label="Email" required />
              <button class="btn btn-outline-dark" type="submit">Subscribe</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="py-5 bg-dark">
    <div class="container">
      <p class="m-0 text-center text-white">© 2025 ShoeStore. All rights reserved.</p>
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/scripts.js"></script>

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

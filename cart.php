<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php');
    exit();
}

$customer_id = $_SESSION['customer_id'];

// Fetch cart items
$stmt = $conn->prepare("SELECT cart_id, product_name AS name, price, quantity, image FROM cart WHERE customer_id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$cartItemsFromDB = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Shopping Cart</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="css/styles.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
  <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
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
        <form class="d-flex">
          <a href="cart.php" class="btn btn-outline-dark position-relative">
            <i class="bi-cart-fill me-1"></i> Cart
            <span id="cart-count" class="badge bg-dark text-white ms-1 rounded-pill">0</span>
          </a>
        </form>
      </div>
    </div>
  </nav>
  <div id="cartApp">
    <header class="bg-dark py-4 text-white text-center">
      <h1 class="display-5 fw-bold">Your Shopping Cart</h1>
    </header>

    <div class="container my-5">
      <div v-if="cartItems.length > 0">
        <table class="table table-bordered table-hover align-middle text-center cart-table">
          <thead class="table-dark">
            <tr>
              <th>Image</th>
              <th>Product</th>
              <th>Price</th>
              <th>Quantity</th>
              <th>Subtotal</th>
              <th>Remove</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in cartItems" :key="item.cart_id">
              <td><img :src="item.image" class="cart-img" alt="Shoe" width="80" /></td>
              <td>{{ item.name }}</td>
              <td>${{ parseFloat(item.price).toFixed(2) }}</td>
              <td>
                <input type="number" min="1" v-model.number="item.quantity" @change="updateQuantity(item)" class="form-control quantity-input" />
              </td>
              <td>${{ (item.price * item.quantity).toFixed(2) }}</td>
              <td>
                <button class="btn btn-sm btn-danger" @click="removeItem(item.cart_id)">
                  <i class="bi bi-trash"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <div class="text-end mt-4">
          <h4>Total: ${{ cartTotal.toFixed(2) }}</h4>
          <h4>Subtotal: ${{ cartTotal.toFixed(2) }}</h4>
          <h4>Total (incl. postage): ${{ totalWithPostage.toFixed(2) }}</h4>
          <button class="btn btn-success" @click="checkout">Proceed to Checkout</button>
        </div>
      </div>

      <div v-else class="text-center my-5">
        <h3>Your cart is empty.</h3>
        <a href="products.php" class="btn btn-outline-dark mt-3">Browse Products</a>
      </div>
    </div>
  </div>

  <script>
    const { createApp } = Vue;
    function updateCartCount() {
      fetch('cart_count.php')
        .then(res => res.json())
        .then(data => {
          document.getElementById('cart-count').textContent = data.count;
        });
    }
    document.addEventListener('DOMContentLoaded', updateCartCount);
    createApp({
      data() {
        return {
          cartItems: <?= json_encode($cartItemsFromDB ?? []) ?>
        };
      },
      computed: {
        cartTotal() {
          return this.cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        },
        totalWithPostage() {
          const postage = 5.00; // or calculate dynamically
          return this.cartTotal + postage;
        }
      },
      methods: {
        updateQuantity(item) {
          fetch('update_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: item.cart_id, quantity: item.quantity })
          });
        },
        removeItem(id) {
          fetch('remove_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
          }).then(() => {
            this.cartItems = this.cartItems.filter(item => item.cart_id !== id);
          });
        },
        checkout() {
          fetch('checkout.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(this.cartItems)
          }).then(res => {
            if (res.ok) {
              alert("Checkout successful!");
              window.location.href = "mypurchase.php";
            }
          });
        }
      }
    }).mount("#cartApp");
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
session_start();
//require_once 'db.php';
$isLoggedIn = isset($_SESSION['customer_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Product Page</title>
  <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="css/styles.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
  <style>
    .card-img-top {
      height: 250px;
      object-fit: cover;
    }
  </style>
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
        <?php if ($isLoggedIn): ?>
          <li class="nav-item"><a class="nav-link" href="myaccount.php">My Account</a></li>
            <li class="nav-item"><a class="nav-link" href="mypurchase.php">My Purchase</a></li>
          <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
          <li class="nav-item"><span class="nav-link">Welcome, <?= htmlspecialchars($_SESSION['customer_name']) ?></span></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
        <?php endif; ?>
      </ul>
      <?php if ($isLoggedIn): ?>
        <form class="d-flex">
          <a href="cart.php" class="btn btn-outline-dark position-relative">
            <i class="bi-cart-fill me-1"></i> Cart
            <span id="cart-count" class="badge bg-dark text-white ms-1 rounded-pill">0</span>
          </a>
        </form>
      <?php endif; ?>
    </div>
  </div>
</nav>

<div id="app">
  <!-- Header -->
  <header class="bg-dark py-4 text-white text-center">
    <h1 class="display-5 fw-bold">Browse Our Shoes</h1>
    <p class="lead">Filter by category and find your perfect pair</p>
  </header>

  <!-- Filter -->
  <div class="container my-4">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <label for="categoryFilter" class="form-label me-2">Filter by Category:</label>
        <select id="categoryFilter" v-model="selectedCategory" class="form-select w-auto d-inline-block">
          <option value="">All</option>
          <option value="Men">Men</option>
          <option value="Women">Women</option>
          <option value="Sports">Sports</option>
        </select>
      </div>
      <div>
        Showing {{ paginatedProducts.length }} of {{ filteredProducts.length }} items
      </div>
    </div>
  </div>

  <!-- Product Grid -->
  <div class="container">
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
      <div class="col" v-for="product in paginatedProducts" :key="product.id">
        <div class="card h-100 shadow-sm">
          <img :src="product.image" class="card-img-top" :alt="product.name" />
          <div class="card-body text-center">
            <h5 class="card-title">{{ product.name }}</h5>
            <p class="card-text text-muted">${{ product.price.toFixed(2) }}</p>
          </div>
          <div class="card-footer text-center border-top-0 bg-transparent">
            <button class="btn btn-outline-dark" @click="addToCart(product)">Add to cart</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <nav class="mt-4">
      <ul class="pagination justify-content-center">
        <li class="page-item" :class="{ disabled: currentPage === 1 }">
          <a class="page-link" href="#" @click.prevent="changePage(currentPage - 1)">Previous</a>
        </li>
        <li class="page-item" v-for="page in totalPages" :class="{ active: page === currentPage }">
          <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
        </li>
        <li class="page-item" :class="{ disabled: currentPage === totalPages }">
          <a class="page-link" href="#" @click.prevent="changePage(currentPage + 1)">Next</a>
        </li>
      </ul>
    </nav>
  </div>
</div>

<script>
  const { createApp } = Vue;
  const isLoggedIn = <?= json_encode($isLoggedIn) ?>;

  // ✅ Global function to update cart count (used inside and outside Vue)
  async function updateCartCount() {
  if (!isLoggedIn) return;

  try {
    const response = await fetch('cart_count.php');
    const data = await response.json();
    console.log("🛒 Cart count:", data); // ✅ Add this debug

    const badge = document.getElementById('cart-count');
    if (badge) {
      badge.textContent = data.count;
    } else {
      console.warn("⚠️ Cart badge not found!");
    }
  } catch (err) {
    console.error('❌ Failed to fetch cart count:', err);
  }
}



  // ✅ Vue app
  createApp({
    data() {
      return {
        products: [],
        currentPage: 1,
        perPage: 8,
        selectedCategory: ""
      };
    },
    computed: {
      filteredProducts() {
        return this.selectedCategory
          ? this.products.filter(p => p.category === this.selectedCategory)
          : this.products;
      },
      totalPages() {
        return Math.ceil(this.filteredProducts.length / this.perPage);
      },
      paginatedProducts() {
        const start = (this.currentPage - 1) * this.perPage;
        return this.filteredProducts.slice(start, start + this.perPage);
      }
    },
    methods: {
      changePage(page) {
        if (page >= 1 && page <= this.totalPages) {
          this.currentPage = page;
        }
      },
      async fetchProducts() {
        const response = await fetch("js/product-data.json");
        this.products = await response.json();
      },
      async addToCart(product) {
        if (!isLoggedIn) {
          alert("Please log in or register to add items to your cart.");
          window.location.href = "login.php";
          return;
        }

        const response = await fetch("add_to_cart.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            name: product.name,
            price: product.price,
            image: product.image
          })
        });

        if (response.ok) {
          alert(`${product.name} added to cart!`);
          updateCartCount(); // ✅ use global function
        } else {
          const res = await response.json();
          alert(res.error || "Failed to add to cart");
        }
      }
    },
   mounted() {
  this.fetchProducts();
  this.$nextTick(() => {
    updateCartCount();
  });
}


  }).mount("#app");
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

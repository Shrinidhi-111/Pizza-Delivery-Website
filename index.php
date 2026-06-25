<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PizzaHub - Order Fresh Pizzas</title>
<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg">
<div class="container">
<a class="navbar-brand" href="index.php">PizzaHub</a>
<button class="navbar-toggler" type="button"
data-bs-toggle="collapse" data-bs-target="#navMenu">
<span class="navbar-toggler-icon"></span>
</button>
<div class="collapse navbar-collapse" id="navMenu">
<ul class="navbar-nav ms-auto">
<li class="nav-item"><a class="nav-link" href="user/menu.php"><h5>Menu</h5></a></li>
<?php if (isset($_SESSION['user_id'])): ?>
<li class="nav-item">
<a class="nav-link" href="user/dashboard.php"><h5>Dashboard</h5></a></li>
<li class="nav-item">
<a class="nav-link" href="user/cart.php"><h5>🛒Cart</h5></a></li>
<li class="nav-item">
<a class="nav-link" href="user/logout.php"><h5>Logout</h5></a></li>
<?php else: ?>
<li class="nav-item">
<a class="nav-link" href="user/login.php"><h5>Login</h5></a></li>
<li class="nav-item">
<a class="nav-link" href="user/register.php"><h5>Register</h5></a></li>
<?php endif; ?>
</ul>
</div>
</div>
</nav>

<section class="hero" style=" height:800px; 
display:flex;
align-items:center;
text-align:center;
  background:  url('assets/images/pizza3.jpeg') center/cover no-repeat;color:white">
<div class="container">
<h1>Fresh Pizzas Delivered Fast!</h1>
<p>Order your favourite pizza from the comfort of your home.</p>
<a href="user/menu.php" class="btn-order">Order Now</a>
</div>
</section>
<section class="py-2">
<div class="container">
<h2 class="section-title">Why Choose PizzaHub?</h2>
<div class="row text-center g-4">
<div class="col-md-4">
<div class="p-4">
<h5 class="mt-3 fw-bold">Fresh Ingredients</h5>
<p class="text-muted">Made with the freshest ingredients every time.</p>
</div>
</div>
<div class="col-md-4">
<div class="p-4">
<h5 class="mt-3 fw-bold">Fast Delivery🛵</h5>
<p class="text-muted">Hot pizza at your door in 30 minutes or less.</p>
</div>
</div>
<div class="col-md-4">
<div class="p-4">
<h5 class="mt-3 fw-bold">Easy Payment💰</h5>
<p class="text-muted">Pay online or cash on delivery.</p>
</div>
</div>
</div>
</div>
</section>
<footer><p>&copy; 2026 PizzaHub. All rights reserved.</p></footer>
<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
</script>
</body>
</html>
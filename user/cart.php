
<?php
session_start();
include '../config/db.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
if (isset($_GET['remove'])) {
unset($_SESSION['cart'][$_GET['remove']]);
header("Location: cart.php"); exit();
}
if (isset($_POST['update_cart'])) {
foreach ($_POST['quantity'] as $mid => $qty) {
if ($qty > 0) { $_SESSION['cart'][$mid]['quantity'] = (int)$qty; }
else { unset($_SESSION['cart'][$mid]); }
}
header("Location: cart.php?updated=1"); exit();
}
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$cart_total = 0;
foreach ($cart as $item) $cart_total += $item['price'] * $item['quantity'];
$delivery_charge = 40;
$grand_total = $cart_total + $delivery_charge;
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><title>Cart - PizzaHub</title>
<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg">
<div class="container">
<a class="navbar-brand" href="../index.php">PizzaHub</a>
<div class="collapse navbar-collapse">
<ul class="navbar-nav ms-auto">
<li class="nav-item"><a class="nav-link" href="menu.php"><h5>Menu</h5></a></li>
<li class="nav-item"><a class="nav-link" href="dashboard.php"><h5>Dashboard</h5></a></li>

<li class="nav-item"><a class="nav-link" href="logout.php"><h5>Logout</h5></a></li>
</ul>
</div>
</div>
</nav>
<div class="page-header"><div class="container"><h2>My Cart</h2></div></div>
<div class="container mb-5">
<?php if (isset($_GET['updated'])): ?>
<div class="alert alert-success">Cart updated!</div>
<?php endif; ?>
<?php if (empty($cart)): ?>
<div class="alert alert-info text-center"><h4><b>😳</b>Cart is empty<b>🫣</b></h4>
<a href="menu.php" style="color:#c0392b;"><h6>Browse Menu</h6></a></div>
<?php else: ?>
<div class="row">
<div class="col-md-8">
<form method="POST">
<table class="table table-bordered">
<thead class="cart-table">
<tr><th>Pizza</th><th>Price</th><th>Qty</th>
<th>Subtotal</th><th>Remove</th></tr>
</thead>
<tbody>
<?php foreach ($cart as $mid => $item): ?>
<tr>
<td><?php echo $item['name']; ?></td>
<td>Rs.<?php echo number_format($item['price'],2); ?></td>
<td>
<input type="number" name="quantity[<?php echo $mid; ?>]"
value="<?php echo $item['quantity']; ?>"
min="0" max="10" class="form-control"
style="width:70px;">
</td>
<td>Rs.<?php
echo number_format($item['price']*$item['quantity'],2); ?></td>
<td>
<a href="cart.php?remove=<?php echo $mid; ?>"
class="btn btn-sm btn-danger">Remove</a>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<button type="submit" name="update_cart"
class="btn btn-outline-danger">Update Cart</button>
</form>
</div>
<div class="col-md-4">
<div class="card p-4"
style="border-radius:15px;box-shadow:0 4px 15px rgba(0,0,0,0.1);">
<h5 class="fw-bold mb-3">Order Summary</h5>
<div class="d-flex justify-content-between mb-2">
<span>Subtotal</span>
<span>Rs.<?php echo number_format($cart_total,2); ?></span>
</div>
<div class="d-flex justify-content-between mb-2">
<span>Delivery</span>
<span>Rs.<?php echo $delivery_charge; ?></span>
</div>
<hr>
<div class="d-flex justify-content-between mb-3 cart-total">
<span>Grand Total</span>
<span>Rs.<?php echo number_format($grand_total,2); ?></span>
</div>
<a href="place_order.php" class="btn btn-primary">Proceed to Order</a>
<a href="menu.php" class="btn btn-outline-danger mt-2 w-100">
Continue Shopping</a>
</div>
</div>
</div>
<?php endif; ?>
</div>
<footer><p>&copy; 2026 PizzaHub. All rights reserved.</p></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
</script>
</body></html
<?php
session_start();
include '../config/db.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
if (empty($_SESSION['cart'])) { header("Location: cart.php"); exit(); }
$user_id = $_SESSION['user_id'];
$cart = $_SESSION['cart'];
$cart_total = 0;
foreach ($cart as $item) $cart_total += $item['price'] * $item['quantity'];
$delivery_charge = 40;
$grand_total = $cart_total + $delivery_charge;
$user_data = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT * FROM users WHERE user_id=$user_id"));
$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
$delivery_addr = trim($_POST['delivery_addr']);
if (empty($delivery_addr)) {
$error = "Delivery address is required.";
} else {
$sql = "INSERT INTO orders (user_id,total_amount,delivery_addr,status)
VALUES ($user_id,$grand_total,'$delivery_addr','Pending Payment')";
if (mysqli_query($conn,$sql)) {
$order_id = mysqli_insert_id($conn);
foreach ($cart as $item) {
$sub = $item['price'] * $item['quantity'];
mysqli_query($conn,
"INSERT INTO order_items
(order_id,menu_id,quantity,unit_price,subtotal)
VALUES ($order_id,{$item['menu_id']},
{$item['quantity']},{$item['price']},$sub)");
}

header("Location: payment.php?order_id=$order_id"); exit();
} else { $error = "Failed to place order. Please try again."; }
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><title>Place Order - PizzaHub</title>
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
<li class="nav-item"><a class="nav-link" href="cart.php"><h5>Back to Cart</h5></a></li>
<li class="nav-item"><a class="nav-link" href="logout.php"><h5>Logout</h5></a></li>
</ul>
</div>
</div>
</nav>
<div class="page-header"><div class="container"><h2>Place Your Order</h2></div></div>
<div class="container mb-5">
<?php if ($error): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
<div class="row">
<div class="col-md-7">
<div class="card p-4 mb-4"
style="border-radius:15px;box-shadow:0 4px 15px rgba(0,0,0,0.1);">
<h5 class="fw-bold mb-3">Order Items</h5>
<table class="table table-bordered">
<thead class="cart-table">
<tr><th>Pizza</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr>
</thead>
<tbody>
<?php foreach ($cart as $item): ?>
<tr>
<td><?php echo $item['name']; ?></td>
<td><?php echo $item['quantity']; ?></td>
<td>Rs.<?php echo number_format($item['price'],2); ?></td>
<td>Rs.<?php
echo number_format($item['price']*$item['quantity'],2); ?></td>
</tr>
<?php endforeach; ?>
</tbody>
<tfoot>
<tr><td colspan="3" class="fw-bold">Delivery Charge</td>
<td>Rs.<?php echo $delivery_charge; ?></td></tr>
<tr><td colspan="3" class="fw-bold cart-total">Grand Total</td>
<td class="cart-total">
Rs.<?php echo number_format($grand_total,2); ?></td></tr>
</tfoot>
</table>
</div>
</div>
<div class="col-md-5">
<div class="card p-4"
style="border-radius:15px;box-shadow:0 4px 15px rgba(0,0,0,0.1);">
<h5 class="fw-bold mb-3">Delivery Address</h5>
<form method="POST">
<div class="mb-3">
<label class="form-label fw-bold">Deliver to:</label>
<textarea name="delivery_addr" class="form-control"
rows="4" required><?php echo $user_data['address']; ?></textarea>
<small class="text-muted">Edit address above if needed.</small>
</div>
<button type="submit" class="btn btn-primary">Confirm Order and Pay</button>
</form>
</div>
</div>
</div>
</div>
<footer><p>&copy; 2026 PizzaHub. All rights reserved.</p></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
</script>
</body></html>
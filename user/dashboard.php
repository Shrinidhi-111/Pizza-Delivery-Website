<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: login.php"); exit();
}

$user_id = $_SESSION['user_id'];
$fname    = $_SESSION['fname'];
$lname    = $_SESSION['lname'];

$total_orders  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM orders WHERE user_id=$user_id"))['t'];
$active_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM orders WHERE user_id=$user_id AND status!='Delivered'"))['t'];
$total_spent   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) as t FROM orders WHERE user_id=$user_id"))['t'];
$total_spent   = $total_spent ? $total_spent : 0;
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
$recent_orders = mysqli_query($conn, "SELECT * FROM orders WHERE user_id=$user_id ORDER BY order_date DESC LIMIT 3");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - PizzaHub</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="../index.php">🍕 PizzaHub</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="menu.php"><h5>Menu</h5></a></li>
                <li class="nav-item">
  <a class="nav-link" href="cart.php"><h5>🛒 Cart
    <?php if ($cart_count > 0)
      echo "<span class='badge bg-warning text-dark'>$cart_count</span>"; ?>
  </h5></a>
</li>
                <li class="nav-item"><a class="nav-link" href="order_status.php"><h5>My Orders</h5></a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php"><h5>Logout</h5></a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="page-header">
    <div class="container">
        <h2>👋 Welcome, <?php echo htmlspecialchars($fname); ?>!</h2>
        <p class="mb-0">What would you like to order today?</p>
    </div>
</div>
<div class="container mb-5">
    <div class="row g-4 mb-5">
        <div class="col-md-4"><div class="dashboard-card bg-red"><h3><?php echo $total_orders; ?></h3><p>Total Orders</p></div></div>
        <div class="col-md-4"><div class="dashboard-card bg-orange"><h3><?php echo $active_orders; ?></h3><p>Active Orders</p></div></div>
        <div class="col-md-4"><div class="dashboard-card bg-green"><h3>₹<?php echo number_format($total_spent,2); ?></h3><p>Total Spent</p></div></div>
    </div>
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <a href="menu.php" class="text-decoration-none">
                <div class="card text-center p-4 pizza-card">
                    <div style="font-size:3rem;">🍕</div>
                    <h5 class="mt-3 fw-bold text-dark">Browse Menu</h5>
                    <p class="text-muted">See all available pizzas</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="cart.php" class="text-decoration-none">
                <div class="card text-center p-4 pizza-card">
                    <div style="font-size:3rem;">🛒</div>
                    <h5 class="mt-3 fw-bold text-dark">My Cart</h5>
                    <p class="text-muted">View and manage your cart</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="order_status.php" class="text-decoration-none">
                <div class="card text-center p-4 pizza-card">
                    <div style="font-size:3rem;">📦</div>
                    <h5 class="mt-3 fw-bold text-dark">My Orders</h5>
                    <p class="text-muted">Track your orders</p>
                </div>
            </a>
        </div>
    </div>
    <h4 class="fw-bold mb-3">🕒 Recent Orders</h4>
    <?php if (mysqli_num_rows($recent_orders) > 0): ?>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="cart-table">
                <tr><th>Order ID</th><th>Date</th><th>Total</th><th>Status</th></tr>
            </thead>
            <tbody>
            <?php while ($o = mysqli_fetch_assoc($recent_orders)):
                $cls = '';
                if ($o['status']=='Confirmed') $cls='status-confirmed';
                elseif ($o['status']=='Preparing') $cls='status-preparing';
                elseif ($o['status']=='Out for Delivery') $cls='status-out';
                elseif ($o['status']=='Delivered') $cls='status-delivered';
            ?>
                <tr>
                    <td>#<?php echo $o['order_id']; ?></td>
                    <td><?php echo date('d M Y', strtotime($o['order_date'])); ?></td>
                    <td>₹<?php echo number_format($o['total_amount'],2); ?></td>
                    <td><span class="<?php echo $cls; ?>"><?php echo $o['status']; ?></span></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <div class="alert alert-info">No orders yet. <a href="menu.php" style="color:#c0392b;">Order now!</a></div>
    <?php endif; ?>
</div>
<footer><p>&copy; 2026 PizzaHub. All rights reserved.</p></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

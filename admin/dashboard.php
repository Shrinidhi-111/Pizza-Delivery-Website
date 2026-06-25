<?php
session_start();
include '../config/db.php';
if (!isset($_SESSION['user_id'])||$_SESSION['role']!='admin') {
header("Location: login.php"); exit();
}
$total_orders = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) as t FROM orders"))['t'];
$total_users = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) as t FROM users WHERE role='user'"))['t'];
$total_revenue = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT SUM(total_amount) as t FROM orders"))['t'];
$total_revenue = $total_revenue ? $total_revenue : 0;
$pending_orders = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) as t FROM orders WHERE status!='Delivered'"))['t'];

$recent_orders = mysqli_query($conn,
"SELECT o.*, CONCAT(u.fname,' ',u.lname) AS name
FROM orders o
JOIN users u ON o.user_id = u.user_id
ORDER BY o.order_date DESC
LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><title>Admin Dashboard - PizzaHub</title>
<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="d-flex">
<!-- SIDEBAR (same on every admin page) -->
<div class="admin-sidebar" style="width:220px;min-width:220px;">
<a class="brand" href="dashboard.php">PizzaHub Admin</a>
<nav class="nav flex-column">
<a class="nav-link active" href="dashboard.php">Dashboard</a>
<a class="nav-link" href="manage_menu.php">Manage Menu</a>
<a class="nav-link" href="manage_orders.php">Manage Orders</a>
<a class="nav-link" href="manage_users.php">Manage Users</a>
<a class="nav-link" href="logout.php">Logout</a>
</nav>
</div>
<div class="flex-grow-1 admin-content">
<h3 class="fw-bold mb-4">Dashboard</h3>
<div class="row g-4 mb-5">
<div class="col-md-3">
<div class="dashboard-card bg-red">
<h3><?php echo $total_orders; ?></h3>
<p>Total Orders</p></div></div>
<div class="col-md-3">
<div class="dashboard-card bg-blue">
<h3><?php echo $total_users; ?></h3>
<p>Total Users</p></div></div>
<div class="col-md-3">
<div class="dashboard-card bg-green">
<h3>Rs.<?php echo number_format($total_revenue,0); ?></h3>
<p>Revenue</p></div></div>
<div class="col-md-3">
<div class="dashboard-card bg-orange">
<h3><?php echo $pending_orders; ?></h3>
<p>Pending</p></div></div>
</div>
<h5 class="fw-bold mb-3">Recent Orders</h5>
<div class="table-responsive">
<table class="table table-bordered table-hover">
<thead class="cart-table">
<tr><th>ID</th><th>Customer</th><th>Amount</th>
<th>Status</th><th>Date</th></tr>
</thead>
<tbody>
<?php while ($o = mysqli_fetch_assoc($recent_orders)):
$cls='';
if ($o['status']=='Confirmed') $cls='status-confirmed';
elseif ($o['status']=='Preparing') $cls='status-preparing';
elseif ($o['status']=='Out for Delivery')$cls='status-out';
elseif ($o['status']=='Delivered') $cls='status-delivered';
?>
<tr>
<td>#<?php echo $o['order_id']; ?></td>
<td><?php echo $o['name']; ?></td>
<td>Rs.<?php echo number_format($o['total_amount'],2); ?></td>
<td>
<span class="<?php echo $cls; ?>">
<?php echo $o['status']; ?></span></td>
<td><?php echo date('d M Y',strtotime($o['order_date'])); ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
</script>
</body></html>

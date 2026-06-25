<?php
session_start();
include '../config/db.php';
if (!isset($_SESSION['user_id'])||$_SESSION['role']!='admin') {
header("Location: login.php"); exit();
}
if (isset($_GET['delete'])) {
mysqli_query($conn,"DELETE FROM menu WHERE menu_id=".(int)$_GET['delete']);
header("Location: manage_menu.php?deleted=1"); exit();
}
$menu_items = mysqli_query($conn,"SELECT * FROM menu ORDER BY category,name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><title>Manage Menu - Admin</title>
<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="d-flex">
<div class="admin-sidebar" style="width:220px;min-width:220px;">
<a class="brand" href="dashboard.php">PizzaHub Admin</a>
<nav class="nav flex-column">
<a class="nav-link" href="dashboard.php">Dashboard</a>
<a class="nav-link active" href="manage_menu.php">Manage Menu</a>
<a class="nav-link" href="manage_orders.php">Manage Orders</a>
<a class="nav-link" href="manage_users.php">Manage Users</a>
<a class="nav-link" href="logout.php">Logout</a>
</nav>
</div>
<div class="flex-grow-1 admin-content">
<div class="d-flex justify-content-between align-items-center mb-4">
<h3 class="fw-bold">Manage Menu</h3>
<a href="add_pizza.php" class="btn btn-primary" style="width:auto;">
+ Add New Pizza</a>
</div>
<?php if (isset($_GET['deleted'])): ?>
<div class="alert alert-success">Pizza deleted.</div>
<?php endif; ?>
<?php if (isset($_GET['saved'])): ?>
<div class="alert alert-success">Pizza saved.</div>
<?php endif; ?>
<div class="table-responsive">
<table class="table table-bordered table-hover">
<thead class="cart-table">
<tr><th>#</th><th>Name</th><th>Category</th><th>Size</th>
<th>Price</th><th>Available</th><th>Actions</th></tr>
</thead>
<tbody>
<?php while ($item = mysqli_fetch_assoc($menu_items)): ?>
<tr>
<td><?php echo $item['menu_id']; ?></td>

<td><?php echo $item['name']; ?></td>
<td><?php echo $item['category']; ?></td>
<td><?php echo $item['size']; ?></td>
<td>Rs.<?php echo number_format($item['price'],2); ?></td>
<td>
<span class="badge
<?php echo $item['is_available']?'bg-success':'bg-secondary'; ?>">
<?php echo $item['is_available']?'Yes':'No'; ?>
</span>
</td>
<td>
<a href="edit_pizza.php?id=<?php echo $item['menu_id']; ?>"
class="btn btn-sm btn-warning">Edit</a>
<a href="manage_menu.php?delete=<?php echo $item['menu_id']; ?>"
class="btn btn-sm btn-danger"
onclick="return confirm('Delete?')">Delete</a>
</td>
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

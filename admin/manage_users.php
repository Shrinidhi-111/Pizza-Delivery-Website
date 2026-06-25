<?php
session_start();
include '../config/db.php';
if (!isset($_SESSION['user_id'])||$_SESSION['role']!='admin') {
header("Location: login.php"); exit();
}
if (isset($_GET['delete'])) {
$id = (int)$_GET['delete'];
if ($id != $_SESSION['user_id']) {
mysqli_query($conn,"DELETE FROM users WHERE user_id=$id");
header("Location: manage_users.php?deleted=1"); exit();
}
}
$users = mysqli_query($conn,
"SELECT * FROM users ORDER BY role DESC, created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><title>Manage Users - Admin</title>
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
<a class="nav-link" href="manage_menu.php">Manage Menu</a>
<a class="nav-link" href="manage_orders.php">Manage Orders</a>
<a class="nav-link active" href="manage_users.php">Manage Users</a>
<a class="nav-link" href="logout.php">Logout</a>
</nav>
</div>
<div class="flex-grow-1 admin-content">
<h3 class="fw-bold mb-4">Manage Users</h3>
<?php if (isset($_GET['deleted'])): ?>
<div class="alert alert-success">User deleted.</div>
<?php endif; ?>
<div class="table-responsive">
<table class="table table-bordered table-hover">
<thead class="cart-table">
<tr><th>#</th><th>fName</th><th>lName</th><th>Email</th><th>Phone</th>
<th>Role</th><th>Joined</th><th>Action</th></tr>
</thead>
<tbody>
<?php while ($u = mysqli_fetch_assoc($users)): ?>
<tr>
<td><?php echo $u['user_id']; ?></td>
<td><?php echo $u['fname']; ?></td>
<td><?php echo $u['lname']; ?></td>
<td><?php echo $u['email']; ?></td>
<td><?php echo $u['phone']; ?></td>
<td>
<span class="badge
<?php echo $u['role']=='admin'?'bg-danger':'bg-primary'; ?>">
<?php echo ucfirst($u['role']); ?>
</span>
</td>
<td><?php echo date('d M Y',strtotime($u['created_at'])); ?></td>
<td>
<?php if ($u['user_id'] != $_SESSION['user_id']): ?>
<a href="manage_users.php?delete=<?php echo $u['user_id']; ?>"
class="btn btn-sm btn-danger"
onclick="return confirm('Delete this user?')">Delete</a>
<?php else: ?>
<span class="text-muted small">You</span>
<?php endif; ?>
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

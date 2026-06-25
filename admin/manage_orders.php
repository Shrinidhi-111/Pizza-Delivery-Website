<?php
session_start();
include '../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php"); exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $oid    = (int)$_POST['order_id'];
    $status = $_POST['status'];

    mysqli_query($conn, "UPDATE orders SET status='$status' WHERE order_id=$oid");

    if ($status == 'Delivered') {
		 mysqli_query($conn, "UPDATE payments SET payment_status='Completed' WHERE order_id=$oid AND payment_method='COD'");
        header("Location: manage_orders.php?delivered=1"); exit();
    } else {
        header("Location: manage_orders.php?updated=1"); exit();
    }
}

// Admin only sees non-delivered orders
$orders = mysqli_query($conn,
    "SELECT o.*, CONCAT(u.fname, ' ', u.lname) as cname, u.phone, p.payment_method, p.payment_status
     FROM orders o
     JOIN users u ON o.user_id = u.user_id
     JOIN payments p ON o.order_id = p.order_id
	 WHERE o.status != 'Delivered' 
	AND (p.payment_status = 'Completed' OR (p.payment_method = 'COD' AND p.payment_status = 'Pending'))
    ORDER BY o.order_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Manage Orders - Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="d-flex">
    <div class="admin-sidebar" style="width:220px;min-width:220px;">
        <a class="brand" href="dashboard.php">PizzaHub Admin</a>
        <nav class="nav flex-column">
            <a class="nav-link" href="dashboard.php">Dashboard</a>
            <a class="nav-link" href="manage_menu.php">Manage Menu</a>
            <a class="nav-link active" href="manage_orders.php">Manage Orders</a>
            <a class="nav-link" href="manage_users.php">Manage Users</a>
            <a class="nav-link" href="logout.php">Logout</a>
        </nav>
    </div>

    <div class="flex-grow-1 admin-content">
        <h3 class="fw-bold mb-4">Manage Orders</h3>

        <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-success">Order status updated successfully!</div>
        <?php endif; ?>

        <?php if (isset($_GET['delivered'])): ?>
            <div class="alert alert-info">Order marked as Delivered and removed from this list.</div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="cart-table">
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Update</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($orders) == 0): ?>
                 <tr>
                        <td colspan="8" class="text-center text-muted py-3"></td>
                    </tr>   
                <?php else: ?>
                <?php while ($o = mysqli_fetch_assoc($orders)):
                    $cls = '';
                    if ($o['status'] == 'Confirmed')            $cls = 'status-confirmed';
                    elseif ($o['status'] == 'Preparing')        $cls = 'status-preparing';
                    elseif ($o['status'] == 'Out for Delivery') $cls = 'status-out';
                ?>
                <tr>
                    <td>#<?php echo $o['order_id']; ?></td>
                    <td><?php echo htmlspecialchars($o['cname']); ?></td>
                    <td><?php echo htmlspecialchars($o['phone']); ?></td>
                    <td>Rs.<?php echo number_format($o['total_amount'], 2); ?></td>
                    <td>
                        <?php echo $o['payment_method'] ?: 'N/A'; ?><br>
                        <span class="badge <?php echo $o['payment_status'] == 'Completed' ? 'bg-success' : 'bg-warning text-dark'; ?>">
                            <?php echo $o['payment_status'] ?: 'Pending'; ?>
                        </span>
                    </td>
                    <td><?php echo date('d M Y', strtotime($o['order_date'])); ?></td>
                    <td><span class="<?php echo $cls; ?>"><?php echo $o['status']; ?></span></td>
                    <td>
                        <form method="POST" class="d-flex gap-1" onsubmit="return confirmDelivered(this)">
                            <input type="hidden" name="order_id" value="<?php echo $o['order_id']; ?>">
                            <select name="status" class="form-select form-select-sm" style="width:150px;">
                                <option value="Confirmed"        <?php echo $o['status'] == 'Confirmed'        ? 'selected' : ''; ?>>Confirmed</option>
                                <option value="Preparing"        <?php echo $o['status'] == 'Preparing'        ? 'selected' : ''; ?>>Preparing</option>
                                <option value="Out for Delivery" <?php echo $o['status'] == 'Out for Delivery' ? 'selected' : ''; ?>>Out for Delivery</option>
                                <option value="Delivered"        <?php echo $o['status'] == 'Delivered'        ? 'selected' : ''; ?>>Delivered</option>
                            </select>
                            <button type="submit" name="update_status"
                                    class="btn btn-sm btn-primary" style="width:auto;">Update</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function confirmDelivered(form) {
        const status = form.querySelector('select[name="status"]').value;
        if (status === 'Delivered') {
            return confirm('Mark this order as Delivered? It will be removed from this list.');
        }
        return true;
    }
</script>
</body>
</html>
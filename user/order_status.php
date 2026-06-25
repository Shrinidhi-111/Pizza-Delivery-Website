<?php
session_start();
include '../config/db.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$user_id = $_SESSION['user_id'];

$orders = mysqli_query($conn,
    "SELECT o.*, p.payment_method, p.payment_status
     FROM orders o
     INNER JOIN payments p ON o.order_id = p.order_id
     WHERE o.user_id = $user_id
     ORDER BY o.order_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>My Orders - PizzaHub</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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

<div class="page-header"><div class="container"><h2>My Orders</h2></div></div>

<div class="container mb-5">
    <?php if (mysqli_num_rows($orders) == 0): ?>
        <div class="alert alert-info text-center">No orders yet.
            <a href="menu.php" style="color:#c0392b;">Order now!</a>
        </div>
    <?php else: ?>
    <?php while ($order = mysqli_fetch_assoc($orders)):
        $cls = '';
        $is_delivered = ($order['status'] == 'Delivered');
        if ($order['status'] == 'Confirmed')            $cls = 'status-confirmed';
        elseif ($order['status'] == 'Preparing')        $cls = 'status-preparing';
        elseif ($order['status'] == 'Out for Delivery') $cls = 'status-out';
        elseif ($order['status'] == 'Delivered')        $cls = 'status-delivered';

        $items = mysqli_query($conn,
            "SELECT oi.*, m.name FROM order_items oi
             JOIN menu m ON oi.menu_id = m.menu_id
             WHERE oi.order_id = {$order['order_id']}");
    ?>
    <div class="card mb-4" style="border-radius:15px;box-shadow:0 4px 15px rgba(0,0,0,0.08);
         <?php echo $is_delivered ? 'border: 2px solid #28a745;' : ''; ?>">

        <div class="card-header d-flex justify-content-between align-items-center"
             style="background:<?php echo $is_delivered ? '#d4edda' : '#f8f9fa'; ?>;border-radius:15px 15px 0 0;">
            <div>
                <strong>Order #<?php echo $order['order_id']; ?></strong>
                <span class="text-muted ms-3 small">
                    <?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?>
                </span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="<?php echo $cls; ?>"><?php echo $order['status']; ?></span>
                <?php if ($is_delivered): ?>
                    <span class="badge bg-success">&#10003; Delivered</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="card-body">
            <table class="table table-sm table-bordered mb-3">
                <thead>
                    <tr><th>Pizza</th><th>Qty</th><th>Subtotal</th></tr>
                </thead>
                <tbody>
                    <?php while ($it = mysqli_fetch_assoc($items)): ?>
                    <tr>
                        <td><?php echo $it['name']; ?></td>
                        <td><?php echo $it['quantity']; ?></td>
                        <td>Rs.<?php echo number_format($it['subtotal'], 2); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <div class="row">
                <div class="col-md-6">
                    <p><strong>Address:</strong> <?php echo $order['delivery_addr']; ?></p>
                    <p><strong>Payment:</strong> <?php echo $order['payment_method'] ?: 'N/A'; ?></p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="cart-total">Total: Rs.<?php echo number_format($order['total_amount'], 2); ?></p>
                    <p><strong>Payment Status:</strong>
                        <span class="badge <?php echo $order['payment_status'] == 'Completed' ? 'bg-success' : 'bg-warning text-dark'; ?>">
                            <?php echo $order['payment_status'] ?: 'Pending'; ?>
                        </span>
                    </p>
                    <?php if ($is_delivered): ?>
                        <p class="text-success fw-bold">&#10003; Your order has been delivered. Enjoy your pizza!</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
    <?php endif; ?>
</div>

<footer><p>&copy; 2026 PizzaHub. All rights reserved.</p></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
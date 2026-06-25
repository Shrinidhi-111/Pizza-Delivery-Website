<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
if (!$order_id) { header("Location: dashboard.php"); exit(); }

$user_id = $_SESSION['user_id'];
$order = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT * FROM orders WHERE order_id=$order_id AND user_id=$user_id"));
if (!$order) { header("Location: dashboard.php"); exit(); }

// Always clean up empty/invalid payment rows first
mysqli_query($conn, "DELETE FROM payments WHERE order_id=$order_id AND (payment_method = '' OR payment_method IS NULL)");

// Now check if a valid payment already exists
$existing = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT payment_id FROM payments WHERE order_id=$order_id"));

$success = $existing ? true : false;
$error   = "";

if (!$existing && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $method = trim($_POST['payment_method'] ?? '');
    $upi_id = trim($_POST['upi_id'] ?? '');

    if (!in_array($method, ['COD', 'UPI'])) {
        $error = "Please select a payment method.";
    } elseif ($method == 'UPI' && empty($upi_id)) {
        $error = "Please enter your UPI ID.";
    } elseif ($method == 'UPI' && strpos($upi_id, '@') === false) {
        $error = "Enter a valid UPI ID (e.g. name@okaxis).";
    } else {
        $amount = (float)$order['total_amount'];
        $m      = mysqli_real_escape_string($conn, $method);
		$status = ($method == 'COD') ? 'Pending' : 'Completed';
$sql    = "INSERT INTO payments (order_id, user_id, amount, payment_method, payment_status)
           VALUES ($order_id, $user_id, $amount, '$m', '$status')";
        //$sql    = "INSERT INTO payments (order_id, user_id, amount, payment_method, payment_status)
                  // VALUES ($order_id, $user_id, $amount, '$m', 'Completed')";
        if (mysqli_query($conn, $sql)) {
            $success = true;
    mysqli_query($conn, "UPDATE orders SET status='Confirmed' WHERE order_id=$order_id");
	unset($_SESSION['cart']);

        } else {
            $error = "Payment failed: " . mysqli_error($conn);
        }
    }
}

$items = mysqli_query($conn,
    "SELECT oi.quantity, oi.subtotal, m.name
     FROM order_items oi JOIN menu m ON oi.menu_id = m.menu_id
     WHERE oi.order_id = $order_id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment - PizzaHub</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="../index.php">PizzaHub</a>
    <ul class="navbar-nav ms-auto">
      <li class="nav-item"><a class="nav-link" href="dashboard.php"><h5>Dashboard</h5></a></li>
      <li class="nav-item"><a class="nav-link" href="logout.php"><h5>Logout</h5></a></li>
    </ul>
  </div>
</nav>

<div class="page-header">
  <div class="container"><h2>Payment</h2></div>
</div>

<div class="container mb-5">

<?php if ($success): 
  $payment = mysqli_fetch_assoc(mysqli_query($conn,
      "SELECT payment_method FROM payments WHERE order_id=$order_id"));
  $is_cod = ($payment['payment_method'] == 'COD');
?>
  <div class="alert <?php echo $is_cod ? 'alert-warning' : 'alert-success'; ?> text-center py-4">
    <?php if ($is_cod): ?>
      <h4>🛵 Order Confirmed!</h4>
      <p>Your Order ID is <strong>#<?php echo $order_id; ?></strong></p>
      <p class="text-muted">Please keep <strong>Rs.<?php echo number_format($order['total_amount'], 2); ?></strong> ready at the time of delivery.</p>
    <?php else: ?>
      <h4>✅ Payment Successful!</h4>
  <p>Your Order ID is <strong>#<?php echo $order_id; ?></strong></p>
  <p>Amount Paid: <strong>Rs.<?php echo number_format($order['total_amount'], 2); ?></strong></p>
  <p>Your pizza is on its way, enjoy the deliciousness😎</p>
    <?php endif; ?>
    <a href="order_status.php" class="btn btn-primary me-2">Track Order</a>
    <a href="menu.php" class="btn btn-outline-danger">Order More</a>
  </div>

<?php else: ?>
  <?php if ($error): ?>
  <div class="alert alert-danger"><?php echo $error; ?></div>
  <?php endif; ?>

  <div class="row g-4">

    <!-- Order Summary -->
    <div class="col-md-5">
      <div class="card p-4" style="border-radius:15px;box-shadow:0 4px 15px rgba(0,0,0,0.1);">
        <h5 class="fw-bold mb-3">Order #<?php echo $order_id; ?></h5>
        <table class="table table-sm table-bordered">
          <thead class="cart-table">
            <tr><th>Item</th><th>Qty</th><th>Subtotal</th></tr>
          </thead>
          <tbody>
            <?php while ($i = mysqli_fetch_assoc($items)): ?>
            <tr>
              <td><?php echo htmlspecialchars($i['name']); ?></td>
              <td><?php echo $i['quantity']; ?></td>
              <td>Rs.<?php echo number_format($i['subtotal'], 2); ?></td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
        <div class="d-flex justify-content-between fw-bold cart-total">
          <span>Total</span>
          <span>Rs.<?php echo number_format($order['total_amount'], 2); ?></span>
        </div>
        <p class="text-muted mt-2 mb-0" style="font-size:.85rem;">
          📍 <?php echo htmlspecialchars($order['delivery_addr']); ?>
        </p>
      </div>
    </div>

    <!-- Payment Methods -->
    <div class="col-md-7">
      <div class="card p-4" style="border-radius:15px;box-shadow:0 4px 15px rgba(0,0,0,0.1);">
        <h5 class="fw-bold mb-3">Select Payment Method</h5>
        <form method="POST">

          <div class="form-check border rounded p-3 mb-3">
            <input class="form-check-input" type="radio"
                   name="payment_method" value="UPI" id="upi"
                   <?php echo (($_POST['payment_method'] ?? '') == 'UPI') ? 'checked' : ''; ?>
                   onchange="document.getElementById('upi_box').style.display='block'">
            <label class="form-check-label fw-bold" for="upi">
              📱 UPI <small class="text-muted fw-normal">(GPay, PhonePe, Paytm)</small>
            </label>
          </div>

          <div id="upi_box" style="display:<?php echo (($_POST['payment_method'] ?? '') == 'UPI') ? 'block' : 'none'; ?>;" class="mb-3 px-2">
            <input type="text" name="upi_id" class="form-control"
                   placeholder="Enter UPI ID (e.g. name@okaxis)"
                   value="<?php echo htmlspecialchars($_POST['upi_id'] ?? ''); ?>">
            <div class="form-text">e.g. name@okaxis, number@paytm, name@ybl</div>
          </div>

          <div class="form-check border rounded p-3 mb-4">
            <input class="form-check-input" type="radio"
                   name="payment_method" value="COD" id="cod"
                   <?php echo (($_POST['payment_method'] ?? '') == 'COD') ? 'checked' : ''; ?>
                   onchange="document.getElementById('upi_box').style.display='none'">
            <label class="form-check-label fw-bold" for="cod">
              💵 Cash on Delivery <small class="text-muted fw-normal">(Pay at door)</small>
            </label>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="fw-bold">Amount to Pay:</span>
            <span class="cart-total">Rs.<?php echo number_format($order['total_amount'], 2); ?></span>
          </div>

          <button type="submit" class="btn btn-primary">Confirm Payment</button>
        </form>
      </div>
    </div>

  </div>
<?php endif; ?>
</div>

<footer><p>&copy; 2026 PizzaHub. All rights reserved.</p></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
session_start();
include '../config/db.php';

// If already logged in as admin, go to dashboard
if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'admin') {
    header("Location: dashboard.php"); exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Both email and password are required.";
    } else {
        // Look up admin by email only first
        $email_safe = mysqli_real_escape_string($conn, $email);
        $result = mysqli_query($conn,
            "SELECT * FROM users WHERE email='$email_safe'");

        if (!$result) {
            $error = "Database error: " . mysqli_error($conn);
        } elseif (mysqli_num_rows($result) == 0) {
            $error = "No account found with this email.";
        } else {
            $user = mysqli_fetch_assoc($result);

            // Check password first
            if (!password_verify($password, $user['password'])) {
                $error = "Incorrect password.";
            // Then check role
            } elseif ($user['role'] != 'admin') {
                $error = "This account does not have admin access.";
            } else {
                // Success — set session and redirect
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['name']    = $user['name'];
                $_SESSION['email']   = $user['email'];
                $_SESSION['role']    = $user['role'];
                header("Location: dashboard.php"); exit();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login - PizzaHub</title>
<link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="../index.php">PizzaHub Admin</a>
  </div>
</nav>

<div class="container">
  <div class="form-container">
    <h2>Admin Login</h2>

    <?php if ($error): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label fw-bold">Admin Email</label>
        <input type="email" name="email" class="form-control"
               placeholder="admin@pizza.com"
               value="<?php echo isset($_POST['email'])
                             ? htmlspecialchars($_POST['email']) : ''; ?>"
               required>
      </div>
      <div class="mb-3">
        <label class="form-label fw-bold">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary">Login as Admin</button>
    </form>

    <!-- Quick fix helper: only use during development, remove before submission -->
    <?php if (isset($_GET['fix'])): ?>
    <hr>
    <div class="alert alert-warning mt-3" style="font-size:.82rem;">
      <strong>Password Reset Helper</strong><br>
      <?php
        $new_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $fix = mysqli_query($conn,
            "UPDATE users SET password='$new_hash'
             WHERE email='admin@pizza.com' AND role='admin'");
        if ($fix && mysqli_affected_rows($conn) > 0) {
            echo "✅ Admin password has been reset to <strong>admin123</strong>. 
                  Remove <code>?fix</code> from the URL now.";
        } else {
            echo "❌ Could not reset password. Make sure admin@pizza.com exists in the users table.";
        }
      ?>
    </div>
    <?php endif; ?>

  </div>
</div>

<footer><p>&copy; 2026 PizzaHub. All rights reserved.</p></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
</script>
</body>
</html>
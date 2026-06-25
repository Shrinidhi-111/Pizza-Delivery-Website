<?php
session_start();
include '../config/db.php';

// Already logged in — redirect
if (isset($_SESSION['user_id'])) {
    header($_SESSION['role'] == 'admin'
        ? "Location: ../admin/dashboard.php"
        : "Location: dashboard.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Both fields are required.";
    } else {
        $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

        if (!$result) {
            $error = "Database error: " . mysqli_error($conn);
        } elseif (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['fname']    = $user['fname'];
				$_SESSION['lname']    = $user['lname'];
                $_SESSION['email']   = $user['email'];
                $_SESSION['role']    = $user['role'];
                header($user['role'] == 'admin'
                    ? "Location: ../admin/dashboard.php"
                    : "Location: dashboard.php");
                exit();
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "No account found with this email.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - PizzaHub</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="../index.php">PizzaHub</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="../index.php"><h5>Home</h5></a></li>
                <li class="nav-item"><a class="nav-link" href="register.php"><h5>Register</h5></a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="form-container">
        <h2>Login</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label fw-bold">Email Address</label>
                <input type="email" name="email" class="form-control"
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
            <p class="text-center mt-3">Don't have an account?
                <a href="register.php" style="color:#c0392b;">Register here</a>
            </p>
            <p class="text-center">
                <small class="text-muted">Admin: admin@pizza.com </small>
            </p>
        </form>
    </div>
</div>

<footer><p>&copy; 2026 PizzaHub. All rights reserved.</p></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
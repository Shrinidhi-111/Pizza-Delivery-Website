<?php
session_start();
include '../config/db.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fname    = trim(mysqli_real_escape_string($conn, $_POST['fname']));
	$lname    = trim(mysqli_real_escape_string($conn, $_POST['lname']));
    $email   = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $phone   = trim(mysqli_real_escape_string($conn, $_POST['phone']));
    $password = trim($_POST['password']);
    $confirm  = trim($_POST['confirm_password']);
    $address  = trim(mysqli_real_escape_string($conn, $_POST['address']));

    if (empty($fname) || empty($email) || empty($phone) || empty($password) || empty($address)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    }elseif(!preg_match('/^[0-9]{10}$/',$phone)){
		$error="Phone number must be 10 digits";
	}
	elseif(!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.com$/", $email)) {
    echo "Email must end with .com";
} 
	else {
        $check = mysqli_query($conn, "SELECT user_id FROM users WHERE email='$email'");
        if (!$check) {
            $error = "Database error: " . mysqli_error($conn);
        } elseif (mysqli_num_rows($check) > 0) {
            $error = "Email already registered. Please login.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (fname,lname, email, phone, password, address, role)
                    VALUES ('$fname', '$lname','$email', '$phone', '$hashed', '$address', 'user')";
            if (mysqli_query($conn, $sql)) {
                $success = "Registration successful! You can now login.";
            } else {
                $error = "Registration failed: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - PizzaHub</title>
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
                <li class="nav-item"><a class="nav-link" href="login.php"><h5>Login</h5></a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="form-container">
        <h2>Create Account</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success); ?>
                <br><a href="login.php" style="color:#155724;font-weight:bold;">Click here to Login</a>
            </div>
        <?php else: ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label fw-bold">First Name</label>
                <input type="text" name="fname" class="form-control"
                       value="<?php echo isset($_POST['fname']) ? htmlspecialchars($_POST['fname']) : ''; ?>" required>
            </div>
			<div class="mb-3">
                <label class="form-label fw-bold">Last Name</label>
                <input type="text" name="lname" class="form-control"
                       value="<?php echo isset($_POST['lname']) ? htmlspecialchars($_POST['lname']) : ''; ?>" >
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Email Address</label>
                <input type="email" name="email" class="form-control"
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Phone Number</label>
                <input type="text" name="phone" class="form-control" maxlength="10" patter="[0-9]{10}"
                       value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Password</label>
                <input type="password" name="password" class="form-control"
                       placeholder="Minimum 6 characters" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Delivery Address</label>
                <textarea name="address" class="form-control" rows="3" required><?php
                    echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : '';
                ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
            <p class="text-center mt-3">Already have an account?
                <a href="login.php" style="color:#c0392b;">Login here</a>
            </p>
        </form>
        <?php endif; ?>
    </div>
</div>

<footer><p>&copy; 2026 PizzaHub. All rights reserved.</p></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
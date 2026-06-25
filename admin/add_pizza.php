<?php
session_start();
include '../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php"); exit();
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name         = trim($_POST['name']);
    $description  = trim($_POST['description']);
    $price        = trim($_POST['price']);
    $size         = $_POST['size'];
    $category     = trim($_POST['category']);
    $is_available = isset($_POST['is_available']) ? 1 : 0;
    $image_name   = "";

    if (empty($name) || empty($price) || empty($category)) {
        $error = "Name, price and category are required.";
    } else {
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            $max_size = 2 * 1024 * 1024; // 2MB

            if (!in_array($_FILES['image']['type'], $allowed_types)) {
                $error = "Only JPG, PNG, and WEBP images are allowed.";
            } elseif ($_FILES['image']['size'] > $max_size) {
                $error = "Image size must be less than 2MB.";
            } else {
                $ext        = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $image_name = 'pizza_' . time() . '.' . strtolower($ext);
                $upload_dir = '../assets/images/pizzas/';

                // Create folder if it doesn't exist
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                // Check folder is writable before uploading
                if (!is_writable($upload_dir)) {
                    $error = "Upload folder is not writable. Please check folder permissions.";
                } elseif (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name)) {
                    $error = "Failed to upload image. Please try again.";
                }
            }
        } else {
            $upload_error = isset($_FILES['image']) ? $_FILES['image']['error'] : 'No file received';
            $error = "Pizza image is required. (Error code: $upload_error)";
        }

        if (empty($error)) {
            $stmt = mysqli_prepare($conn,
                "INSERT INTO menu (name, description, price, size, category, is_available, image)
                 VALUES (?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssdssis", $name, $description, $price, $size, $category, $is_available, $image_name);

            if (mysqli_stmt_execute($stmt)) {
                header("Location: manage_menu.php?saved=1"); exit();
            } else {
                $error = "Failed to add pizza: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Add Pizza - Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        #image-preview {
            display: none;
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid #dee2e6;
            margin-top: 10px;
        }
    </style>
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
        <h3 class="fw-bold mb-4">Add New Pizza</h3>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card p-4" style="max-width:600px;border-radius:15px;box-shadow:0 4px 15px rgba(0,0,0,0.1);">
            <form method="POST" enctype="multipart/form-data">

                <div class="mb-3">
                    <label class="form-label fw-bold">Pizza Name</label>
                    <input type="text" name="name" class="form-control" required
                           value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Description</label>
                    <textarea name="description" class="form-control" rows="3"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Price (Rs.)</label>
                        <input type="number" name="price" class="form-control" step="0.01" required
                               value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Size</label>
                        <select name="size" class="form-control">
                            <option value="Small">Small</option>
                            <option value="Medium" selected>Medium</option>
                            <option value="Large">Large</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Category</label>
                    <input type="text" name="category" class="form-control"
                           placeholder="e.g. Veg, Non-Veg" required
                           value="<?php echo isset($_POST['category']) ? htmlspecialchars($_POST['category']) : ''; ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Pizza Image <span class="text-danger">*</span></label>
                    <input type="file" name="image" class="form-control"
                           accept="image/jpeg,image/jpg,image/png,image/webp"
                           id="imageInput" required>
                    <small class="text-muted">Accepted: JPG, PNG, WEBP &nbsp;|&nbsp; Max size: 2MB</small>
                    <br>
                    <img id="image-preview" src="#" alt="Image Preview">
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" name="is_available" class="form-check-input" id="avail" checked>
                    <label class="form-check-label fw-bold" for="avail">Available on Menu</label>
                </div>

                <button type="submit" class="btn btn-primary" style="width:auto;">Add Pizza</button>
                <a href="manage_menu.php" class="btn btn-outline-danger ms-2" style="width:auto;">Cancel</a>

            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('imageInput').addEventListener('change', function () {
        const file = this.files[0];
        const preview = document.getElementById('image-preview');
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
        }
    });
</script>
</body>
</html>
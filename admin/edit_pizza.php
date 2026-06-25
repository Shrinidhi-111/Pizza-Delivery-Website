<?php
session_start();
include '../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php"); exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$pizza = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM menu WHERE menu_id=$id"));
if (!$pizza) { header("Location: manage_menu.php"); exit(); }

$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name         = trim($_POST['name']);
    $description  = trim($_POST['description']);
	
    $price        = trim($_POST['price']);
    $size         = $_POST['size'];
    $category     = trim($_POST['category']);
    $is_available = isset($_POST['is_available']) ? 1 : 0;

    // Keep existing image by default
    $image_name = $pizza['image'];

    if (empty($name) || empty($price) || empty($category)) {
        $error = "Name, price and category are required.";
    } else {
        // Handle new image upload if a new file is selected
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            $max_size = 2 * 1024 * 1024; // 2MB

            if (!in_array($_FILES['image']['type'], $allowed_types)) {
                $error = "Only JPG, PNG, and WEBP images are allowed.";
            } elseif ($_FILES['image']['size'] > $max_size) {
                $error = "Image size must be less than 2MB.";
            } else {
                $ext            = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $new_image_name = 'pizza_' . time() . '.' . strtolower($ext);
                $upload_dir     = '../assets/images/pizzas/';

                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                if (!is_writable($upload_dir)) {
                    $error = "Upload folder is not writable. Please check folder permissions.";
                } elseif (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $new_image_name)) {
                    // Delete old image if it exists
                    if (!empty($pizza['image']) && file_exists($upload_dir . $pizza['image'])) {
                        unlink($upload_dir . $pizza['image']);
                    }
                    $image_name = $new_image_name;
                } else {
                    $error = "Failed to upload image. Please try again.";
                }
            }
        }

        if (empty($error)) {
            $stmt = mysqli_prepare($conn,
                "UPDATE menu SET name=?, description=?, price=?, size=?, category=?, is_available=?, image=?
                 WHERE menu_id=?");
            mysqli_stmt_bind_param($stmt, "ssdssisi", $name, $description, $price, $size, $category, $is_available, $image_name, $id);

            if (mysqli_stmt_execute($stmt)) {
                header("Location: manage_menu.php?saved=1"); exit();
            } else {
                $error = "Failed to update pizza: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Edit Pizza - Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        #image-preview {
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
        <h3 class="fw-bold mb-4">Edit Pizza</h3>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card p-4" style="max-width:600px;border-radius:15px;box-shadow:0 4px 15px rgba(0,0,0,0.1);">
            <!-- FIX: Added enctype for file upload -->
            <form method="POST" enctype="multipart/form-data">

                <div class="mb-3">
                    <label class="form-label fw-bold">Pizza Name</label>
                    <input type="text" name="name" class="form-control"
                           value="<?php echo htmlspecialchars($pizza['name']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Description</label>
                    <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($pizza['description']); ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Price (Rs.)</label>
                        <input type="number" name="price" class="form-control"
                               step="0.01" value="<?php echo $pizza['price']; ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Size</label>
                        <select name="size" class="form-control">
                            <option value="Small"  <?php echo $pizza['size'] == 'Small'  ? 'selected' : ''; ?>>Small</option>
                            <option value="Medium" <?php echo $pizza['size'] == 'Medium' ? 'selected' : ''; ?>>Medium</option>
                            <option value="Large"  <?php echo $pizza['size'] == 'Large'  ? 'selected' : ''; ?>>Large</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Category</label>
                    <input type="text" name="category" class="form-control"
                           value="<?php echo htmlspecialchars($pizza['category']); ?>" required>
                </div>

                <!-- FIX: Image upload section added -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Pizza Image</label>

                    <!-- Show current image -->
                    <?php if (!empty($pizza['image'])): ?>
                        <div class="mb-2">
                            <small class="text-muted">Current Image:</small><br>
                            <img id="image-preview"
                                 src="../assets/images/pizzas/<?php echo htmlspecialchars($pizza['image']); ?>"
                                 alt="Current Pizza Image">
                        </div>
                    <?php endif; ?>

                    <input type="file" name="image" class="form-control"
                           accept="image/jpeg,image/jpg,image/png,image/webp"
                           id="imageInput">
                    <small class="text-muted">Leave empty to keep current image &nbsp;|&nbsp; Accepted: JPG, PNG, WEBP &nbsp;|&nbsp; Max: 2MB</small>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" name="is_available" class="form-check-input"
                           id="avail" <?php echo $pizza['is_available'] ? 'checked' : ''; ?>>
                    <label class="form-check-label fw-bold" for="avail">Available on Menu</label>
                </div>

                <button type="submit" class="btn btn-primary" style="width:auto;">Update Pizza</button>
                <a href="manage_menu.php" class="btn btn-outline-danger ms-2" style="width:auto;">Cancel</a>

            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Preview new image when selected
    document.getElementById('imageInput').addEventListener('change', function () {
        const file = this.files[0];
        const preview = document.getElementById('image-preview');
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>
</body>
</html>
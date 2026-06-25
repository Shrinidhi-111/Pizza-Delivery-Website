<?php
session_start();
include '../config/db.php';

if (isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
    $menu_id = $_POST['menu_id'];
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    if (isset($_SESSION['cart'][$menu_id])) {
        $_SESSION['cart'][$menu_id]['quantity'] += 1;
    } else {
        $_SESSION['cart'][$menu_id] = [
            'menu_id'  => $menu_id,
            'name'     => $_POST['name'],
            'price'    => $_POST['price'],
            'quantity' => 1
        ];
    }
    header("Location: menu.php?added=1"); exit();
}

$cat   = isset($_GET['category']) ? $_GET['category'] : 'All';
$items = ($cat == 'All')
    ? mysqli_query($conn, "SELECT * FROM menu WHERE is_available=1 ORDER BY category")
    : mysqli_query($conn, "SELECT * FROM menu WHERE is_available=1
        AND category='" . mysqli_real_escape_string($conn, $cat) . "'");

$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Menu - PizzaHub</title>
<link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="../index.php">PizzaHub</a>
    <button class="navbar-toggler" type="button"
            data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="../index.php"><h5>Home<h5></a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
        <li class="nav-item"><a class="nav-link" href="dashboard.php"><h5>Dashboard<h5></a></li>
        <li class="nav-item">
          <a class="nav-link" href="cart.php"><h5>🛒Cart</h5>
            <?php if ($cart_count > 0)
              echo "<span class='badge bg-warning text-dark'>$cart_count</span>"; ?>
          </a>
        </li>
        <li class="nav-item"><a class="nav-link" href="logout.php"><h5>Logout</h5></a></li>
        <?php else: ?>
        <li class="nav-item"><a class="nav-link" href="login.php"><h5>Login</h5></a></li>
        <li class="nav-item"><a class="nav-link" href="register.php"><h5>Register</h5></a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="page-header">
  <div class="container"><h2>Our Pizza Menu</h2></div>
</div>

<div class="container mb-5">

  <?php if (isset($_GET['added'])): ?>
  <div class="alert alert-success alert-dismissible fade show">
    Pizza added to cart! <a href="cart.php">View Cart</a>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>

  <!-- Category Filter Buttons -->
  
  <div class="mb-4 text-center">
  <input type="text" id="pizzaSearch" 
         class="form-control" 
         placeholder="🔍 Search pizza by name..."
         style="max-width:500px;font-weight:900;  display:block;">
    <a href="menu.php"
       class="btn btn-sm me-2 mb-2
       <?php echo $cat=='All' ? 'btn-danger' : 'btn-outline-danger'; ?>">
      All
    </a>
    <?php
    $cats = mysqli_query($conn,
        "SELECT DISTINCT category FROM menu WHERE is_available=1 ORDER BY category");
    while ($c = mysqli_fetch_assoc($cats)):
    ?>
    <a href="menu.php?category=<?php echo urlencode($c['category']); ?>"
       class="btn btn-sm me-2 mb-2
       <?php echo $cat==$c['category'] ? 'btn-danger' : 'btn-outline-danger'; ?>">
      <?php echo $c['category']; ?>
    </a>
    <?php endwhile; ?>
  </div>

  <!-- Pizza Cards -->
  <div class="row g-4">
    <?php while ($item = mysqli_fetch_assoc($items)): ?>
    <div class="col-md-4 col-sm-6 pizza-item">
      <div class="card pizza-card">

        <!-- IMAGE SECTION — fixed for Windows XAMPP -->
        <div style="height:220px;border-radius:15px 15px 0 0;overflow:hidden;">
          <?php
          // Browser path (for src attribute)
          $img_menu  = '../assets/images/menu/' . $item['image'];
		$img_pizza  = '../assets/images/pizzas/' . $item['image'];
          // Absolute server path (for file_exists check — works on Windows XAMPP)
          $full_menu = $_SERVER['DOCUMENT_ROOT'] . '/pizza_delivery/assets/images/menu/' . $item['image'];
			$full_pizza = $_SERVER['DOCUMENT_ROOT'] . '/pizza_delivery/assets/images/pizzas/' . $item['image'];
          //if (!empty($item['image']) && file_exists($full_path)):
		  if (!empty($item['image']) && file_exists($full_pizza)) {
			$img_path = $img_pizza;
		} elseif (!empty($item['image']) && file_exists($full_menu)) {
				$img_path = $img_menu;
		} 
          ?>
            <img src="<?php echo $img_path; ?>"
                 alt="<?php echo htmlspecialchars($item['name']); ?>"
                 style="width:100%;height:100%;object-fit:cover;">
          
            <div style="height:100%;
                        background:linear-gradient(135deg,#c0392b,#e74c3c);
                        display:flex;align-items:center;
                        justify-content:center;font-size:3.5rem;">
              🍕
            </div>

        </div>

        <!-- Card Body -->
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="card-title fw-bold mb-0">
              <?php echo htmlspecialchars($item['name']); ?>
            </h5>
			<?php if(strtolower($item['category']) == 'veg'): ?>
  <span style="display:inline-block; background:#DCFCE7; color:#15803D; 
               padding:4px 12px; border-radius:100px; font-size:0.72rem; 
               font-weight:700; letter-spacing:0.05em; text-transform:uppercase;">
    VEG
  </span>
<?php else: ?>
  <span style="display:inline-block; background:#FDECEA; color:#922B21; 
               padding:4px 12px; border-radius:100px; font-size:0.72rem; 
               font-weight:700; letter-spacing:0.05em; text-transform:uppercase;">
    <?php echo $item['category']; ?>
  </span>
<?php endif; ?>
            
          </div>
          <p class="text-muted small mb-2">
            <?php echo $item['description'] ?: 'Delicious fresh pizza'; ?>
          </p>
          <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="price-tag">
              Rs.<?php echo number_format($item['price'], 2); ?>
            </span>
            <span class="text-muted small"><?php echo $item['size']; ?></span>
          </div>

          <?php if (isset($_SESSION['user_id'])): ?>
          <form method="POST">
            <input type="hidden" name="menu_id"
                   value="<?php echo $item['menu_id']; ?>">
            <input type="hidden" name="name"
                   value="<?php echo htmlspecialchars($item['name']); ?>">
            <input type="hidden" name="price"
                   value="<?php echo $item['price']; ?>">
            <button type="submit" name="add_to_cart"
                    class="btn btn-primary w-100">Add to Cart</button>
          </form>
          <?php else: ?>
          <a href="login.php" class="btn btn-primary w-100">Login to Order</a>
          <?php endif; ?>

        </div>
      </div>
    </div>
    <?php endwhile; ?>
  </div>

</div>

<footer><p>&copy; 2026 PizzaHub. All rights reserved.</p></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
</script>
<script>
document.getElementById('pizzaSearch').addEventListener('keyup', function() {
  const search = this.value.toLowerCase();
  const items  = document.querySelectorAll('.pizza-item');

  items.forEach(function(item) {
    const name = item.querySelector('.card-title').textContent.toLowerCase();
    if (name.includes(search)) {
      item.style.display = 'block';
    } else {
      item.style.display = 'none';
    }
  });
});
</script>
</body>
</html>
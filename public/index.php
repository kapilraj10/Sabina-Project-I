<?php
session_start();
$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>User</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
<div class="container-fluid">

<a class="navbar-brand fw-bold text-primary" href="index.php">
Store Management
</a>

<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#storeNavbar">
<span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse" id="storeNavbar">

<ul class="navbar-nav me-auto">
<li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
<li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
<li class="nav-item"><a class="nav-link" href="orders.php">Orders</a></li>
</ul>

<div class="d-flex">
<?php if ($userName): ?>
<span class="btn btn-outline-secondary me-2">
Hello, <?php echo htmlspecialchars($userName); ?>
</span>
<a href="/Sabina/auth/logout.php" class="btn btn-danger">Logout</a>
<?php else: ?>
<a href="/Sabina/auth/login.php" class="btn btn-outline-primary me-2">Login</a>
<a href="/Sabina/auth/register.php" class="btn btn-primary">Register</a>
<?php endif; ?>
</div>

</div>
</div>
</nav>

<?php
require_once __DIR__ . '/../database/categories.php';
$categories = ($_SERVER['REQUEST_METHOD'] === 'GET') ? getAllCategories() : [];
?>

<div class="container mt-4">
<h1 class="mb-4">Categories</h1>

<?php if(empty($categories)): ?>
<div class="alert alert-secondary">No categories found.</div>
<?php else: ?>

<div class="row g-4">
<?php foreach($categories as $cat): ?>
<div class="col-6 col-md-4 col-lg-3">
<div class="card h-100 shadow-sm">

<?php if(!empty($cat['image'])): ?>
<img src="<?php echo htmlspecialchars($cat['image']); ?>" 
class="card-img-top" 
style="height:180px; object-fit:cover;"
alt="<?php echo htmlspecialchars($cat['name']); ?>">
<?php else: ?>
<div class="bg-light d-flex align-items-center justify-content-center" 
style="height:180px;">
No image
</div>
<?php endif; ?>

<div class="card-body">
<h5 class="card-title">
<?php echo htmlspecialchars($cat['name']); ?>
</h5>
<p class="card-text text-truncate">
<?php echo htmlspecialchars($cat['description']); ?>
</p>
</div>

</div>
</div>
<?php endforeach; ?>
</div>

<?php endif; ?>
</div>

<?php
require_once __DIR__ . '/../database/products.php';
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$filter_cat = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$products = ($_SERVER['REQUEST_METHOD'] === 'GET')
? getProducts($q, $filter_cat > 0 ? $filter_cat : null)
: [];
?>

<div class="container mt-5">

<div class="d-flex justify-content-between align-items-center mb-4">
<h2 class="h4">Products</h2>

<form class="d-flex gap-2" method="get">
<input type="search" name="q" 
class="form-control form-control-sm" 
placeholder="Search products"
value="<?php echo htmlspecialchars($q); ?>">
</form>
</div>

<?php if(empty($products)): ?>
<div class="alert alert-secondary">No products found.</div>
<?php else: ?>

<div class="row g-4">
<?php foreach($products as $p): ?>
<div class="col-6 col-md-4 col-lg-3">
<div class="card h-100 shadow-sm">

<?php if(!empty($p['image'])): ?>
<img src="<?php echo htmlspecialchars($p['image']); ?>" 
class="card-img-top" 
style="height:180px; object-fit:cover;"
alt="<?php echo htmlspecialchars($p['name']); ?>">
<?php else: ?>
<div class="bg-light d-flex align-items-center justify-content-center" 
style="height:180px;">
No image
</div>
<?php endif; ?>

<div class="card-body d-flex flex-column">

<h5 class="card-title">
<?php echo htmlspecialchars($p['name']); ?>
</h5>

<p class="card-text text-muted">
<?php echo htmlspecialchars($p['category_name'] ?? ''); ?>
</p>

<div class="mt-auto d-flex justify-content-between align-items-center">
<strong>Rs <?php echo number_format((float)$p['price'],2); ?></strong>

<form method="post" action="/Sabina/cart/add.php" class="m-0">
<input type="hidden" name="product_id" value="<?php echo (int)$p['id']; ?>">
<?php 
if(empty($_SESSION['csrf_token'])){
$_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}
?>
<input type="hidden" name="csrf_token" 
value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
<button class="btn btn-sm btn-success">+</button>
</form>

</div>

</div>
</div>
</div>
<?php endforeach; ?>
</div>

<?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
session_start();
// use the flat session keys set by database/auth.php (user_name, user_role, ...)
$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>User</title>
 
<!-- Bootstrap CSS (CDN) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<!-- Local site styles -->
<link rel="stylesheet" href="/Sabina/assets/css/bootstrap.min.css">
<link rel="stylesheet" href="/Sabina/assets/css/style.css">
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
<li class="nav-item"><a class="nav-link" href="shop.php">Shop</a></li>
<li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
<li class="nav-item"><a class="nav-link" href="orders.php">Orders</a></li>
</ul>

		<div class="d-flex">

		<?php if ($userName): ?>

			<span class="btn btn-outline-secondary me-2">Hello, <?php echo htmlspecialchars($userName); ?></span>

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
// Load categories for public listing (GET only)
require_once __DIR__ . '/../database/categories.php';
$categories = [];
// only call on GET
if($_SERVER['REQUEST_METHOD'] === 'GET'){
		$categories = getAllCategories();
}
?>

<div class="container mt-4">
	<h1 class="mb-3">Categories</h1>

	<?php if(empty($categories)): ?>
		<div class="alert alert-secondary">No categories found.</div>
	<?php else: ?>
		<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
			<?php foreach($categories as $cat): ?>
			<div class="col">
						<div class="card h-100 category-card">
								<?php if(!empty($cat['image'])): ?>
									<img src="<?php echo htmlspecialchars($cat['image']); ?>" class="card-img-top category-img" alt="<?php echo htmlspecialchars($cat['name']); ?>">
								<?php else: ?>
									<div class="bg-light d-flex align-items-center justify-content-center category-img">No image</div>
								<?php endif; ?>
								<div class="card-body d-flex flex-column">
									<h5 class="card-title"><?php echo htmlspecialchars($cat['name']); ?></h5>
									<p class="card-text text-truncate"><?php echo htmlspecialchars($cat['description']); ?></p>
								</div>
							</div>
			</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

</div>

<!-- Bootstrap JS (CDN with local fallback) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>if(typeof bootstrap === 'undefined'){document.write('<script src="/Sabina/assets/js/bootstrap.bundle.min.js"><\/script>');}</script>
<script src="/Sabina/assets/js/script.js"></script>

</body>
</html>
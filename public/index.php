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

<div class="container mt-4">
<h1>Welcome — Public Area</h1>
</div>

<!-- Bootstrap JS (CDN with local fallback) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>if(typeof bootstrap === 'undefined'){document.write('<script src="/Sabina/assets/js/bootstrap.bundle.min.js"><\/script>');}</script>
<script src="/Sabina/assets/js/script.js"></script>

</body>
</html>
<?php
// public/index.php - product and category listing
// Use the shared navbar include which handles session start and assets
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>User</title>

</head>
<body>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<?php if(!empty($_SESSION['user_name'])): ?>
	<div class="container mt-2 text-end">
		<form method="post" action="/Sabina/auth/logout.php" class="d-inline">
			<?php if(empty($_SESSION['csrf_token'])){ $_SESSION['csrf_token'] = bin2hex(random_bytes(16)); } ?>
			<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
			<button class="btn btn-sm btn-danger">Logout</button>
		</form>
	</div>
<?php endif; ?>

<?php
require_once __DIR__ . '/../database/categories.php';
$categories = [];
if($_SERVER['REQUEST_METHOD'] === 'GET'){
	$categories = getAllCategories();
}
?>

<div class="container mt-4">
	<h1 class="mb-3">Categories</h1>

	<?php if(empty($categories)): ?>
		<div class="alert alert-secondary">No categories found.</div>
	<?php else: ?>
		<div class="cards-container grid--small">
			<?php foreach($categories as $cat): ?>
				<div class="card category-card tiny-card">
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
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

</div>

<?php
require_once __DIR__ . '/../database/products.php';
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$filter_cat = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$products = [];
if($_SERVER['REQUEST_METHOD'] === 'GET'){
	$products = getProducts($q, $filter_cat > 0 ? $filter_cat : null);
}
?>

<div class="container mt-4">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<h2 class="h5">Products</h2>
		<form class="d-flex" method="get" style="gap:.5rem;">
			<input type="search" name="q" class="form-control form-control-sm" placeholder="Search products" value="<?php echo htmlspecialchars($q); ?>">
		</form>
	</div>

	<?php if(empty($products)): ?>
		<div class="alert alert-secondary">No products found.</div>
	<?php else: ?>
		<div class="cards-container grid--small">
			<?php foreach($products as $p): ?>
				<div class="card category-card tiny-card">
					<?php if(!empty($p['image'])): ?>
						<img src="<?php echo htmlspecialchars($p['image']); ?>" class="card-img-top category-img" alt="<?php echo htmlspecialchars($p['name']); ?>">
					<?php else: ?>
						<div class="bg-light d-flex align-items-center justify-content-center category-img">No image</div>
					<?php endif; ?>
					<div class="card-body d-flex flex-column">
						<h5 class="card-title"><?php echo htmlspecialchars($p['name']); ?></h5>
						<p class="card-text text-truncate"><?php echo htmlspecialchars($p['category_name'] ?? ''); ?></p>
						<div class="mt-auto d-flex justify-content-between align-items-center">
							<strong>Rs <?php echo number_format((float)$p['price'],2); ?></strong>
							<form method="post" action="/Sabina/cart/add.php" class="m-0">
								<input type="hidden" name="product_id" value="<?php echo (int)$p['id']; ?>">
								<?php if(empty($_SESSION['csrf_token'])){ $_SESSION['csrf_token'] = bin2hex(random_bytes(16)); } ?>
								<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
								<button class="btn btn-sm btn-success tiny-add" aria-label="Add to cart">+</button>
							</form>
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

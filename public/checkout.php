<?php
session_start();
require_once __DIR__ . '/../database/products.php';
// load cart
$cart = $_SESSION['cart'] ?? [];
$items = [];
$total = 0.0;
foreach($cart as $pid => $qty){
    $p = getProductById((int)$pid);
    if(!$p) continue;
    $p['qty'] = (int)$qty;
    $p['line_total'] = $p['qty'] * (float)$p['price'];
    $total += $p['line_total'];
    $items[] = $p;
}

if(empty($items)){
    $_SESSION['flash'] = 'Your cart is empty.';
    header('Location: /Sabina/');
    exit();
}

// ensure CSRF token
if(empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(16));

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Checkout</title>
  <link rel="stylesheet" href="/Sabina/assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container mt-4">
  <h2>Checkout</h2>
  <div class="row">
    <div class="col-md-6">
      <form method="post" action="/Sabina/public/order_place.php">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <div class="mb-2">
          <label class="form-label">Full name</label>
          <input class="form-control" name="name" required>
        </div>
        <div class="mb-2">
          <label class="form-label">Phone</label>
          <input class="form-control" name="phone" required>
        </div>
        <div class="mb-2">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" name="email" required>
        </div>
        <div class="mb-2">
          <label class="form-label">Region</label>
          <input class="form-control" name="region">
        </div>
        <div class="mb-2">
          <label class="form-label">Address</label>
          <textarea class="form-control" name="address" rows="3" required></textarea>
        </div>
        <button class="btn btn-primary" type="submit">Place Order (Rs <?php echo number_format($total,2); ?>)</button>
      </form>
    </div>
    <div class="col-md-6">
      <h5>Order summary</h5>
      <ul class="list-group">
        <?php foreach($items as $it): ?>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <div>
              <strong><?php echo htmlspecialchars($it['name']); ?></strong>
              <div class="small text-muted">Qty: <?php echo $it['qty']; ?> × Rs <?php echo number_format((float)$it['price'],2); ?></div>
            </div>
            <div>Rs <?php echo number_format($it['line_total'],2); ?></div>
          </li>
        <?php endforeach; ?>
        <li class="list-group-item d-flex justify-content-between"><strong>Total</strong><strong>Rs <?php echo number_format($total,2); ?></strong></li>
      </ul>
    </div>
  </div>
</div>

</body>
</html>

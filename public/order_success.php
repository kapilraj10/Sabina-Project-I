<?php
session_start();
require_once __DIR__ . '/../database/orders.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$order = null;
if($id > 0) $order = getOrderById($id);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Order placed</title>
  <link rel="stylesheet" href="/Sabina/assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="container mt-4">
  <?php if(!$order): ?>
    <div class="alert alert-danger">Order not found.</div>
  <?php else: ?>
    <h3>Thank you, your order was placed</h3>
    <p>Order ID: <strong><?php echo $order['id']; ?></strong></p>
    <p>Status: <strong><?php echo htmlspecialchars($order['status']); ?></strong></p>
    <h5>Items</h5>
    <ul class="list-group">
      <?php foreach($order['items'] as $it): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <div><?php echo htmlspecialchars($it['product_name']); ?> <div class="small text-muted">Qty: <?php echo $it['qty']; ?> × Rs <?php echo number_format((float)$it['unit_price'],2); ?></div></div>
          <div>Rs <?php echo number_format((float)$it['total_price'],2); ?></div>
        </li>
      <?php endforeach; ?>
    </ul>
    <p class="mt-3">Total: <strong>Rs <?php echo number_format((float)$order['total'],2); ?></strong></p>
  <?php endif; ?>
</div>
</body>
</html>

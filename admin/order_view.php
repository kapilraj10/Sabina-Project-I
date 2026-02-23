<?php
session_start();
require_once __DIR__ . '/../database/auth.php';
require_once __DIR__ . '/../database/orders.php';
if(!isAdmin()){ header('Location: /Sabina/'); exit(); }
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$order = $id ? getOrderById($id) : null;
if(!$order){ $_SESSION['flash'] = 'Order not found.'; header('Location: /Sabina/admin/orders.php'); exit(); }
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Order #<?php echo $order['id']; ?></title>
<link rel="stylesheet" href="/Sabina/assets/css/style.css"><link rel="stylesheet" href="/Sabina/assets/css/admin.css"></head><body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="container mt-4">
  <h3>Order #<?php echo $order['id']; ?></h3>
  <p>Customer: <?php echo htmlspecialchars($order['customer_name']); ?> — <?php echo htmlspecialchars($order['customer_phone']); ?></p>
  <p>Email: <?php echo htmlspecialchars($order['customer_email']); ?></p>
  <p>Address: <?php echo nl2br(htmlspecialchars($order['customer_address'])); ?></p>
  <p>Region: <?php echo htmlspecialchars($order['customer_region']); ?></p>
  <p>Status: <strong><?php echo htmlspecialchars($order['status']); ?></strong></p>
  <h5>Items</h5>
  <ul class="list-group">
    <?php foreach($order['items'] as $it): ?>
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <div><?php echo htmlspecialchars($it['product_name']); ?><div class="small text-muted">Qty: <?php echo $it['qty']; ?> × Rs <?php echo number_format((float)$it['unit_price'],2); ?></div></div>
        <div>Rs <?php echo number_format((float)$it['total_price'],2); ?></div>
      </li>
    <?php endforeach; ?>
  </ul>
  <p class="mt-3">Total: Rs <?php echo number_format((float)$order['total'],2); ?></p>
  <a href="/Sabina/admin/orders.php" class="btn btn-secondary mt-2">Back</a>
</div>
</body></html>

<?php
session_start();
require_once __DIR__ . '/database/products.php';

// remove item
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_id'])){
    $id = (int)$_POST['remove_id'];
    if(isset($_SESSION['cart'][$id])){
        unset($_SESSION['cart'][$id]);
    }
    header('Location: /Sabina/cart.php');
    exit();
}

$cart = $_SESSION['cart'] ?? [];
$items = [];
$total = 0.0;
foreach($cart as $pid => $qty){
    $p = getProductById($pid);
    if($p){
        $p['qty'] = $qty;
        $p['subtotal'] = $qty * (float)$p['price'];
        $items[] = $p;
        $total += $p['subtotal'];
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <title>Cart</title>
</head>
<body class="p-3">
  <div class="container">
    <h1 class="h4">Shopping Cart</h1>
    <?php if(!empty($_SESSION['flash'])): ?><div class="alert alert-info"><?php echo htmlspecialchars($_SESSION['flash']); unset($_SESSION['flash']); ?></div><?php endif; ?>
    <?php if(empty($items)): ?>
      <div class="alert alert-secondary">Your cart is empty.</div>
    <?php else: ?>
      <table class="table">
        <thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th><th></th></tr></thead>
        <tbody>
          <?php foreach($items as $it): ?>
            <tr>
              <td><?php echo htmlspecialchars($it['name']); ?></td>
              <td>Rs <?php echo number_format((float)$it['price'],2); ?></td>
              <td><?php echo (int)$it['qty']; ?></td>
              <td>Rs <?php echo number_format((float)$it['subtotal'],2); ?></td>
              <td>
                <form method="post"><input type="hidden" name="remove_id" value="<?php echo (int)$it['id']; ?>"><button class="btn btn-sm btn-danger">Remove</button></form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr><th colspan="3">Total</th><th>Rs <?php echo number_format($total,2); ?></th><th></th></tr>
        </tfoot>
      </table>
    <?php endif; ?>
    <a href="/Sabina/" class="btn btn-secondary">Continue shopping</a>
  </div>
</body>
</html>

<?php
session_start();
require_once __DIR__ . '/../database/auth.php';
require_once __DIR__ . '/../database/orders.php';
if(!isAdmin()){
    header('Location: /Sabina/'); exit();
}
$orders = getAllOrders();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin - Orders</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="/Sabina/assets/css/style.css">
  <link rel="stylesheet" href="/Sabina/assets/css/admin.css">
</head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="container-fluid">
    <nav class="d-none d-md-block sidebar">
      <div class="px-3">
        <ul class="nav flex-column mt-3">
          <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2"></i>Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="users.php"><i class="bi bi-people"></i>Manage Users</a></li>
          <li class="nav-item"><a class="nav-link active" href="orders.php"><i class="bi bi-receipt"></i>Orders</a></li>
          <li class="nav-item"><a class="nav-link" href="categories.php"><i class="bi bi-tags"></i>Categories</a></li>
          <li class="nav-item"><a class="nav-link" href="products.php"><i class="bi bi-box-seam"></i>Products</a></li>
        </ul>
      </div>
    </nav>

  <main class="admin-main">
    <h2>Orders</h2>
    <?php if(!empty($_SESSION['flash'])): ?>
      <div class="alert alert-info"><?php echo htmlspecialchars($_SESSION['flash']); ?></div>
      <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
    <table class="table">
      <thead><tr><th>ID</th><th>Customer</th><th>Total</th><th>Status</th><th>Created</th><th>Actions</th></tr></thead>
      <tbody>
      <?php foreach($orders as $o): ?>
        <tr>
          <td><?php echo $o['id']; ?></td>
          <td><?php echo htmlspecialchars($o['customer_name']); ?><div class="small text-muted"><?php echo htmlspecialchars($o['customer_phone']); ?></div></td>
          <td>Rs <?php echo number_format((float)$o['total'],2); ?></td>
          <td><?php echo htmlspecialchars($o['status']); ?></td>
          <td><?php echo $o['created_at']; ?></td>
          <td>
            <a class="btn btn-sm btn-outline-primary" href="/Sabina/admin/order_view.php?id=<?php echo $o['id']; ?>">View</a>
            <form method="post" action="/Sabina/admin/order_update.php" style="display:inline-block">
              <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
              <select name="status" class="form-select form-select-sm d-inline-block" style="width:auto;">
                <option value="pending" <?php echo ($o['status']==='pending')? 'selected':''; ?>>Pending</option>
                <option value="processing" <?php echo ($o['status']==='processing')? 'selected':''; ?>>Processing</option>
                <option value="completed" <?php echo ($o['status']==='completed')? 'selected':''; ?>>Completed</option>
                <option value="cancelled" <?php echo ($o['status']==='cancelled')? 'selected':''; ?>>Cancelled</option>
              </select>
              <?php if(empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(16)); ?>
              <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
              <button class="btn btn-sm btn-primary" type="submit">Update</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>

<?php
// Minimal admin dashboard placeholder
session_start();
// If not admin, redirect to login
if(empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin'){
  header('Location: ../auth/login.php');
  exit();
}

// load DB for revenue stats
require_once __DIR__ . '/../config/db.php';

// Total revenue (exclude cancelled orders)
$total_revenue = 0.0;
$today_revenue = 0.0;
$sqlTotal = "SELECT COALESCE(SUM(total),0) as total_revenue FROM orders WHERE status <> 'cancelled'";
if($res = mysqli_query($conn, $sqlTotal)){
  $row = mysqli_fetch_assoc($res);
  $total_revenue = (float) ($row['total_revenue'] ?? 0);
}

// Today's revenue (orders created today, exclude cancelled)
$sqlToday = "SELECT COALESCE(SUM(total),0) as today_revenue FROM orders WHERE DATE(created_at) = CURDATE() AND status <> 'cancelled'";
if($res2 = mysqli_query($conn, $sqlToday)){
  $row2 = mysqli_fetch_assoc($res2);
  $today_revenue = (float) ($row2['today_revenue'] ?? 0);
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="/Sabina/assets/css/style.css">
  <link rel="stylesheet" href="/Sabina/assets/css/admin.css">
</head>
<body>
  <div class="container-fluid">
    <nav class="d-none d-md-block sidebar">
      <div class="px-3">
        <h5 class="mt-2">Admin</h5>
        <ul class="nav flex-column mt-3">
          <li class="nav-item"><a class="nav-link active" href="dashboard.php"><i class="bi bi-speedometer2"></i>Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="users.php"><i class="bi bi-people"></i>Manage Users</a></li>
          <li class="nav-item"><a class="nav-link" href="orders.php"><i class="bi bi-receipt"></i>Orders</a></li>
          <li class="nav-item"><a class="nav-link" href="categories.php"><i class="bi bi-tags"></i>Categories</a></li>
          <li class="nav-item"><a class="nav-link" href="products.php"><i class="bi bi-box-seam"></i>Products</a></li>
        </ul>
      </div>
    </nav>

  <main class="admin-main">
  <div class="admin-card">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <h1 class="h3 mb-1">Admin Dashboard</h1>
            <p class="text-muted mb-2">Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?></p>
            <a href="../auth/logout.php" class="btn btn-sm btn-outline-secondary">Logout</a>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="col-sm-6 col-lg-3 mb-3">
            <div class="p-3 bg-light rounded">
              <h5 class="mb-0">Total Revenue</h5>
              <p class="h4 mb-0">Rs .<?php echo number_format($total_revenue, 2); ?></p>
              <small class="text-muted">All-time revenue (excluding cancelled)</small>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3 mb-3">
            <div class="p-3 bg-light rounded">
              <h5 class="mb-0">Today's Revenue</h5>
              <p class="h4 mb-0">RSs .<?php echo number_format($today_revenue, 2); ?></p>
              <small class="text-muted">Revenue for <?php echo date('F j, Y'); ?> (excluding cancelled)</small>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

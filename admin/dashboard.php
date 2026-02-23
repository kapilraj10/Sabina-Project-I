<?php
// Minimal admin dashboard placeholder
session_start();
// If not admin, redirect to login
if(empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin'){
    header('Location: ../auth/login.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="container mt-4">
  <h1>Admin Dashboard</h1>
  <p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?></p>
  <p><a href="../auth/logout.php" class="btn btn-sm btn-outline-secondary">Logout</a></p>
</body>
</html>

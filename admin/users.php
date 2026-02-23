<?php
session_start();
// require admin
if(empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin'){
    header('Location: ../auth/login.php');
    exit();
}

require_once __DIR__ . '/../database/users.php';

// simple CSRF token for delete forms
if(empty($_SESSION['csrf_token'])){
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}

$message = null;
// Handle delete POST
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])){
    $token = $_POST['csrf_token'] ?? '';
    if(!hash_equals($_SESSION['csrf_token'], $token)){
        $message = 'Invalid CSRF token.';
    } else {
        $id = (int) $_POST['delete_id'];
        if($id === ($_SESSION['user_id'] ?? 0)){
            $message = 'You cannot delete your own account while logged in.';
        } else {
            if(deleteUserById($id)){
                $message = 'User deleted.';
            } else {
                $message = 'Delete failed.';
            }
        }
    }
}

$users = getAllUsers();
$cur = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Manage Users</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="/Sabina/assets/css/style.css">
  <link rel="stylesheet" href="/Sabina/assets/css/admin.css">
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <nav class="d-none d-md-block sidebar collapse show">
      <div class="px-3">
        <ul class="nav flex-column mt-3">
          <li class="nav-item"><a class="nav-link <?php echo $cur==='dashboard.php' ? 'active' : ''; ?>" href="/Sabina/admin/dashboard.php"><i class="bi bi-speedometer2"></i>Dashboard</a></li>
          <li class="nav-item"><a class="nav-link <?php echo $cur==='users.php' ? 'active' : ''; ?>" href="/Sabina/admin/users.php"><i class="bi bi-people"></i>Manage Users</a></li>
          <li class="nav-item"><a class="nav-link <?php echo $cur==='categories.php' ? 'active' : ''; ?>" href="/Sabina/admin/categories.php"><i class="bi bi-tags"></i>Categories</a></li>
          <li class="nav-item"><a class="nav-link <?php echo $cur==='products.php' ? 'active' : ''; ?>" href="/Sabina/admin/products.php"><i class="bi bi-box-seam"></i>Products</a></li>
        </ul>
      </div>
    </nav>

    <main class="admin-main">
      <h1>Registered Users</h1>
      <?php if($message): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
      <?php endif; ?>

      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Created</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($users as $u): ?>
            <tr>
              <td><?php echo (int)$u['id']; ?></td>
              <td><?php echo htmlspecialchars($u['name']); ?></td>
              <td><?php echo htmlspecialchars($u['email']); ?></td>
              <td><?php echo htmlspecialchars($u['role']); ?></td>
              <td><?php echo htmlspecialchars($u['created_at']); ?></td>
              <td>
                <?php if((int)$u['id'] !== (int)($_SESSION['user_id'] ?? 0)): ?>
                <form method="post" style="display:inline" onsubmit="return confirm('Delete this user?');">
                  <input type="hidden" name="delete_id" value="<?php echo (int)$u['id']; ?>">
                  <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                  <button class="btn btn-sm btn-danger">Delete</button>
                </form>
                <?php else: ?>
                  <span class="text-muted">(you)</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

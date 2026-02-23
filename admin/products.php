<?php
session_start();
// admin guard
if(empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin'){
    header('Location: ../auth/login.php');
    exit();
}

require_once __DIR__ . '/../database/products.php';
require_once __DIR__ . '/../database/categories.php';

if(empty($_SESSION['csrf_token'])){
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}

$message = null;
$cur = basename($_SERVER['PHP_SELF']);

// handle create
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create'){
    $name = trim($_POST['name'] ?? '');
    $category_id = (int) ($_POST['category_id'] ?? 0);
    $price = (float) ($_POST['price'] ?? 0);

    if($name === ''){
        $message = 'Name is required.';
    } else {
        $imagePath = null;
        if(!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE){
            $uploadDir = __DIR__ . '/../uploads/products';
            if(!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $f = $_FILES['image'];
            $allowed = ['image/jpeg','image/png','image/gif'];
            if(!in_array($f['type'], $allowed)){
                $message = 'Invalid image type.';
            } elseif($f['size'] > 3 * 1024 * 1024){
                $message = 'Image too large (max 3MB).';
            } else {
                $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
                $filename = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                $dest = $uploadDir . '/' . $filename;
                if(move_uploaded_file($f['tmp_name'], $dest)){
                    $imagePath = '/Sabina/uploads/products/' . $filename;
                } else {
                    $message = 'Failed to move uploaded file.';
                }
            }
        }

        if($message === null){
            $id = createProduct($name, $category_id, $price, $imagePath);
            if($id){
                $message = 'Product created.';
            } else {
                $message = 'Failed to create product.';
            }
        }
    }
}

// handle delete
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])){
    $token = $_POST['csrf_token'] ?? '';
    if(!hash_equals($_SESSION['csrf_token'], $token)){
        $message = 'Invalid CSRF token.';
    } else {
        $id = (int) $_POST['delete_id'];
        $prod = getProductById($id);
        if($prod && !empty($prod['image'])){
            $local = __DIR__ . '/..' . $prod['image'];
            if(file_exists($local)) @unlink($local);
        }
        if(deleteProductById($id)){
            $message = 'Product deleted.';
        } else {
            $message = 'Delete failed.';
        }
    }
}

$products = getAllProducts();
$categories = getAllCategories();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Manage Products</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="/Sabina/assets/css/style.css">
  <link rel="stylesheet" href="/Sabina/assets/css/admin.css">
</head>
<body class="p-3">
  <div class="container-fluid">
    <nav class="d-none d-md-block sidebar">
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
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4">Products</h1>
      </div>

      <?php if($message): ?><div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>

      <div class="row">
        <div class="col-md-4">
          <div class="card mb-3">
            <div class="card-body">
              <h5 class="card-title">Create Product</h5>
              <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="create">
                <div class="mb-2">
                  <label class="form-label">Name</label>
                  <input name="name" class="form-control" required>
                </div>
                <div class="mb-2">
                  <label class="form-label">Category</label>
                  <select name="category_id" class="form-select">
                    <option value="0">-- None --</option>
                    <?php foreach($categories as $cat): ?>
                    <option value="<?php echo (int)$cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="mb-2">
                  <label class="form-label">Price</label>
                  <input name="price" type="number" step="0.01" class="form-control" value="0.00" required>
                </div>
                <div class="mb-2">
                  <label class="form-label">Image (optional)</label>
                  <input type="file" name="image" accept="image/*" class="form-control">
                </div>
                <button class="btn btn-primary">Create</button>
              </form>
            </div>
          </div>
        </div>

        <div class="col-md-8">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">All Products</h5>
              <div class="table-responsive">
                <table class="table table-sm table-hover">
                  <thead><tr><th>ID</th><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Actions</th></tr></thead>
                  <tbody>
                  <?php foreach($products as $p): ?>
                    <tr>
                      <td><?php echo (int)$p['id']; ?></td>
                      <td><?php if(!empty($p['image'])): ?><img src="<?php echo htmlspecialchars($p['image']); ?>" style="max-width:80px;max-height:60px;object-fit:cover;border-radius:4px" alt=""><?php endif; ?></td>
                      <td><?php echo htmlspecialchars($p['name']); ?></td>
                      <td><?php echo htmlspecialchars($p['category_name'] ?? ''); ?></td>
                      <td><?php echo number_format((float)$p['price'],2); ?></td>
                      <td class="admin-actions">
                        <a href="product_edit.php?id=<?php echo (int)$p['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="post" style="display:inline" onsubmit="return confirm('Delete this product?');">
                          <input type="hidden" name="delete_id" value="<?php echo (int)$p['id']; ?>">
                          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                          <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

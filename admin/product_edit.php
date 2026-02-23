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

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$prod = getProductById($id);
if(!$prod){ header('Location: products.php'); exit(); }
$categories = getAllCategories();
$message = null;

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim($_POST['name'] ?? '');
    $category_id = (int) ($_POST['category_id'] ?? 0);
    $price = (float) ($_POST['price'] ?? 0);
    $token = $_POST['csrf_token'] ?? '';
    if(!hash_equals($_SESSION['csrf_token'], $token)){
        $message = 'Invalid CSRF token.';
    } elseif($name === ''){
        $message = 'Name required.';
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
                $message = 'Image too large.';
            } else {
                $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
                $filename = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                $dest = $uploadDir . '/' . $filename;
                if(move_uploaded_file($f['tmp_name'], $dest)){
                    $imagePath = '/Sabina/uploads/products/' . $filename;
                    if(!empty($prod['image'])){ $old = __DIR__ . '/..' . $prod['image']; if(file_exists($old)) @unlink($old); }
                } else { $message = 'Failed to move uploaded file.'; }
            }
        }

        if($message === null){
            $ok = updateProduct($id, $name, $category_id, $price, $imagePath);
            if($ok){ $message = 'Product updated.'; $prod = getProductById($id); }
            else { $message = 'Update failed.'; }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Edit Product</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="/Sabina/assets/css/style.css">
  <link rel="stylesheet" href="/Sabina/assets/css/admin.css">
</head>
<body class="p-3">
  <div class="container">
    <h1 class="h4 mb-3">Edit Product</h1>
    <?php if($message): ?><div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
      <div class="mb-2">
        <label class="form-label">Name</label>
        <input name="name" class="form-control" value="<?php echo htmlspecialchars($prod['name']); ?>" required>
      </div>
      <div class="mb-2">
        <label class="form-label">Category</label>
        <select name="category_id" class="form-select">
          <option value="0">-- None --</option>
          <?php foreach($categories as $c): ?>
          <option value="<?php echo (int)$c['id']; ?>" <?php if($c['id']==$prod['category_id']) echo 'selected'; ?>><?php echo htmlspecialchars($c['name']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="mb-2">
        <label class="form-label">Price</label>
        <input name="price" type="number" step="0.01" class="form-control" value="<?php echo htmlspecialchars($prod['price']); ?>" required>
      </div>
      <div class="mb-2">
        <label class="form-label">Current Image</label><br>
        <?php if(!empty($prod['image'])): ?><img src="<?php echo htmlspecialchars($prod['image']); ?>" style="max-width:140px;display:block;margin-bottom:.5rem;border-radius:4px;" alt=""><?php else: ?><div class="text-muted">No image</div><?php endif; ?>
        <label class="form-label mt-2">Replace Image (optional)</label>
        <input type="file" name="image" accept="image/*" class="form-control">
      </div>
      <button class="btn btn-primary">Save</button>
      <a href="products.php" class="btn btn-secondary">Back</a>
    </form>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

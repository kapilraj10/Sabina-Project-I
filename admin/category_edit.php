<?php
session_start();
// admin guard
if(empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin'){
    header('Location: ../auth/login.php');
    exit();
}

require_once __DIR__ . '/../database/categories.php';

if(empty($_SESSION['csrf_token'])){
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}

$message = null;
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$cat = getCategoryById($id);
if(!$cat){
    header('Location: categories.php');
    exit();
}

// handle update
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $token = $_POST['csrf_token'] ?? '';
    if(!hash_equals($_SESSION['csrf_token'], $token)){
        $message = 'Invalid CSRF token.';
    } elseif($name === ''){
        $message = 'Name is required.';
    } else {
        $imagePath = null;
        if(!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE){
            $uploadDir = __DIR__ . '/../uploads/categories';
            if(!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $f = $_FILES['image'];
            $allowed = ['image/jpeg','image/png','image/gif'];
            if(!in_array($f['type'], $allowed)){
                $message = 'Invalid image type.';
            } elseif($f['size'] > 2 * 1024 * 1024){
                $message = 'Image too large.';
            } else {
                $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
                $filename = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                $dest = $uploadDir . '/' . $filename;
                if(move_uploaded_file($f['tmp_name'], $dest)){
                    $imagePath = '/Sabina/uploads/categories/' . $filename;
                    // delete old file if exists
                    if(!empty($cat['image'])){
                        $old = __DIR__ . '/..' . $cat['image'];
                        if(file_exists($old)) @unlink($old);
                    }
                } else {
                    $message = 'Failed to move uploaded file.';
                }
            }
        }

        if($message === null){
            $ok = updateCategory($id, $name, $description, $imagePath);
            if($ok){
                $message = 'Category updated.';
                $cat = getCategoryById($id); // refresh
            } else {
                $message = 'Update failed.';
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Edit Category</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/Sabina/assets/css/style.css">
    <link rel="stylesheet" href="/Sabina/assets/css/admin.css">
</head>
<body class="p-3">
  <div class="container">
    <h1 class="h4 mb-3">Edit Category</h1>
    <?php if($message): ?><div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
      <div class="mb-2">
        <label class="form-label">Name</label>
        <input name="name" class="form-control" value="<?php echo htmlspecialchars($cat['name']); ?>" required>
      </div>
      <div class="mb-2">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($cat['description']); ?></textarea>
      </div>
      <div class="mb-2">
        <label class="form-label">Current Image</label><br>
        <?php if(!empty($cat['image'])): ?><img src="<?php echo htmlspecialchars($cat['image']); ?>" style="max-width:140px;display:block;margin-bottom:.5rem;border-radius:4px;" alt="">
        <?php else: ?><div class="text-muted">No image</div><?php endif; ?>
        <label class="form-label mt-2">Replace Image (optional)</label>
        <input type="file" name="image" accept="image/*" class="form-control">
      </div>
      <button class="btn btn-primary">Save</button>
      <a href="categories.php" class="btn btn-secondary">Back</a>
    </form>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

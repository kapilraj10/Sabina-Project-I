<?php
session_start();
// admin guard
if(empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin'){
    header('Location: ../auth/login.php');
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors',1);

require_once __DIR__ . '/../database/categories.php';

if(empty($_SESSION['csrf_token'])){
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}

$message = null;

// handle create
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create'){
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if($name === ''){
        $message = 'Name is required.';
    } else {
        // handle image upload if provided
        $imagePath = null;
        if(!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE){
            $uploadDir = __DIR__ . '/../uploads/categories';
            if(!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $f = $_FILES['image'];
            // validate type
            $allowed = ['image/jpeg','image/png','image/gif'];
            if(!in_array($f['type'], $allowed)){
                $message = 'Invalid image type. Use jpg/png/gif.';
            } elseif($f['size'] > 2 * 1024 * 1024){
                $message = 'Image too large (max 2MB).';
            } else {
                $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
                $filename = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                $dest = $uploadDir . '/' . $filename;
                if(move_uploaded_file($f['tmp_name'], $dest)){
                    // store web path
                    $imagePath = '/Sabina/uploads/categories/' . $filename;
                } else {
                    $message = 'Failed to move uploaded file.';
                }
            }
        }

        if($message === null){
            $id = createCategory($name, $description, $imagePath);
            if($id){
                $message = 'Category created.';
            } else {
                $message = 'Failed to create category (DB).';
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
        // optional: remove image file from disk
        $cat = getCategoryById($id);
        if($cat && !empty($cat['image'])){
            $local = __DIR__ . '/..' . $cat['image'];
            if(file_exists($local)) @unlink($local);
        }
        if(deleteCategoryById($id)){
            $message = 'Category deleted.';
        } else {
            $message = 'Delete failed.';
        }
    }
}

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$categories = getAllCategories($q);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Manage Categories</title>
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
          <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2"></i>Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="users.php"><i class="bi bi-people"></i>Manage Users</a></li>
          <li class="nav-item"><a class="nav-link active" href="categories.php"><i class="bi bi-tags"></i>Categories</a></li>
        </ul>
      </div>
    </nav>

    <main class="admin-main">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4">Categories</h1>
        <div class="d-flex gap-2">
          <form class="d-flex" method="get" role="search">
            <input name="q" class="form-control form-control-sm" placeholder="Search categories" value="<?php echo htmlspecialchars($q); ?>">
            <button class="btn btn-sm btn-outline-secondary ms-2">Search</button>
          </form>
          <a href="dashboard.php" class="btn btn-sm btn-secondary">Back to Dashboard</a>
        </div>
      </div>

    <?php if($message): ?><div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>

    <div class="row">
      <div class="col-md-5">
        <div class="card mb-3">
          <div class="card-body">
            <h5 class="card-title">Create Category</h5>
            <form method="post" enctype="multipart/form-data">
              <input type="hidden" name="action" value="create">
              <div class="mb-2">
                <label class="form-label">Name</label>
                <input name="name" class="form-control" required>
              </div>
              <div class="mb-2">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
              </div>
              <div class="mb-2">
                <label class="form-label">Image (jpg/png/gif, max 2MB)</label>
                <input type="file" name="image" accept="image/*" class="form-control">
              </div>
              <button class="btn btn-primary">Create</button>
            </form>
          </div>
        </div>
      </div>

      <div class="col-md-7">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">All Categories</h5>
            <div class="table-responsive">
              <table class="table table-sm table-hover">
                <thead>
                  <tr><th>ID</th><th>Image</th><th>Name</th><th>Description</th><th>Created</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php foreach($categories as $c): ?>
                  <tr>
                    <td><?php echo (int)$c['id']; ?></td>
                    <td><?php if(!empty($c['image'])): ?><img src="<?php echo htmlspecialchars($c['image']); ?>" class="thumb" alt=""><?php endif; ?></td>
                    <td><?php echo htmlspecialchars($c['name']); ?></td>
                    <td><?php echo htmlspecialchars($c['description']); ?></td>
                    <td><?php echo htmlspecialchars($c['created_at']); ?></td>
                    <td>
                      <a href="category_edit.php?id=<?php echo (int)$c['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                      <form method="post" style="display:inline" onsubmit="return confirm('Delete this category?');">
                        <input type="hidden" name="delete_id" value="<?php echo (int)$c['id']; ?>">
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
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

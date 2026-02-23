<?php
// include config and auth helpers
include __DIR__ . '/../config/db.php';
include __DIR__ . '/../database/auth.php';


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$error = '';
$success = '';
$name = '';
$email = '';

if(isset($_POST['register'])){
    // basic server-side validation
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if($name === '' || $email === '' || $password === ''){
        $error = 'Please fill all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6){
        $error = 'Password must be at least 6 characters.';
    } else {
        // check if email already exists
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? LIMIT 1");
        if($stmt){
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            if(mysqli_stmt_num_rows($stmt) > 0){
                $error = 'Email is already registered.';
                mysqli_stmt_close($stmt);
            } else {
                mysqli_stmt_close($stmt);
                // attempt registration
                $ok = registerUser($name, $email, $password);
                if($ok){
                    // redirect to login on success
                    header("Location: login.php");
                    exit();
                } else {
                    $dberr = mysqli_error($conn);
                    $error = 'Registration failed due to a server error: ' . htmlspecialchars($dberr);
                }
            }
        } else {
            $error = 'Registration unavailable. Database error.';
        }
    }

}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Register</title>
<!-- Bootstrap CSS (CDN) with local fallback -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="../assets/css/bootstrap.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="../assets/css/auth.css">
</head>

<body>

<div class="auth-wrapper">
  <div class="auth-card">
    <h3 class="text-center">Register</h3>

    <?php if(!empty($error)) echo "<p class='text-danger'>".htmlspecialchars($error)."</p>"; ?>
    <?php if(!empty($success)) echo "<p class='text-success'>".htmlspecialchars($success)."</p>"; ?>

    <form method="POST" data-auth="true">

      <div class="mb-1-5">
        <input type="text" name="name" class="form-control" placeholder="Name" value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
      </div>

      <div class="mb-1-5">
        <input type="email" name="email" class="form-control" placeholder="Email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
      </div>

      <div class="mb-1-5 position-relative">
        <input id="register-password" type="password" name="password" class="form-control" placeholder="Password (min 6 chars)" required>
        <div class="mt-2 text-end"><button type="button" class="btn btn-sm btn-link toggle-password" data-target="#register-password">Show</button></div>
      </div>

      <button name="register" class="btn btn-success w-100 mb-2">Register</button>
    </form>

    <p class="small-note text-center mb-0">Already have an account? <a href="login.php">Login</a></p>
  </div>
</div>

<!-- Bootstrap JS (CDN) with local fallback -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>if(typeof bootstrap === 'undefined'){ document.write('<script src="../assets/js/bootstrap.bundle.min.js"><\/script>'); }</script>
<script src="../assets/js/auth.js"></script>
<script src="../assets/js/script.js"></script>
</body>
</html>

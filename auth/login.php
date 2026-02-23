<?php
session_start();
// include config and auth helpers (use absolute paths relative to this file)
include __DIR__ . '/../config/db.php';
include __DIR__ . '/../database/auth.php';

if(isset($_POST['login'])){
  if(loginUser($_POST['email'],$_POST['password'])){
    // Redirect based on role
    if(!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'){
      header("Location: ../admin/dashboard.php");
      exit();
    } else {
      header("Location: ../public/index.php");
      exit();
    }
  }else{
    $error = "Invalid Email or Password";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="../assets/css/bootstrap.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="../assets/css/auth.css">
</head>

<body>

<div class="auth-wrapper">
  <div class="auth-card">
    <h3 class="text-center">Login</h3>

    <?php if(isset($error)) echo "<p class='text-danger'>$error</p>"; ?>

    <form method="POST" data-auth="true">

      <div class="mb-1-5">
        <input type="email" name="email" class="form-control" placeholder="Email" required>
      </div>

      <div class="mb-1-5 position-relative">
        <input id="login-password" type="password" name="password" class="form-control" placeholder="Password" required>
        <div class="mt-2 text-end"><button type="button" class="btn btn-sm btn-link toggle-password" data-target="#login-password">Show</button></div>
      </div>

      <button name="login" class="btn btn-primary w-100 mb-2">Login</button>
    </form>

    <p class="small-note text-center mb-0">Don't have an account? <a href="register.php">Register</a></p>
  </div>
</div>

<!-- Bootstrap JS (CDN) with local fallback -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>if(typeof bootstrap === 'undefined'){ document.write('<script src="../assets/js/bootstrap.bundle.min.js"><\/script>'); }</script>
<script src="../assets/js/auth.js"></script>
<script src="../assets/js/script.js"></script>
</body>
</html>

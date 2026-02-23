<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$userName = $_SESSION['user_name'] ?? null;
$cartCount = 0;
if(!empty($_SESSION['cart']) && is_array($_SESSION['cart'])){
    $cartCount = array_sum($_SESSION['cart']);
}
if(empty($_SESSION['csrf_token'])){
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}
?>
<!-- Bootstrap CSS: try CDN first, fall back to local assets files -->

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="/Sabina/assets/css/bootstrap.min.css">
<link rel="stylesheet" href="/Sabina/assets/css/style.css">

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container-fluid">

        <a class="navbar-brand fw-bold text-primary" href="index.php">
            Store Management
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#storeNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="storeNavbar">

            <ul class="navbar-nav me-auto">

                <li class="nav-item">
                    <a class="nav-link active" href="index.php">Home</a>
                </li>


                <li class="nav-item">
                    <a class="nav-link" href="/Sabina/public/checkout.php">Cart
                        <?php if($cartCount > 0): ?>
                            <span class="badge bg-secondary ms-1"><?php echo (int)$cartCount; ?></span>
                        <?php endif; ?>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/Sabina/public/order.php">My Orders</a>
                </li>

            </ul>

            <div class="d-flex">
                <?php if($userName): ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle me-2" data-bs-toggle="dropdown"><?php echo htmlspecialchars($userName); ?></button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/Sabina/public/order.php">My Orders</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" id="logout-btn" data-csrf="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="/Sabina/auth/login.php" class="btn btn-outline-primary me-2">Login</a>
                    <a href="/Sabina/auth/register.php" class="btn btn-primary">Register</a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</nav>

<!-- Bootstrap JS: CDN with local fallback -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>if (typeof bootstrap === 'undefined') { document.write('<script src="assets/js/bootstrap.bundle.min.js"><\/script>'); }</script>
<script src="assets/js/script.js"></script>


<!-- Bootstrap CSS: try CDN first, fall back to local assets files -->

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/css/style.css">

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
                    <a class="nav-link" href="shop.php">Shop</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="cart.php">Cart</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="orders.php">Orders</a>
                </li>

            </ul>

            <div class="d-flex">

                <a href="auth/login.php" class="btn btn-outline-primary me-2">Login</a>
                <a href="auth/register.php" class="btn btn-primary">Register</a>

            </div>

        </div>
    </div>
</nav>

<!-- Bootstrap JS: CDN with local fallback -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>if (typeof bootstrap === 'undefined') { document.write('<script src="assets/js/bootstrap.bundle.min.js"><\/script>'); }</script>
<script src="assets/js/script.js"></script>


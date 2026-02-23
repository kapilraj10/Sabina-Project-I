<?php
session_start();
require_once __DIR__ . '/../database/products.php';

// simple add-to-cart endpoint
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header('Location: /Sabina/public/index.php');
    exit();
}

$token = $_POST['csrf_token'] ?? '';
if(empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)){
    // invalid token; redirect back
    $_SESSION['flash'] = 'Invalid request.';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/Sabina/'));
    exit();
}

$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
if($product_id <= 0){
    $_SESSION['flash'] = 'Invalid product.';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/Sabina/'));
    exit();
}

$product = getProductById($product_id);
if(!$product){
    $_SESSION['flash'] = 'Product not found.';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/Sabina/'));
    exit();
}

if(!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) $_SESSION['cart'] = [];
// increment quantity
if(isset($_SESSION['cart'][$product_id])){
    $_SESSION['cart'][$product_id] += 1;
} else {
    $_SESSION['cart'][$product_id] = 1;
}

$_SESSION['flash'] = htmlspecialchars($product['name']) . ' added to cart.';
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/Sabina/'));
exit();

?>

<?php
session_start();
require_once __DIR__ . '/../database/orders.php';
error_reporting(E_ALL);
ini_set('display_errors',1);

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header('Location: /Sabina/'); exit();
}

$token = $_POST['csrf_token'] ?? '';
if(empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)){
    $_SESSION['flash'] = 'Invalid request.';
    header('Location: /Sabina/checkout.php'); exit();
}

$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$region = trim($_POST['region'] ?? '');
$address = trim($_POST['address'] ?? '');

if($name === '' || $address === '' || $phone === ''){
    $_SESSION['flash'] = 'Please fill required fields.';
    header('Location: /Sabina/checkout.php'); exit();
}

$cart = $_SESSION['cart'] ?? [];
if(empty($cart)){
    $_SESSION['flash'] = 'Your cart is empty.';
    header('Location: /Sabina/'); exit();
}

$customer = ['name'=>$name,'email'=>$email,'phone'=>$phone,'address'=>$address,'region'=>$region];
$user_id = $_SESSION['user_id'] ?? null;
$order_id = createOrder($customer, $cart, $user_id);
if($order_id){
    // clear cart
    unset($_SESSION['cart']);
    $_SESSION['flash'] = 'Order placed. Your order id is ' . $order_id;
    header('Location: /Sabina/public/order_success.php?id=' . $order_id);
    exit();
} else {
    $_SESSION['flash'] = 'Failed to place order. Try again.';
    header('Location: /Sabina/checkout.php'); exit();
}

?>

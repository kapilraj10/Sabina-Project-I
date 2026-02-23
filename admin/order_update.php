<?php
session_start();
require_once __DIR__ . '/../database/auth.php';
require_once __DIR__ . '/../database/orders.php';
require_once __DIR__ . '/../config/db.php';
if(!isAdmin()){
    header('Location: /Sabina/'); exit();
}
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header('Location: /Sabina/admin/orders.php'); exit();
}
$token = $_POST['csrf_token'] ?? '';
if(empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)){
    $_SESSION['flash'] = 'Invalid request.';
    header('Location: /Sabina/admin/orders.php'); exit();
}
$order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
$status = $_POST['status'] ?? '';
if($order_id <= 0 || $status === ''){
    $_SESSION['flash'] = 'Invalid data.';
    header('Location: /Sabina/admin/orders.php'); exit();
}
// attempt update and capture DB error if any
$ok = updateOrderStatus($order_id, $status);
if($ok){
    $_SESSION['flash'] = 'Order status updated.';
} else {
    $dberr = mysqli_error($conn) ?: '';
    $_SESSION['flash'] = 'Failed to update.' . ($dberr ? ' DB: ' . $dberr : '');
}
header('Location: /Sabina/admin/orders.php'); exit();

?>

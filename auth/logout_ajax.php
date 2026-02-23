<?php
// Logout endpoint for AJAX requests. Returns JSON instead of redirect.
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../database/auth.php';

// Only accept POST
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if($method !== 'POST'){
    http_response_code(405);
    echo json_encode(['success'=>false,'message'=>'Method not allowed']);
    exit();
}

// Read JSON body
$body = file_get_contents('php://input');
$data = json_decode($body, true) ?: [];
$token = $data['csrf_token'] ?? '';
if(empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)){
    http_response_code(400);
    echo json_encode(['success'=>false,'message'=>'Invalid CSRF token']);
    exit();
}

// Perform logout
logoutUser();
// Return JSON with redirect location
echo json_encode(['success'=>true,'redirect'=>'/Sabina/']);
exit();

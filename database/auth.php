<?php
// Simple authentication helpers for the Sabina project
// Requires ../config/db.php which defines $conn (mysqli)

require_once __DIR__ . '/../config/db.php';

function registerUser($name, $email, $password, $role = 'user'){
    global $conn;
    $name = mysqli_real_escape_string($conn, $name);
    $email = mysqli_real_escape_string($conn, $email);
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if(!$stmt) return false;
    mysqli_stmt_bind_param($stmt, 'ssss', $name, $email, $hash, $role);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}

function loginUser($email, $password){
    global $conn;
    // Start session if not already
    if(session_status() === PHP_SESSION_NONE) session_start();

    $email = mysqli_real_escape_string($conn, $email);
    $sql = "SELECT id, name, email, password, role FROM users WHERE email = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    if(!$stmt) return false;
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id, $name, $uemail, $hash, $role);
    if(mysqli_stmt_fetch($stmt)){
        mysqli_stmt_close($stmt);
        if(password_verify($password, $hash)){
            // populate session
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $uemail;
            $_SESSION['user_role'] = $role;
            return true;
        }
    } else {
        mysqli_stmt_close($stmt);
    }
    return false;
}

function logoutUser(){
    if(session_status() === PHP_SESSION_NONE) session_start();
    // Unset session variables and destroy
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

function isLoggedIn(){
    if(session_status() === PHP_SESSION_NONE) session_start();
    return !empty($_SESSION['user_id']);
}

function isAdmin(){
    if(session_status() === PHP_SESSION_NONE) session_start();
    return (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');
}

?>

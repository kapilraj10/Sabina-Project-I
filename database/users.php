<?php
// User administration helpers
// Requires config/db.php which defines $conn (mysqli)
require_once __DIR__ . '/../config/db.php';

/**
 * Return all users as an array of associative arrays.
 * Excludes password field.
 */
function getAllUsers(){
    global $conn;
    $users = [];
    $sql = "SELECT id, name, email, role, created_at FROM users ORDER BY id DESC";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        while($row = mysqli_fetch_assoc($res)){
            $users[] = $row;
        }
        mysqli_stmt_close($stmt);
    }
    return $users;
}

/**
 * Delete a user by id. Returns true on success.
 */
function deleteUserById($id){
    global $conn;
    $sql = "DELETE FROM users WHERE id = ? LIMIT 1";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, 'i', $id);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }
    return false;
}

?>

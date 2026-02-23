<?php
// Category helpers
// Requires ../config/db.php which defines $conn (mysqli)
require_once __DIR__ . '/../config/db.php';

function createCategory($name, $description, $imagePath = null){
    global $conn;
    $sql = "INSERT INTO categories (name, description, image, created_at) VALUES (?, ?, ?, NOW())";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, 'sss', $name, $description, $imagePath);
        $ok = mysqli_stmt_execute($stmt);
        if($ok){
            $id = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
            return $id;
        }
        mysqli_stmt_close($stmt);
    }
    return false;
}

/**
 * Get categories. If $search is provided, filter by name or description (wildcard match).
 * @param string|null $search
 * @return array
 */
function getAllCategories($search = null){
    global $conn;
    $cats = [];
    if($search === null || $search === ''){
        $sql = "SELECT id, name, description, image, created_at FROM categories ORDER BY id DESC";
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            while($row = mysqli_fetch_assoc($res)){
                $cats[] = $row;
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        $sql = "SELECT id, name, description, image, created_at FROM categories WHERE name LIKE ? OR description LIKE ? ORDER BY id DESC";
        if($stmt = mysqli_prepare($conn, $sql)){
            $term = '%' . $search . '%';
            mysqli_stmt_bind_param($stmt, 'ss', $term, $term);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            while($row = mysqli_fetch_assoc($res)){
                $cats[] = $row;
            }
            mysqli_stmt_close($stmt);
        }
    }
    return $cats;
}

function getCategoryById($id){
    global $conn;
    $sql = "SELECT id, name, description, image, created_at FROM categories WHERE id = ? LIMIT 1";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($res);
        mysqli_stmt_close($stmt);
        return $row ?: null;
    }
    return null;
}

function updateCategory($id, $name, $description, $imagePath = null){
    global $conn;
    if($imagePath === null){
        // update without changing image
        $sql = "UPDATE categories SET name = ?, description = ? WHERE id = ?";
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, 'ssi', $name, $description, $id);
            $ok = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return $ok;
        }
    } else {
        $sql = "UPDATE categories SET name = ?, description = ?, image = ? WHERE id = ?";
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, 'sssi', $name, $description, $imagePath, $id);
            $ok = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return $ok;
        }
    }
    return false;
}

function deleteCategoryById($id){
    global $conn;
    $sql = "DELETE FROM categories WHERE id = ? LIMIT 1";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, 'i', $id);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }
    return false;
}

?>

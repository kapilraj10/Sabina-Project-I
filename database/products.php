<?php
// Product helpers
// Requires ../config/db.php which defines $conn (mysqli)
require_once __DIR__ . '/../config/db.php';

function createProduct($name, $category_id, $price, $imagePath = null){
    global $conn;
    $sql = "INSERT INTO products (name, category_id, price, image, created_at) VALUES (?, ?, ?, ?, NOW())";
    if($stmt = mysqli_prepare($conn, $sql)){
    // types: s (name), i (category_id), d (price), s (imagePath)
    mysqli_stmt_bind_param($stmt, 'sids', $name, $category_id, $price, $imagePath);
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

function getAllProducts(){
    global $conn;
    $items = [];
    $sql = "SELECT p.id, p.name, p.price, p.image, p.created_at, p.category_id, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        while($row = mysqli_fetch_assoc($res)){
            $items[] = $row;
        }
        mysqli_stmt_close($stmt);
    }
    return $items;
}

/**
 * Get products with optional search and category filter.
 * @param string|null $search
 * @param int|null $category_id
 * @return array
 */
function getProducts($search = null, $category_id = null){
    global $conn;
    $items = [];
    $base = "SELECT p.id, p.name, p.price, p.image, p.created_at, p.category_id, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id";
    $conds = [];
    $types = '';
    $params = [];

    if($search !== null && $search !== ''){
        $conds[] = "(p.name LIKE ? OR p.description LIKE ? )";
        $term = '%' . $search . '%';
        $types .= 'ss';
        $params[] = $term;
        $params[] = $term;
    }
    if($category_id !== null && $category_id > 0){
        $conds[] = "p.category_id = ?";
        $types .= 'i';
        $params[] = (int)$category_id;
    }

    if(count($conds) > 0){
        $base .= ' WHERE ' . implode(' AND ', $conds);
    }
    $base .= ' ORDER BY p.id DESC';

    if($stmt = mysqli_prepare($conn, $base)){
        if(!empty($types)){
            // bind params dynamically
            $refs = [];
            $refs[] = & $types;
            for($i=0;$i<count($params);$i++){
                $refs[] = & $params[$i];
            }
            call_user_func_array('mysqli_stmt_bind_param', array_merge([$stmt], $refs));
        }
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        while($row = mysqli_fetch_assoc($res)){
            $items[] = $row;
        }
        mysqli_stmt_close($stmt);
    }
    return $items;
}

function getProductById($id){
    global $conn;
    $sql = "SELECT id, name, price, image, category_id, created_at FROM products WHERE id = ? LIMIT 1";
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

function updateProduct($id, $name, $category_id, $price, $imagePath = null){
    global $conn;
    if($imagePath === null){
        $sql = "UPDATE products SET name = ?, category_id = ?, price = ? WHERE id = ?";
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, 'sidi', $name, $category_id, $price, $id);
            $ok = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return $ok;
        }
    } else {
        $sql = "UPDATE products SET name = ?, category_id = ?, price = ?, image = ? WHERE id = ?";
        if($stmt = mysqli_prepare($conn, $sql)){
            // types: s (name), i (category_id), d (price), s (imagePath), i (id)
            mysqli_stmt_bind_param($stmt, 'sidsi', $name, $category_id, $price, $imagePath, $id);
            $ok = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return $ok;
        }
    }
    return false;
}

function deleteProductById($id){
    global $conn;
    $sql = "DELETE FROM products WHERE id = ? LIMIT 1";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, 'i', $id);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }
    return false;
}

?>

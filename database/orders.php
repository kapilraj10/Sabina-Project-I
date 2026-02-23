<?php
// Orders helper functions
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/products.php';

/**
 * Create an order from cart and customer data.
 * @param array $customer ['name','email','phone','address','region']
 * @param array $cart associative array product_id => qty
 * @param int|null $user_id
 * @return int|false order id on success
 */
function createOrder(array $customer, array $cart, $user_id = null){
    global $conn;
    if(empty($cart)) return false;

    mysqli_begin_transaction($conn);
    try {
        // compute totals and prepare items
        $total = 0.0;
        $items = [];
        foreach($cart as $pid => $qty){
            $p = getProductById((int)$pid);
            if(!$p) continue;
            $unit = (float)$p['price'];
            $lineTotal = $unit * (int)$qty;
            $items[] = [
                'product_id' => (int)$pid,
                'product_name' => $p['name'],
                'qty' => (int)$qty,
                'unit_price' => $unit,
                'total_price' => $lineTotal
            ];
            $total += $lineTotal;
        }

        if(empty($items)){
            mysqli_roll_back($conn);
            return false;
        }

        $sql = "INSERT INTO orders (user_id, customer_name, customer_email, customer_phone, customer_address, customer_region, total, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
        if($stmt = mysqli_prepare($conn, $sql)){
            $cust_name = $customer['name'] ?? '';
            $cust_email = $customer['email'] ?? null;
            $cust_phone = $customer['phone'] ?? null;
            $cust_address = $customer['address'] ?? null;
            $cust_region = $customer['region'] ?? null;
            // ensure $user_id is an int or null
            if($user_id !== null) $user_id = (int)$user_id;
            // bind params: i (user_id), s (name), s (email), s (phone), s (address), s (region), d (total)
            mysqli_stmt_bind_param($stmt, 'isssssd', $user_id, $cust_name, $cust_email, $cust_phone, $cust_address, $cust_region, $total);
            $ok = mysqli_stmt_execute($stmt);
            if(!$ok){ mysqli_stmt_close($stmt); mysqli_roll_back($conn); return false; }
            $order_id = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
        } else {
            mysqli_roll_back($conn); return false;
        }

        // insert order items using a single prepared statement executed per item
        $sqlItem = "INSERT INTO order_items (order_id, product_id, product_name, qty, unit_price, total_price) VALUES (?, ?, ?, ?, ?, ?)";
        if(!$stmtItem = mysqli_prepare($conn, $sqlItem)){
            mysqli_roll_back($conn); return false;
        }
        foreach($items as $it){
            $orderid = $order_id;
            $prodid = $it['product_id'];
            $pname = $it['product_name'];
            $qty = $it['qty'];
            $uprice = $it['unit_price'];
            $tprice = $it['total_price'];
            // types: i (order_id), i (product_id), s (product_name), i (qty), d (unit_price), d (total_price)
            mysqli_stmt_bind_param($stmtItem, 'iisidd', $orderid, $prodid, $pname, $qty, $uprice, $tprice);
            $exec = mysqli_stmt_execute($stmtItem);
            if(!$exec){ mysqli_stmt_close($stmtItem); mysqli_roll_back($conn); return false; }
        }
        mysqli_stmt_close($stmtItem);

        mysqli_commit($conn);
        return $order_id;

    } catch(Exception $e){
        mysqli_roll_back($conn);
        return false;
    }
}

function getAllOrders(){
    global $conn;
    $rows = [];
    $sql = "SELECT o.*, u.name as user_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.id DESC";
    if($res = mysqli_query($conn, $sql)){
        while($r = mysqli_fetch_assoc($res)) $rows[] = $r;
    }
    return $rows;
}

function getOrderById($id){
    global $conn;
    $sql = "SELECT o.*, u.name as user_name FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = ? LIMIT 1";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $order = mysqli_fetch_assoc($res);
        mysqli_stmt_close($stmt);
        if($order){
            // fetch items
            $sqlItems = "SELECT * FROM order_items WHERE order_id = ?";
            if($s2 = mysqli_prepare($conn, $sqlItems)){
                mysqli_stmt_bind_param($s2, 'i', $id);
                mysqli_stmt_execute($s2);
                $r2 = mysqli_stmt_get_result($s2);
                $items = [];
                while($it = mysqli_fetch_assoc($r2)) $items[] = $it;
                mysqli_stmt_close($s2);
                $order['items'] = $items;
            }
        }
        return $order ?: null;
    }
    return null;
}

function updateOrderStatus($order_id, $status){
    global $conn;
    $allowed = ['pending','processing','completed','cancelled'];
    if(!in_array($status, $allowed)) return false;
    $sql = "UPDATE orders SET status = ? WHERE id = ? LIMIT 1";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, 'si', $status, $order_id);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }
    return false;
}

?>

<?php
session_start();
require_once __DIR__ . '/../database/auth.php';
require_once __DIR__ . '/../database/orders.php';
require_once __DIR__ . '/../config/db.php';

if(!isLoggedIn()){
		header('Location: /Sabina/auth/login.php'); exit();
}

$user_id = $_SESSION['user_id'];
$orders = [];
$sql = "SELECT id, total, status, created_at FROM orders WHERE user_id = ? ORDER BY id DESC";
if($stmt = mysqli_prepare($conn, $sql)){
		mysqli_stmt_bind_param($stmt, 'i', $user_id);
		mysqli_stmt_execute($stmt);
		$res = mysqli_stmt_get_result($stmt);
		while($row = mysqli_fetch_assoc($res)) $orders[] = $row;
		mysqli_stmt_close($stmt);
}

?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>My Orders</title>
<link rel="stylesheet" href="/Sabina/assets/css/style.css"></head><body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>
<div class="container mt-4">
	<h3>My Orders</h3>
	<?php if(empty($orders)): ?>
		<div class="alert alert-secondary">You have not placed any orders yet.</div>
	<?php else: ?>
		<table class="table">
			<thead><tr><th>Order ID</th><th>Total</th><th>Status</th><th>Placed</th><th>Action</th></tr></thead>
			<tbody>
			<?php foreach($orders as $o): ?>
				<tr>
					<td><?php echo $o['id']; ?></td>
					<td>Rs <?php echo number_format((float)$o['total'],2); ?></td>
					<td><?php echo htmlspecialchars($o['status']); ?></td>
					<td><?php echo $o['created_at']; ?></td>
					<td><a class="btn btn-sm btn-outline-primary" href="/Sabina/public/order_success.php?id=<?php echo $o['id']; ?>">View</a></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
</div>
</body></html>

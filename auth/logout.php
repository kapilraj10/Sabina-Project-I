<?php
// include auth helper and logout
include __DIR__ . '/../database/auth.php';
logoutUser();
// After logout, redirect to the auth login page
header("Location: login.php");
exit();
?>

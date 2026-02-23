<?php
$conn = mysqli_connect("localhost", "root", "admin", "sabina");

if (!$conn){
    die("Database Connection Failed: " . mysqli_connect_error());
}
?>
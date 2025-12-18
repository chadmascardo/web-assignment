<?php
session_start();

$product_id = $_GET['id'];

if (isset($_SESSION['cart'][$product_id])) {
    unset($_SESSION['cart'][$product_id]);
}

header("Location: pos.php");
exit;
?>
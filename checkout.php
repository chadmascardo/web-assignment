<?php
session_start();
require_once "../inventory/db.php";

if (empty($_SESSION['cart'])) {
    die("Cart is empty. Cannot checkout.");
}

$payment = (float)$_POST['payment'];

$grand_total = 0;
foreach ($_SESSION['cart'] as $item) {
    $grand_total += $item['price'] * $item['quantity'];
}

if ($payment < $grand_total) {
    die("Insufficient payment. Total is ₱" . number_format($grand_total, 2));
}

$change = $payment - $grand_total;

foreach ($_SESSION['cart'] as $product_id => $item) {
    $stmt = $pdo->prepare("UPDATE items SET quantity = quantity - ? WHERE product_id = ?");
    $stmt->execute([$item['quantity'], $product_id]);
}

$_SESSION['last_sale'] = [
    'cart' => $_SESSION['cart'],
    'total' => $grand_total,
    'payment' => $payment,
    'change' => $change
];

$_SESSION['cart'] = [];

header("Location: receipt.php");
exit;
?>
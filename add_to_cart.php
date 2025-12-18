<?php
session_start();
require_once "../inventory/db.php";

$product_id = $_POST['product_id'];
$quantity = (int)$_POST['quantity'];

$stmt = $pdo->prepare("SELECT * FROM items WHERE product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Product not found.");
}

if ($quantity > $product['quantity']) {
    die("Insufficient stock. Only " . $product['quantity'] . " available.");
}

if (isset($_SESSION['cart'][$product_id])) {
    $new_quantity = $_SESSION['cart'][$product_id]['quantity'] + $quantity;
    
    if ($new_quantity > $product['quantity']) {
        die("Cannot add more. Total would exceed available stock of " . $product['quantity']);
    }
    
    $_SESSION['cart'][$product_id]['quantity'] = $new_quantity;
} else {
    $_SESSION['cart'][$product_id] = [
        'name' => $product['name'],
        'price' => $product['price'],
        'quantity' => $quantity
    ];
}

header("Location: pos.php");
exit;
?>
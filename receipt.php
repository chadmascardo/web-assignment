<?php
session_start();

if (!isset($_SESSION['last_sale'])) {
    die("No receipt to display.");
}

$sale = $_SESSION['last_sale'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Receipt</title>
</head>
<body>

<h2>SALES RECEIPT</h2>
<p>Date: <?= date('Y-m-d H:i:s') ?></p>
<hr>

<table border="1" cellpadding="8">
    <tr>
        <th>Product</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Subtotal</th>
    </tr>
    
    <?php foreach ($sale['cart'] as $id => $item): ?>
    <tr>
        <td><?= htmlspecialchars($item['name']) ?></td>
        <td>₱<?= number_format($item['price'], 2) ?></td>
        <td><?= $item['quantity'] ?></td>
        <td>₱<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
    </tr>
    <?php endforeach; ?>
    
    <tr>
        <td colspan="3"><strong>GRAND TOTAL:</strong></td>
        <td><strong>₱<?= number_format($sale['total'], 2) ?></strong></td>
    </tr>
    <tr>
        <td colspan="3">Payment:</td>
        <td>₱<?= number_format($sale['payment'], 2) ?></td>
    </tr>
    <tr>
        <td colspan="3">Change:</td>
        <td>₱<?= number_format($sale['change'], 2) ?></td>
    </tr>
</table>

<br>
<p>Thank you for your purchase!</p>

<hr>
<a href="pos.php">New Transaction</a> | 
<a href="../index.php">Back to Inventory</a>

</body>
</html>
<?php
session_start();
require_once "../inventory/db.php";

if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = [];
}

$error = "";
$product = null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
    $product_id = strtoupper(trim($_POST['product_id']));
    
    if (!preg_match("/^[A-Z]{3}-[0-9]{5}$/", $product_id)) {
        $error = "Invalid Product ID format. Use: AAA-12345";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM items WHERE product_id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product) {
            $error = "Product not found in inventory.";
        }
    }
}
$grand_total = 0;
foreach($_SESSION['cart'] as $item){
    $grand_total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS System</title>
</head>
<body>
    
<h2>Point of Sale System</h2>
<a href="../inventory/index.php">Back to Inventory</a>
<hr>
<h3>Scan Product</h3>
<?php if ($error): ?>
    <p style="color: red;"><?= $error ?></p>
<?php endif; ?>

<form method="post">
    <label>Product ID:</label>
    <input type="text" name="product_id" placeholder="ABC-12345" required>
    <button type="submit" name="search">Search</button>
</form>

<?php if ($product): ?>
    <h4>Product Found:</h4>
    <p>
        <strong>Name:</strong> <?= htmlspecialchars($product['name']) ?><br>
        <strong>Price:</strong> ₱<?= number_format($product['price'], 2) ?><br>
        <strong>Available Stock:</strong> <?= $product['quantity'] ?>
    </p>
    
    <form action="add_to_cart.php" method="post">
        <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
        <label>Quantity:</label>
        <input type="number" name="quantity" min="1" max="<?= $product['quantity'] ?>" value="1" required>
        <button type="submit">Add to Cart</button>
    </form>
<?php endif; ?>

<hr>

<h3>Shopping Cart</h3>

<?php if (empty($_SESSION['cart'])): ?>
    <p>Cart is empty.</p>
<?php else: ?>
    <table border="1" cellpadding="8">
        <tr>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
            <th>Action</th>
        </tr>
        
        <?php foreach ($_SESSION['cart'] as $id => $item): ?>
        <tr>
            <td><?= htmlspecialchars($id) ?></td>
            <td><?= htmlspecialchars($item['name']) ?></td>
            <td>₱<?= number_format($item['price'], 2) ?></td>
            <td><?= $item['quantity'] ?></td>
            <td>₱<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
            <td>
                <a href="remove_from_cart.php?id=<?= $id ?>" 
                   onclick="return confirm('Remove this item?');">Remove</a>
            </td>
        </tr>
        <?php endforeach; ?>
        
        <tr>
            <td colspan="4"><strong>GRAND TOTAL:</strong></td>
            <td colspan="2"><strong>₱<?= number_format($grand_total, 2) ?></strong></td>
        </tr>
    </table>
    
    <br>
    
    <h3>Checkout</h3>
    <form action="checkout.php" method="post">
        <label>Payment Amount:</label>
        <input type="number" name="payment" step="0.01" min="<?= $grand_total ?>" required>
        <button type="submit">Complete Sale</button>
    </form>
<?php endif; ?>

</body>
</html>



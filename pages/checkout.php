<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';
$user_id = $_SESSION['user_id'];

// Fetch cart items
$stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($cart_items)) {
    echo "<p>Your cart is empty. <a href='../index.php'>Go shopping</a></p>";
    exit();
}

// Get product info
$product_ids = array_column($cart_items, 'product_id');
$placeholders = implode(',', array_fill(0, count($product_ids), '?'));
$stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
$stmt->execute($product_ids);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare order items and calculate total
$total_cost = 0;
$order_items = [];
foreach ($products as $product) {
    foreach ($cart_items as $cart_item) {
        if ($cart_item['product_id'] == $product['id']) {
            $quantity = $cart_item['quantity'];
            $total_cost += $product['price'] * $quantity;
            $order_items[] = [
                'product_id' => $product['id'],
                'name' => $product['name'],
                'quantity' => $quantity,
                'price' => $product['price']
            ];
        }
    }
}

// Handle order submission
if (isset($_POST['place_order'])) {
    $address = $_POST['address'];
    $city = $_POST['city'];
    $postal_code = $_POST['pin_code'];
    $payment_method = $_POST['payment_method'];
    $order_details = json_encode($order_items);

    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, order_details, address, city, pin_code, payment_method, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$user_id, $total_cost, $order_details, $address, $city, $pin_code, $payment_method]);

    // Clear the cart after placing order
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);

    header("Location: order_success.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout</title>
<style>
    body { font-family: Arial, sans-serif; background-color: #f8f9fa; margin: 0; padding: 20px; color: #333; }
    .container { max-width: 800px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    input, select, textarea { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px; }
    h2 { text-align: center; }
    table { width: 100%; margin-top: 20px; border-collapse: collapse; }
    th, td { padding: 12px; border-bottom: 1px solid #ddd; }
    .total { text-align: right; font-size: 1.5em; margin-top: 20px; }
    button { display: block; width: 100%; padding: 12px; background: #28a745; color: #fff; border: none; font-size: 1.2em; border-radius: 5px; cursor: pointer; margin-top: 20px; }
    button:hover { background: #218838; }
</style>
</head>
<body>
<div class="container">
    <h2>Checkout</h2>

    <form method="POST">
        <h3>Shipping Address</h3>
        <input type="text" name="address" placeholder="Full Address" required>
        <input type="text" name="city" placeholder="City" required>
        <input type="text" name="pin_code" placeholder="Pin Code" required>

        <h3>Payment Method</h3>
        <select name="payment_method" required>
            <option value="Cash on Delivery">Cash on Delivery</option>
            <option value="Credit Card">Credit Card</option>
            <option value="UPI">UPI</option>
        </select>

        <h3>Order Summary</h3>
        <table>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Subtotal</th>
            </tr>
            <?php foreach ($order_items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>$<?= number_format($item['price'], 2) ?></td>
                    <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <div class="total">Total: $<?= number_format($total_cost, 2) ?></div>

        <button type="submit" name="place_order">Place Order</button>
    </form>
</div>
</body>
</html>

<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';
$user_id = $_SESSION['user_id'];

// Get the latest order for the user
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$user_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "<p>No recent orders found. <a href='../index.php'>Go shopping</a></p>";
    exit();
}

// Decode the order items
$order_items = json_decode($order['order_details'], true);
if (empty($order_items)) {
    echo "<p>Order details are missing or corrupted.</p>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Successful</title>
<style>
    body { font-family: Arial, sans-serif; background-color: #f8f9fa; margin: 0; padding: 20px; color: #333; }
    .container { max-width: 800px; margin: auto; background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); text-align: center; }
    h2 { color: #28a745; font-size: 2em; margin-bottom: 20px; }
    p { font-size: 1.2em; margin-bottom: 30px; }
    .order-details, .order-summary { text-align: left; margin-top: 30px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: center; }
    .order-info { background: #f1f1f1; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
    a { display: inline-block; padding: 12px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; font-size: 1.1em; transition: background-color 0.3s; margin-top: 20px; }
    a:hover { background-color: #0056b3; }
</style>
</head>
<body>
<div class="container">
    <h2>Thank You for Your Order!</h2>
    
    <p>Your order has been placed successfully. Here are your order details:</p>

    <div class="order-info">
        <p><strong>Order ID:</strong> <?= $order['id'] ?></p>
        <p><strong>Total Amount:</strong> $<?= number_format($order['total_amount'], 2) ?></p>
        <p><strong>Order Date:</strong> <?= $order['created_at'] ?></p>
        <p><strong>Shipping Address:</strong> <?= htmlspecialchars($order['address']) ?>, <?= htmlspecialchars($order['city']) ?>, <?= htmlspecialchars($order['pin_code']) ?></p>
        <p><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
    </div>

    <div class="order-summary">
        <h3>Items Ordered</h3>
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
    </div>

    <a href="../index.php">Continue Shopping</a>
</div>
</body>
</html>


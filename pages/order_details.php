<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

if (!isset($_GET['id'])) {
    echo "Invalid order ID.";
    exit();
}

$order_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Fetch the order
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "Order not found.";
    exit();
}

// Decode the order items JSON
$order_items = json_decode($order['order_details'], true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #343a40;
            color: #fff;
        }
        .info {
            margin-bottom: 20px;
        }
        a {
            display: inline-block;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Order #<?= $order['id'] ?> Details</h2>

    <div class="info">
        <p><strong>Total Amount:</strong> $<?= number_format($order['total_amount'], 2) ?></p>
        <p><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($order['address']) ?>, <?= htmlspecialchars($order['city']) ?>, <?= htmlspecialchars($order['pin_code']) ?></p>
        <p><strong>Order Date:</strong> <?= $order['created_at'] ?></p>
    </div>

    <h3>Items:</h3>
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($order_items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>$<?= number_format($item['price'], 2) ?></td>
                    <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="orders_history.php">Back to Order History</a>
</div>

</body>
</html>

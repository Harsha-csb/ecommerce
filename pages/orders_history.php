<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';
$user_id = $_SESSION['user_id'];

// Fetch user's orders
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
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
    <h2>Your Order History</h2>

    <?php if (empty($orders)): ?>
        <p>You have no orders yet. <a href="../index.php">Start shopping</a></p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Total Amount</th>
                    <th>Payment Method</th>
                    <th>Order Date</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= $order['id'] ?></td>
                        <td>$<?= number_format($order['total_amount'], 2) ?></td>
                        <td><?= htmlspecialchars($order['payment_method']) ?></td>
                        <td><?= $order['created_at'] ?></td>
                        <td>
                            <a href="order_details.php?id=<?= $order['id'] ?>">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="../index.php">Back to Home</a>
</div>

</body>
</html>

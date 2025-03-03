


<?php
session_start();
include 'includes/db.php'; // Include the database connection

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: pages/login.php");
    exit();
}

// Fetch products from the database
$stmt = $conn->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('khana.png');
            background-size: cover;
            background-position: center;
            position: relative;
            color: #333;
        }

        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 204, 0.4);
            z-index: -1;
        }

        header {
            background-color: rgba(255, 165, 0, 0.8);
           
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            padding: 20px 0;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
        }
        .header-container {
    text-align: center;
}

.header-logo {
    width: 300px; /* Adjust logo size */
    height: auto;
    display: block;
    margin: 0 auto;
}

        header h1 {
            margin: 0;
            font-size: 3rem;
            color: #fff;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        nav {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        nav a, .logout-button {
            background-color: #ff6347;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 20px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        nav a:hover, .logout-button:hover {
            background-color: #e64d3a;
            transform: scale(1.05);
        }

        .main-container {
            padding: 40px;
            max-width: 1100px;
            margin: auto;
        }

        .product-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .product {
            background-color: rgba(255, 250, 205, 0.8);
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
        }

        .product:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 14px rgba(0, 0, 0, 0.2);
        }

        .product h3 {
            font-size: 1.4rem;
            margin-bottom: 10px;
            color: #ff8c00;
            text-transform: capitalize;
        }

        .product p {
            font-size: 1rem;
            color: #555;
            margin-bottom: 10px;
        }

        .product-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            margin: 15px 0;
            border-radius: 10px;
        }

        .add-to-cart-button {
            background-color: #32cd32;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        .add-to-cart-button:hover {
            background-color: #228b22;
            transform: scale(1.05);
        }

        footer {
            background-color: rgba(255, 165, 0, 0.8);
            padding: 25px 0;
            color: #fff;
            text-align: center;
            font-size: 0.9rem;
            box-shadow: 0 -4px 6px rgba(0, 0, 0, 0.2);
        }

        .order-history-button {
            background-color: #4169e1;
            color: white;
            padding: 10px 15px;
            border-radius: 20px;
            text-decoration: none;
            transition: background-color 0.3s, transform 0.2s;
            margin-left: 10px;
        }

        .order-history-button:hover {
            background-color: #364faf;
            transform: scale(1.05);
        }

        .cart-link {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: white;
            font-size: 1rem;
        }

        .cart-icon {
            margin-right: 5px;
            height: 25px;
        }

        @media (min-width: 768px) {
            .product-list {
                grid-template-columns: repeat(3, 1fr);
            }
        }
    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Store</title>
</head>
<body style="background-image: url('khana.png'); background-size: cover; background-position:; hegiht=100% width= 100%;">
    <header  >
        <div class="header-container">
        <img src="kkk.png" alt="HR STORES Logo" class="header-logo">
            <h1 style="color:white; front-style:italic;">Welcome to HR STORES</h1>
            <nav>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="pages/login.php">Login</a>
                    <a href="pages/register.php">Register</a>
                <?php else:
                    $stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
                    $stmt->execute([$_SESSION['user_id']]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                ?>
                    <div class="user-info">
                        <span><?= htmlspecialchars($user['email']); ?></span>
                    </div>
                    <form method="POST" style="display: inline;">
                        <button type="submit" name="logout" class="logout-button">Logout</button>
                    </form>
                <?php endif; ?>
                <a href="pages/cart.php" class="cart-link">
                    <img src="images/demo.png" alt="Cart" class="cart-icon" height="25px">
                    Cart
                </a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="pages/orders_history.php">Order History</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <div class="main-container">
        <main>
            <h2 style="color:black;">Products</h2>
            <div class="product-list">
                <?php if (empty($products)) : ?>
                    <p>No products available.</p>
                <?php else : ?>
                    <?php foreach ($products as $product) : ?>

        <div class="product">

           

            <h3><?= htmlspecialchars($product['name']); ?></h3>

            <p>Price: $<?= number_format($product['price'], 2); ?></p>

            <p><?= htmlspecialchars($product['description']); ?></p>

            <?php if (!empty($product['image'])) : ?>

                <img src="images/<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>" class="product-image">

            <?php endif; ?>

            <form method="POST" action="pages/cart.php">

                <input type="hidden" name="product_id" value="<?= $product['id']; ?>">

                <button type="submit" name="add_to_cart" class="add-to-cart-button">Add to Cart</button>

            </form>

        </div>

    <?php endforeach; ?>

<?php endif; ?>

            </div>

        </main>

    </div>

    <footer>

        <p>&copy; <?= date('Y'); ?> Online Store. All rights reserved.</p>

        <p>created by HARSHA</p>

    </footer>

</body>

</html>
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
    background-color: #f7f7f7;
    color: #333;
}

/* Header */
header {
    background-color:rgb(54, 96, 173);
    color: white;
    padding: 20px;
    text-align: center;
    position: relative;
}

.header-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 20px;
    width: 100%; /* Ensures full width */
}

header h1 {
    margin: 0;
    font-size: 2em;
}

/* Updated navigation styling to align buttons in one row */
nav {
    display: flex;
    align-items: center; /* Align buttons vertically centered */
}

nav a, .logout-button {
    color: white;
    text-decoration: none;
    margin: 0 15px;
    font-size: 1em;
    text-transform: uppercase;
    display: inline-block;
}

nav a:hover, .logout-button:hover {
    text-decoration: underline;
}

.logout-button {
    background-color: #ff5733;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.logout-button:hover {
    background-color: #e84e2f;
    padding: 20px;
    display: flex;
    justify-content: center;
    flex-wrap: wrap; /* Allows responsiveness */
}

/* Product Listing */
.product-list {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 20px;
    width: 80%; /* Take up most of the screen */
}

.product {
    background-color: #fff;
    padding: 15px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    text-align: center;
    width: 23%; /* Set width for 4 products per row */
    transition: transform 0.3s ease-in-out;
    margin-bottom: 20px;
}

.product:hover {
    transform: translateY(-10px);
}

.product h3 {
    margin-bottom: 10px;
    font-size: 1.3em;
    color: #333;
}

.product p {
    font-size: 1em;
    color: #777;
    margin-bottom: 10px;
}

.product-image {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 8px;
    margin: 10px 0;
}



.add-to-cart-button {
    background-color: #2ecc71;
    color: white;
    padding: 12px 25px;
    border: none;
    border-radius: 6px;
    font-size: 1.2em;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.3s;
}

.add-to-cart-button:hover {
    background-color: #27ae60;
    transform: scale(1.05);
}

/* Footer */
footer {
    background-color:rgb(77, 137, 198);
    color: white;
    text-align: center;
    padding: 20px 0;
    margin-top: 10px;
}

footer p {
    margin: 0;
    font-size: 1.1em;
}
        </style>
       
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="header-container">
            <h1 style="color:white; front-style:italic;">Welcome to HR STORES</h1>
            <nav>
                <a href="pages/login.php">Login</a>
                <a href="pages/register.php">Register</a>
                <a href="pages/cart.php" class="cart-link">
                    <img src="images/demo.png" alt="Cart" class="cart-icon" height="25px">
                    Cart
                </a>
                <form method="POST" style="display: inline;">
    <button type="submit" name="logout" class="logout-button">Logout</button>
</form>
            </nav>
        </div>
    </header>
    <div class="main-container">
        <main>
            <h2>Products</h2>
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
    </footer>
</body>
</html>



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
    font-family: 'Playfair Display', serif;
    margin: 0;
    padding: 0;
    background-color:rgb(255, 255, 255);
    color: #e0e0e0;
}

/* Header */
header {
    background-color: #181818;
    padding: 30px;
    text-align: center;
}

header h1 {
    margin: 0;
    font-size: 3rem;
    color: #fff;
    font-weight: 700;
    letter-spacing: 2px;
    
}

/* Navigation */
nav {
    display: flex;
    justify-content: flex-end; /* Align to the left */
    gap: 30px;
    margin-top: 20px;
}


nav a, .logout-button {
    background-color: #ff5733;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}

nav a:hover, .logout-button:hover {
    border-color: #fff;
}

/* Product List */
.main-container {
    padding: 50px;
    max-width: 1200px;
    margin: auto;
}

.product-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 40px;
}

.product {
    background-color: #1f1f1f;
    padding: 20px;
    border-radius: 12px;
    text-align: center;
    transition: transform 0.3s ease;
}

.product:hover {
    transform: translateY(-10px);
}

.product h3 {
    font-size: 1.5rem;
    margin-bottom: 10px;
    color: #fff;
}

.product p {
    font-size: 1rem;
    color: #bbb;
}

.product-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    margin: 20px 0;
    border-radius: 10px;
}

/* Button */
.add-to-cart-button {
    background-color: transparent;
    color: #fff;
    border: 1px solid #fff;
    padding: 10px 25px;
    border-radius: 5px;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s;
}

.add-to-cart-button:hover {
    background-color: #fff;
    color: #000;
}

/* Footer */
footer {
    background-color: #181818;
    padding: 30px 0;
    color: #aaa;
    text-align: center;
}
.order-history-button {
    background-color: #3498db; /* Blue color */
    color: white;
    padding: 8px 12px;
    border-radius: 4px;
    text-decoration: none;
    transition: background-color 0.3s;
    margin-left: 10px;
}

.order-history-button:hover {
    background-color: #217dbb;
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
            <h2 style="color:black";> Products</h2>
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
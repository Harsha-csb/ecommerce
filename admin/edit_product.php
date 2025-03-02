<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

if (!isset($_GET['id'])) {
    header("Location: manage_products.php");
    exit();
}

$id = $_GET['id'];

// Fetch product data
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "Product not found.";
    exit();
}

// Update product
if (isset($_POST['update'])) {
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $description = trim($_POST['description']);

    $image = $product['image'];
    
    // If new image uploaded
    if (!empty($_FILES['image']['name'])) {
        $new_image = $_FILES['image']['name'];
        $new_image_tmp = $_FILES['image']['tmp_name'];
        $new_image_path = "../images/" . $new_image;

        if (move_uploaded_file($new_image_tmp, $new_image_path)) {
            // Delete old image
            $old_image_path = "../images/" . $product['image'];
            if (file_exists($old_image_path)) {
                unlink($old_image_path);
            }
            $image = $new_image;
        } else {
            echo "<p style='color:red;'>Failed to upload new image.</p>";
        }
    }

    $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, description = ?, image = ? WHERE id = ?");
    if ($stmt->execute([$name, $price, $description, $image, $id])) {
        echo "<p style='color:green;'>Product updated successfully!</p>";
        header("refresh:1;url=manage_products.php");
        exit();
    } else {
        echo "<p style='color:red;'>Failed to update product.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7fa; }
        .container {
            width: 50%; margin: 50px auto; padding: 30px;
            background-color: #fff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; color: #333; }
        form { display: flex; flex-direction: column; }
        input, textarea { padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px; }
        button {
            padding: 12px; background-color: #28a745; color: white;
            border: none; border-radius: 4px; cursor: pointer; font-size: 1em;
        }
        button:hover { background-color: #218838; }
        img { width: 100px; margin-top: 10px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Product</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Product Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($product['name']); ?>" required>

        <label>Price:</label>
        <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($product['price']); ?>" required>

        <label>Description:</label>
        <textarea name="description" required><?= htmlspecialchars($product['description']); ?></textarea>

        <label>Current Image:</label>
        <img src="../images/<?= htmlspecialchars($product['image']); ?>" alt="Current Image">

        <label>Change Image (optional):</label>
        <input type="file" name="image">

        <button type="submit" name="update">Update Product</button>
    </form>
    <a href="manage_products.php" style="display:block; margin-top:20px; text-align:center;">Back to Products</a>
</div>

</body>
</html>

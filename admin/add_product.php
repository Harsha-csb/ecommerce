<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    
    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_size = $_FILES['image']['size'];
    $image_error = $_FILES['image']['error'];
    
    $image_folder = "../images/";

    // Ensure the images folder exists
    if (!is_dir($image_folder)) {
        mkdir($image_folder, 0755, true);
    }

    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    $image_ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));

    // Validate image
    if (!in_array($image_ext, $allowed_types)) {
        echo "<p style='color: red; text-align: center;'>Invalid image type. Only JPG, JPEG, PNG, and GIF are allowed.</p>";
    } elseif ($image_error !== 0) {
        echo "<p style='color: red; text-align: center;'>There was an error uploading the image.</p>";
    } elseif ($image_size > 2 * 1024 * 1024) { // 2MB limit
        echo "<p style='color: red; text-align: center;'>Image size should not exceed 2MB.</p>";
    } else {
        // Rename image to avoid overwriting
        $new_image_name = uniqid('product_', true) . '.' . $image_ext;
        $image_path = $image_folder . $new_image_name;

        if (move_uploaded_file($image_tmp, $image_path)) {
            // Insert product details into the database
            $stmt = $conn->prepare("INSERT INTO products (name, price, description, image) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $price, $description, $new_image_name]);

            echo "<p style='color: green; text-align: center;'>Product added successfully!</p>";
        } else {
            echo "<p style='color: red; text-align: center;'>Failed to upload the image.</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .container {
            width: 50%;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h2 {
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, textarea {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            text-decoration: none;
            color: #4CAF50;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add Product</h2>
        <form method="POST" enctype="multipart/form-data">
            <label for="name">Product Name:</label>
            <input type="text" name="name" id="name" required>

            <label for="price">Price:</label>
            <input type="number" step="0.01" name="price" id="price" required>

            <label for="description">Description:</label>
            <textarea name="description" id="description" required></textarea>

            <label for="image">Image:</label>
            <input type="file" name="image" id="image" required>

            <button type="submit" name="add_product">Add Product</button>
        </form>
        <div class="back-link">
            <a href="manage_products.php">Back to Manage Products</a>
        </div>
    </div>
</body>
</html>

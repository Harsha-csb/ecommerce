<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the product to get the image name
    $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        $image_path = "../images/" . $product['image'];

        // Delete the product from the database
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        if ($stmt->execute([$id])) {
            
            // Delete the image file if it exists
            if (file_exists($image_path)) {
                unlink($image_path);
            }

            header("Location: manage_products.php?message=Product+deleted+successfully");
            exit();
        } else {
            header("Location: manage_products.php?error=Failed+to+delete+product");
            exit();
        }
    } else {
        header("Location: manage_products.php?error=Product+not+found");
        exit();
    }
} else {
    header("Location: manage_products.php?error=Invalid+product+ID");
    exit();
}
?>

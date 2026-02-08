<?php
include "../config/connection.php";

if (isset($_POST['submit'])) {
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];

    // Validate required fields
    if (empty($product_name) || empty($price) || empty($stock) || empty($description)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'All fields are required'
        ]);
        exit;
    }

    $category_name = "Joystick"; // or make dynamic if needed
    $insert_cat = "INSERT INTO categories (name, description, status, created_at) 
                   VALUES ('$category_name', '$description', 1, NOW())";
    mysqli_query($conn, $insert_cat);
    $category_id = mysqli_insert_id($conn);


    $insert_product = "INSERT INTO products (name, price, stock_quantity, description, category_id, status, created_at) 
                       VALUES ('$product_name', '$price', '$stock', '$description', '$category_id', 1, NOW())";
    mysqli_query($conn, $insert_product);


    if (!empty($_POST['colors'])) {
        $targetDir = "../public/uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        foreach ($_POST['colors'] as $color) {
            $image_name = "";
            $fileKey = "image_" . $color;

            if (!empty($_FILES[$fileKey]['name'])) {
                $image_name = time() . "_" . basename($_FILES[$fileKey]["name"]);
                $targetFilePath = $targetDir . $image_name;

                if (!move_uploaded_file($_FILES[$fileKey]["tmp_name"], $targetFilePath)) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => "Failed to upload image for $color"
                    ]);
                    exit;
                }
            }

            // Insert into category_details
            $insert_color = "INSERT INTO category_details (category_id, detail_name, detail_value, created_at) 
                             VALUES ('$category_id','$color', '$image_name', NOW())";
            mysqli_query($conn, $insert_color);
        }
    }

    if (!empty($_POST['details']['Compatibility'])) {
        $compatibility = implode(", ", $_POST['details']['Compatibility']);
        $insert_compat = "INSERT INTO category_details (category_id, detail_name, detail_value, created_at) 
                          VALUES ('$category_id', 'Compatibility', '$compatibility', NOW())";
        mysqli_query($conn, $insert_compat);
    }

    header("Location: ./index.php?msg=Product added successfully");
    exit;
}
?>

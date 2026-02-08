<?php
include "../config/connection.php";
$id = $_GET['product_id'] ?? null;

if (!$id) {
    echo "No user selected.";
    return;
}

$id = $_GET['product_id'];
$sql = "SELECT p.id AS `id`,
               p.name AS `pname`,
               c.name AS `cname`,
               p.price AS `Price`,
               p.stock_quantity AS `stock`
        FROM products p
        JOIN categories c ON c.id = p.category_id
        WHERE p.id = '$id';";
        
$res = mysqli_query($conn, $sql);
include "./header.php" ?>

<!-- Update Product Form -->
<div id="add-product-form">
    <div class="form-container">
        <h3 style="margin-bottom: 20px; color: #2c3e50;">Edit Product</h3>
        <?php if ($res):
            while ($data = mysqli_fetch_assoc($res)):
                $pname = isset($data['pname']) ? $data['pname'] : '';
                $price = isset($data['Price']) ? $data['Price'] : 0;
                $stock = isset($data['stock']) ? $data['stock'] : 0;
                $cname = isset($data['cname']) ? $data['cname'] : '';
                ?>

                <form action="./update-product.php" method="POST" novalidate>
                    <input type="hidden" name="product_id" value="<?php echo $data['id']; ?>">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Customer Name</label>
                            <input type="text" class="form-input" id="pname" name="productname" placeholder="Enter Product Name"
                                value="<?php echo $pname; ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Categories</label>
                            <input type="text" class="form-input" id="categories" name="categories" placeholder=""
                                value="<?php echo $cname; ?>" readonly>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Price</label>
                            <input type="number" class="form-input" id="price" name="price" placeholder="Enter Price"
                                value="<?php echo $price; ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Stock</label>
                            <input type="number" class="form-input" id="stock" name="stock" placeholder="Enter Stock"
                                value="<?php echo $stock; ?>">
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <a href="./index.php" class="btn"
                            style="background: #95a5a6; color: white; margin-right: 10px;">Cancel</a>
                        <button type="submit" name="submit" class="btn btn-primary">Update Now</button>
                    </div>
                </form>
            <?php endwhile;
        else: ?>
            <hr>
        <?php endif; ?>
    </div>
</div>
</div>

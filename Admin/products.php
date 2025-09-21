<?php
include "../config/connection.php";
$sql = "SELECT p.id AS `id`,
               p.name AS `pname`,
               c.name AS `cname`,
               p.price AS `Price`,
               cd.detail_value AS `image`,
               p.stock_quantity AS `stock`
        FROM products p
        JOIN categories c ON c.id = p.category_id
        LEFT JOIN category_details cd 
               ON c.id = cd.category_id 
               AND cd.detail_name = 'Black'";
$res = mysqli_query($conn, $sql);
include "header.php" ?>

<div class="table-container">
    <div class="table-header">
        <div class="table-title">Product Management</div>
        <a href="./add-product.php" class="btn btn-primary">Add New Product</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1;
            while ($row = mysqli_fetch_assoc($res)) { ?>
                <tr>
                    <td>
                        <img src="../public/uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['pname']; ?>"
                            style="width:50px; height:50px; background:#f0f0f0; border-radius:8px;">
                    </td>
                    <td><?php echo $row['pname']; ?></td>
                    <td><?php echo $row['cname']; ?></td>
                    <td><?php echo $row['Price']; ?></td>
                    <td><?php echo $row['stock']; ?></td>
                    <td><span class="status status-active">Active</span></td>
                    <td>
                        <a href="./edit-product.php?product_id=<?php echo $row['id'] ?>" title=""
                            class="btn btn-success">Edit</a>
                        <a href="./delete-product.php?product_id=<?php echo $row['id'] ?>" title=""
                            class="btn btn-danger">Delete</a>
                    </td>
                </tr>
                <?php $i++;
            } ?>
        </tbody>
    </table>
</div>
<?php
if (isset($_GET['product_id'])) {
    include './edit-product.php';
}
?>
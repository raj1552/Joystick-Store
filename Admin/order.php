<?php
include "../config/connection.php";
$sql = "SELECT 
    o.id AS `id`,
    c.fullname AS `Customer`,
    p.name AS `Product`,
    o.total_amount AS `Total_Amount`,
    o.status AS `Status`,
    od.quantity AS `Quantity`,
    DATE(od.created_at) AS `OrderDate`
FROM orders o
JOIN customers c ON o.customer_id = c.id
JOIN order_details od ON o.id = od.order_id
JOIN products p ON od.product_id = p.id;";
$res = mysqli_query($conn, $sql);
include "header.php" ?>

<div class="table-container">
    <div class="table-header">
        <div class="table-title">Order Management</div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Products</th>
                <th>Quantity</th>
                <th>Total Amount</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1;
            while ($row = mysqli_fetch_assoc($res)) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['Customer']; ?></td>
                    <td><?php echo $row['Product']; ?></td>
                    <td><?php echo $row['Quantity']; ?></td>
                    <td><?php echo $row['Total_Amount']; ?></td>
                    <td><?php echo $row['OrderDate']; ?></td>
                    <td>
                        <?php if ($row['Status'] === 'Completed'): ?>
                            <span class="status status-active"><?php echo $row['Status']; ?></span>
                        <?php else: ?>
                            <span class="status status-inactive"><?php echo $row['Status']; ?></span>
                        <?php endif; ?>
                    <td>
                        <a href="./edit-order.php?order_id=<?php echo $row['id'] ?>" title=""
                            class="btn btn-success">Edit</a>
                        <a href="./delete-order.php?order_id=<?php echo $row['id'] ?>" title="" class="btn btn-danger">Delete</a>
                    </td>
                </tr>
                <?php $i++;
            } ?>
        </tbody>
    </table>
</div>
<?php
if (isset($_GET['order_id'])) {
    include './delete-order.php';
}
?>
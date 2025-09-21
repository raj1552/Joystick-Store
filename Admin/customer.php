<?php
include "../config/connection.php";
$sql = "SELECT * from customers;";
$res = mysqli_query($conn, $sql);
include "./header.php" ?>

<!-- Customer Page -->
<div class="table-container">
    <div class="table-header">
        <div class="table-title">Customer Management</div>
        <a href="./add-customer.php" class="btn btn-primary">Add New Customer</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1;
            while ($row = mysqli_fetch_assoc($res)) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['fullname']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['phone']; ?></td>
                    <td><?php echo $row['address']; ?></td>
                    <td><span class="status status-active">Active</span></td>
                    <td>
                        <a href="./edit.php?user_id=<?php echo $row['id'] ?>" title="" class="btn btn-success">Edit</a>
                        <a href="./delete.php?user_id=<?php echo $row['id'] ?>" title="" class="btn btn-danger">Delete</a>
                    </td>
                </tr>
                <?php $i++;
            } ?>
        </tbody>
    </table>
    <?php
    if (isset($_GET['user_id'])) {
        include './edit.php';
    }
    ?>
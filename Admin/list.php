<?php 
include "../config/connection.php";
$sql ="SELECT * FROM customers";
$res = mysqli_query($conn, $sql);

include "../includes/header.php"; ?>
<?php echo isset($_SESSION['msg'])? "<p class=' msg-box'>". $_SESSION['msg']. "</p>" : '';?>
        <h1 class="page-title">All User</h1>

        <a href="./logout.php" title="logout" class="btn btn-primary">Logout</a>
        <?php if($res):?>
            <table border="1" cellpadding="6" cellspacing="0">
                <thead>
                    <tr>
                        <th>S.N</th>
                        <th>FullName</th>
                        <th>Email/ Username</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i=1; while($row= mysqli_fetch_assoc($res)):
                        //echo "<pre>";
                        //print_r($row);
                        //echo"</pre>";
                    ?>
                    <tr>
                        <td><?php echo $i;?></td>
                        <td><?php echo$row['fullname']; ?></td>
                        <td><?php echo$row['email']; ?></td>
                        <td>
                            <a href="./edit.php?user_id=<?php echo $row['id']?>" title="" class="text-link">Edit</a>
                            <a href="./delete.php?user_id=<?php echo $row['id']?>" title="" class="text-link">Delete</a>
                        </td>
                    </tr>
                    
                    <?php $i++; endwhile;?>
                </tbody>

            </table>
        <?php else : ?>
            <p class="msg-box"> Oops! data not found. </p>
        <?php endif; ?>

        <a href="./index.php" class="text-link" title="Go back Home">Go Back Home</a>
        
     <?php include "../includes/footer.php"; ?>
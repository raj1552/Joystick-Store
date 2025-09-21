<?php include "../config/connection.php";
$id = $_GET['user_id'] ?? null;

if (!$id) {
    echo "No user selected.";
    return;
}

$id = $_GET['user_id'];
$sql = "SELECT id, fullname, email, phone, address FROM users WHERE id='$id';";
$res = mysqli_query($conn, $sql);
include "./header.php" ?>

<!-- Update Admin Form -->
<div id="add-product-form">
    <div class="form-container">
        <h3 style="margin-bottom: 20px; color: #2c3e50;">Edit Admin</h3>
        <?php if ($res):
            while ($data = mysqli_fetch_assoc($res)):
                $fname = isset($data['fullname']) ? $data['fullname'] : '';
                $email = isset($data['email']) ? $data['email'] : '';
                $phone = isset($data['phone']) ? $data['phone'] : '';
                $addr = isset($data['address']) ? $data['address'] : '';
                ?>

                <form action="./update-admin.php" method="POST" novalidate>
                    <input type="hidden" name="user_id" value="<?php echo $data['id']; ?>">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Customer Name</label>
                            <input type="text" class="form-input" id="fname" name="fullname" placeholder="Enter Customer Name"
                                value="<?php echo $fname; ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-input" id="email" name="email" placeholder="Enter Email"
                                value="<?php echo $email; ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Phone</label>
                            <input type="number" class="form-input" id="phone" name="phone" placeholder="Enter Phone"
                                value="<?php echo $phone; ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-input" id="addr" name="address" placeholder="Enter Address"
                                value="<?php echo $addr; ?>">
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
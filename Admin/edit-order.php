<?php
include "../config/connection.php";

$id = $_GET['order_id'] ?? null;
if (!$id) {
    echo "No order selected.";
    exit;
}

// sanitize id
$id = mysqli_real_escape_string($conn, $id);

// corrected joins and LIMIT 1 to return a single row for the form
$sql = "SELECT o.id AS `id`,
        c.fullname AS `Customer`,
        p.name AS `Product`,
        p.price AS `Price`,
        o.total_amount AS `Total_Amount`,
        o.status AS `Status`,
        od.quantity AS `Quantity`,
        DATE(od.created_at) AS `OrderDate`
    FROM orders o
    JOIN customers c ON o.customer_id = c.id
    JOIN order_details od ON o.id = od.order_id
    JOIN products p ON od.product_id = p.id
    WHERE o.id = '$id'
    LIMIT 1;";

$res = mysqli_query($conn, $sql);
include "./header.php";
?>

<!-- Update Order Form -->
<div id="add-product-form">
  <div class="form-container">
    <h3 style="margin-bottom: 20px; color: #2c3e50;">Edit Order</h3>

    <?php if ($res && mysqli_num_rows($res) > 0):
        $data = mysqli_fetch_assoc($res);

        // Pull values directly from DB (no static default)
        $cname = htmlspecialchars($data['Customer']);
        $product = htmlspecialchars($data['Product']);
        $price = isset($data['Price']) ? (float)$data['Price'] : 0;
        $quantity = isset($data['Quantity']) ? (int)$data['Quantity'] : 0;
        $status = isset($data['Status']) ? $data['Status'] : ''; // keep DB value (if empty, will not preselect)
        $totalAmount = isset($data['Total_Amount']) ? (float)$data['Total_Amount'] : ($price * $quantity);
    ?>
      <form action="./update-order.php" method="POST" novalidate>
        <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($data['id']); ?>">
        <input type="hidden" id="price" value="<?php echo $price; ?>">

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Customer Name</label>
            <input type="text" class="form-input" id="fname" name="fullname" value="<?php echo $cname; ?>">
          </div>

          <div class="form-group">
            <label class="form-label">Product Name</label>
            <input type="text" class="form-input" id="product" name="product" value="<?php echo $product; ?>" readonly>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Quantity</label>
            <input type="number" class="form-input" id="quantity" name="quantity" value="<?php echo $quantity; ?>">
          </div>

          <div class="form-group">
            <label class="form-label">Total Amount</label>
            <input type="number" class="form-input" id="tamount" name="tamount" value="<?php echo $totalAmount; ?>" readonly>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Status</label>
            <select class="form-input" id="status" name="status">
              <option value="Pending" <?php echo (strtolower($status) === 'pending') ? 'selected' : ''; ?>>Pending</option>
              <option value="Completed" <?php echo (strtolower($status) === 'completed') ? 'selected' : ''; ?>>Completed</option>
            </select>
          </div>
        </div>

        <div style="text-align: right;">
          <a href="./index.php" class="btn" style="background: #95a5a6; color: white; margin-right: 10px;">Cancel</a>
          <button type="submit" name="submit" class="btn btn-primary">Update Now</button>
        </div>
      </form>

    <?php else: ?>
      <hr>
      <p>No data found for that order.</p>
    <?php endif; ?>
  </div>
</div>

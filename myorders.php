<?php
session_start();
include "./config/connection.php";

if (!isset($_SESSION['user_id'])) {
  header("Location: ./customer-login.php");
  exit();
}

$user_id = $_SESSION['user_id'] ?? 0;

// Get all orders with order details for this customer
$sql = "SELECT 
        o.id AS order_id,
        o.customer_id,
        o.order_date,
        o.total_amount AS order_total,
        o.status AS order_status,
        o.created_at AS order_created,
        o.updated_at AS order_updated,
        od.id AS detail_id,
        od.product_id,
        od.color,
        od.address,
        od.city,
        od.state,
        od.zip,
        od.quantity,
        od.unit_price,
        od.subtotal,
        od.created_at AS detail_created,
        p.name AS product_name,
        cd.detail_value AS image
        FROM orders o
        JOIN order_details od ON o.id = od.order_id
        JOIN products p ON od.product_id = p.id
        LEFT JOIN category_details cd 
        ON p.category_id = cd.category_id 
        AND cd.detail_name = od.color
        WHERE o.customer_id = $user_id
        ORDER BY o.order_date DESC, o.id DESC, od.id ASC";

$res = mysqli_query($conn, $sql);
$hasOrders = mysqli_num_rows($res) > 0;

// Group orders by order_id for better display
$orders = [];
if ($hasOrders) {
    while ($row = mysqli_fetch_assoc($res)) {
        $order_id = $row['order_id'];
        $orders[$order_id]['info'] = [
            'order_id' => $row['order_id'],
            'order_date' => $row['order_date'],
            'order_total' => $row['order_total'],
            'order_status' => $row['order_status'],
            'order_created' => $row['order_created'],
            'address' => $row['address'],
            'city' => $row['city'],
            'state' => $row['state'],
            'zip' => $row['zip']
        ];
        $orders[$order_id]['items'][] = $row;
    }
}

include "./header.php";
?>

<section class="cart-container">
  <div class="container">
    <div class="cart-header">
      <h2>My Orders</h2>
    </div>
  </div>
</section>

<section class="carts-products">
  <div class="container">
    <div class="cart-wrapper">
      <?php if ($hasOrders): ?>
        <?php foreach ($orders as $order_id => $order): ?>
          <?php $orderInfo = $order['info']; ?>
          <?php $orderItems = $order['items']; ?>

          <!-- Order Items -->
          <?php foreach ($orderItems as $row): ?>
            <div class="cart-details order-item" data-detail-id="<?= $row['detail_id'] ?>" data-order-id="<?= $row['order_id'] ?>">
              <div class="product-image">
                <?php if (!empty($row['image'])): ?>
                  <img src="./public/uploads/<?= $row['image'] ?>" alt="<?= $row['product_name'] ?>" class="product-img" />
                <?php else: ?>
                  <div class="no-image-placeholder">No Image</div>
                <?php endif; ?>
              </div>
              <div class="cart-content">
                <div class="cart-details-top">
                  <h3><?= $row['product_name'] ?></h3>
                  <div class="order-actions">
                    <span class="status-badge status-<?= strtolower($orderInfo['order_status']) ?>">
                      <?= ucfirst($orderInfo['order_status']) ?>
                    </span>
                  </div>
                </div>
                <p>Color: <span class="product-color"><?= ucfirst($row['color']) ?></span></p>
                <div class="cart-details-bottom">
                  <div class="quantity-info">
                    <h6 style="margin-bottom: 0;">Quantity: <?= $row['quantity'] ?></h6>
                    <p style="margin: 5px 0 0 0; font-size: 12px; color: #666;">
                      Ordered: <?= date('M d, Y', strtotime($row['detail_created'])) ?>
                    </p>
                  </div>
                  <div class="price-info" style="text-align: right;">
                    <p class="price">NRs. <?= number_format($row['subtotal']) ?></p>
                    <p style="margin: 5px 0 0 0; font-size: 12px; color: #666;">
                      Unit Price: NRs. <?= number_format($row['unit_price']) ?>
                    </p>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
          
        <?php endforeach; ?>
      <?php else: ?>
        <div class="no-orders">
          <h3>No Orders Yet 📦</h3>
          <p>You haven't placed any orders yet. Start shopping to see your orders here!</p>
          <a href="./shop.php" class="btn btn-primary">Start Shopping</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php include "./footer.php"; ?>

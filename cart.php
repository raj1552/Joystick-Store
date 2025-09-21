<?php
session_start();
include "./config/connection.php";

if (!isset($_SESSION['user_id'])) {
  header("Location: ./customer-login.php");
  exit();
}

$user_id = $_SESSION['user_id'] ?? 0;

$sql = "SELECT 
        c.id AS cart_id,
        c.customer_id,
        c.product_id,
        p.name AS product_name,
        p.price,
        c.color,
        c.quantity,
        (p.price * c.quantity) AS total_amount,
        cd.detail_value AS image
        FROM cart c
        JOIN products p ON c.product_id = p.id
        JOIN category_details cd 
        ON p.category_id = cd.category_id 
        AND cd.detail_name = c.color
        WHERE c.customer_id = $user_id
        AND c.status != 'completed'"; // Fixed: should be 'completed' not 'Completed'

$res = mysqli_query($conn, $sql);
$subtotal = 0;
$hasItems = mysqli_num_rows($res) > 0; // Check if cart has items

include "./header.php";
?>

<section class="cart-container">
  <div class="container">
    <div class="cart-header">
      <h2>Your Shopping Cart</h2>
    </div>
  </div>
</section>

<section class="carts-products">
  <div class="container">
    <div class="cart-wrapper">
      <?php if ($hasItems): ?>
        <?php while ($row = mysqli_fetch_assoc($res)):
          $subtotal += $row['total_amount'];
          ?>
          <div class="cart-details" data-cart-id="<?= $row['cart_id'] ?>" data-price="<?= $row['price'] ?>">
            <div class="product-image">
              <img src="./public/uploads/<?= $row['image'] ?>" alt="<?= $row['product_name'] ?>" class="product-img" />
            </div>
            <div class="cart-content">
              <div class="cart-details-top">
                <h3><?= $row['product_name'] ?></h3>
                <a href="delete-cart-item.php?id=<?= $row['cart_id'] ?>">
                  <svg width="30" height="30" viewBox="0 0 24 24" fill="none">
                    <path d="M6 19.0045C6 20.1022 6.89991 21 8.00002 21H16C17.1001 21 18 20.1022 18 19.0045V7.5H6V19.0045ZM19.5 4.5H15.75L14.4945 3H9.50559L8.25 4.5H4.5V6H19.5V4.5Z" fill="#FF0000"/>
                  </svg>
                </a>
              </div>
              <p>Color: <span class="product-color"><?= ucfirst($row['color']) ?></span></p>
              <div class="cart-details-bottom">
                <div class="quantity-selector">
                  <h6 style="margin-bottom: 0;">Quantity :</h6>
                  <span class="quantity-display"><?= $row['quantity'] ?></span>
                </div>
                <p class="price">NRs. <?= number_format($row['total_amount']) ?></p>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="no-orders">
          <h3>Your cart is empty 🛒</h3>
          <p>Looks like you haven't added anything yet.</p>
          <a href="./shop.php" class="btn btn-primary">Start Shopping</a>
        </div>
      <?php endif; ?>
    </div>

    <div class="order-wrapper">
      <div class="order-summary">
        <h4>Order Summary</h4>
        
        <?php if ($hasItems): ?>
          <!-- Show order summary only when there are items -->
          <div class="subtotal">
            <p>Subtotal</p>
            <p id="cartSubtotal">NRs. <?= number_format($subtotal) ?></p>
          </div>
          <hr />
          <div class="total">
            <h3>Total</h3>
            <p class="highlight" id="cartTotal">NRs. <?= number_format($subtotal) ?></p>
          </div>
          <button class="btn btn-primary" id="checkoutBtn">Process to Checkout</button>
        <?php else: ?>
          <!-- Show empty cart message in order summary -->
          <div class="empty-cart-summary">
            <p style="text-align: center; color: #666; margin: 20px 0;">
              Add items to your cart to see order summary
            </p>
            <button class="btn btn-secondary" disabled style="width: 100%; cursor: not-allowed;">
              Cart is Empty
            </button>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php
// Only include checkout and payment if there are items
if ($hasItems) {
  $total = $subtotal;
  include "./checkout.php";
  include "./payment.php";
}
?>

<?php include "./footer.php"; ?>
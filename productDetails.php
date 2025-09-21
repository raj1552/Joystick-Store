<?php
session_start();
include "./config/connection.php";

if (!isset($_SESSION['user_id'])) {
  $product_id = $_GET['product_id'] ?? 0;
  header("Location: ./customer-login.php?redirect=productDetails.php?product_id=$product_id");
  exit();
}

$user_id = $_SESSION['user_id'];
$product_id = isset($_GET['product_id']) ? (int) $_GET['product_id'] : 0;

// Fetch product details
$sql = "SELECT id, name AS pname, price, stock_quantity AS stock 
        FROM products 
        WHERE id = $product_id";
$res = mysqli_query($conn, $sql);
$product = mysqli_fetch_assoc($res);

// Fetch available colors and images
$sql_images = "SELECT detail_name, detail_value 
               FROM category_details 
               WHERE category_id = (SELECT category_id FROM products WHERE id = $product_id)
               AND detail_name IN ('black', 'white', 'blue', 'orange')";
$res_images = mysqli_query($conn, $sql_images);
$images = mysqli_fetch_all($res_images, MYSQLI_ASSOC);

// Handle Add to Cart → only cart table
if (isset($_POST['add_to_cart'])) {
  $selected_color = $_POST['color'] ?? '';
  $quantity = intval($_POST['quantity'] ?? 1);

  if ($selected_color == '') {
    $error = "Please select a color.";
  } else {
    // Insert into cart only
    $stmt_cart = $conn->prepare("INSERT INTO cart (customer_id, product_id, quantity, color, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt_cart->bind_param("iiis", $user_id, $product_id, $quantity, $selected_color);
    $stmt_cart->execute();

    $success = "Product added to cart successfully!";
  }
}
include "./header.php";
?>

<!-- Product Details HTML -->
<section class="product-details-wrapper">
  <div class="container">
    <div class="product-container">
      <div class="product-images">
        <?php foreach ($images as $img) { ?>
          <div class="image-wrapper">
            <img src="./public/uploads/<?php echo $img['detail_value']; ?>" alt="<?php echo $product['pname']; ?>" />
          </div>
        <?php } ?>
      </div>
      <div class="product-details">
        <h2><?php echo $product['pname']; ?></h2>
        <h4 class="highlight">NRs. <?php echo number_format($product['price']); ?></h4>

        <form method="POST">
          <div class="product-color">
            <h5>Select Color</h5>
            <ul class="color-option">
              <?php foreach ($images as $img) { ?>
                <li>
                  <label>
                    <input type="radio" name="color" value="<?php echo $img['detail_name']; ?>" required>
                    <span class="option" style="background-color: <?php echo strtolower($img['detail_name']); ?>"></span>
                  </label>
                </li>
              <?php } ?>
            </ul>
          </div>

          <div class="product-quantity">
            <label>Quantity:</label>
            <div class="quantity-wrapper">
              <button type="button" class="qty-btn minus">-</button>
              <input type="text" name="quantity" value="1" readonly data-max="<?php echo $product['stock']; ?>">
              <button type="button" class="qty-btn plus">+</button>
            </div>
          </div>

          <?php if (isset($error))
            echo "<p style='color:red;'>$error</p>"; ?>
          <?php if (isset($success))
            echo "<p style='color:green;'>$success</p>"; ?>
          <button type="submit" name="add_to_cart" class="btn btn-primary">Add to Cart</button>
        </form>
      </div>
    </div>
  </div>
</section>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Select all quantity wrappers (in case there are multiple)
    const quantityWrappers = document.querySelectorAll('.quantity-wrapper');

    quantityWrappers.forEach(wrapper => {
      const minusBtn = wrapper.querySelector('.qty-btn.minus');
      const plusBtn = wrapper.querySelector('.qty-btn.plus');
      const qtyInput = wrapper.querySelector('input[name="quantity"]');
      const maxStock = parseInt(qtyInput.getAttribute('data-max'));

      minusBtn.addEventListener('click', () => {
        let current = parseInt(qtyInput.value);
        if (current > 1) qtyInput.value = current - 1;
      });

      plusBtn.addEventListener('click', () => {
        let current = parseInt(qtyInput.value);
        if (current < maxStock) qtyInput.value = current + 1;
      });
    });
  });
</script>

<?php include "./footer.php"; ?>
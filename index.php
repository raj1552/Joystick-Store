<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include "./config/connection.php";
include "./header.php" ?>

<!-- Hero Section -->
<section class="hero-section">
  <video class="hero-content" autoplay muted loop playsinline data-autoplay>
    <source src="./public/assets/877ea6b1463c4b818873e775021f0418.mp4" type="video/mp4" />
  </video>
</section>
<section class="beyond-container">
  <h2>SOAR BEYOND THE LIMITS</h2>
  <h3>NOVA II</h3>
  <a href="./shop.php">Learn More</a>
</section>

<!-- Best Seller -->
<section class="best-seller">
  <div class="container">
    <div class="seller-header">
      <h2>Best Sellers</h2>
      <a href="./shop.php" class="btn btn-primary">View More Products</a>
    </div>
    <div class="cards-container">
      <?php
      $sql = "SELECT p.id AS `id`,
                     p.name AS `pname`,
                     p.price AS `Price`,
                     cd.detail_value AS `image`
              FROM products p
              JOIN categories c ON c.id = p.category_id
              LEFT JOIN category_details cd 
                     ON c.id = cd.category_id 
                     AND cd.detail_name = 'Black' LIMIT 3;";

      $res = mysqli_query($conn, $sql);

      while ($row = mysqli_fetch_assoc($res)) {
        if (!empty($row['image'])) { ?>
          <div class="card">
            <div class="card-contents">
              <img src="./public/uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['pname']; ?>" />
              <h3><?php echo $row['pname']; ?></h3>
              <h4>Starts at <span class="highlight">NRs. <?php echo number_format($row['Price']); ?></span></h4>
              <a href="./productDetails.php?product_id=<?php echo $row['id']; ?>" class="btn btn-primary">Shop Now</a>
            </div>
          </div>
        <?php }
      } ?>
    </div>
  </div>
</section>

<!-- Exclusive Deals -->
<section class="deals-container">
  <div class="container">
    <div class="deals-contents">
      <div class="details">
        <h2>Exclusive Deals</h2>
        <h3>Limited Time Offer</h3>
        <p>
          Discover premium gaming gear designed to level up your
          experience. From high-performance controllers to next-gen
          accessories, grab the best deals before they’re gone. Elevate
          your play with style, precision, and unbeatable offers.
        </p>
        <a href="./shop.php" class="btn btn-primary">Get Yours Today</a>
      </div>
    </div>
  </div>
</section>
</div>
<?php include "./footer.php" ?>
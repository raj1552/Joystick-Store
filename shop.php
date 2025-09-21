<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "./config/connection.php";
include "./header.php"; 
?>
<!-- Banner -->
<section class="banner">
  <h2>Console</h2>
</section>

<!-- Products -->
<section class="best-seller">
  <div class="container">
    <div class="cards-container">
      <?php
      // Fetch products with 'Black' detail
      $sql = "SELECT p.id AS `id`,
                     p.name AS `pname`,
                     p.price AS `Price`,
                     cd.detail_value AS `image`
              FROM products p
              JOIN categories c ON c.id = p.category_id
              LEFT JOIN category_details cd 
                     ON c.id = cd.category_id 
                     AND cd.detail_name = 'Black'";

      $res = mysqli_query($conn, $sql);

      while($row = mysqli_fetch_assoc($res)) { 
        if(!empty($row['image'])) { ?>
          <div class="card">
            <div class="card-contents">
              <img src="./public/uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['pname']; ?>" />
              <h3><?php echo $row['pname']; ?></h3>
              <h4>Starts at <span class="highlight">NRs. <?php echo number_format($row['Price']); ?></span></h4>
              <a href="./productDetails.php?product_id=<?php echo $row['id']; ?>" class="btn btn-primary">Shop Now</a>
            </div>
          </div>
      <?php } } ?>
    </div>
  </div>
</section>

<?php include "./footer.php"; ?>

<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['login_status']) || $_SESSION['login_status'] !== true) {
  header("Location: ../admin-login.php");
  exit();
}

include "./header.php";
?>

<div class="dashboard-container">
  <?php include "./sidebar.php"; ?>

  <div class="main-content">
    <div class="header">
      <h1 id="page-title">Dashboard</h1>
      <div class="user-info">
        <span><?= $_SESSION['user_email'] ?? 'Admin User'; ?></span>
        <div class="user-avatar">A</div>
      </div>
    </div>

    <!-- Pages -->
    <div id="dashboard" class="page active">
      <?php include "./dashboard.php"; ?>
    </div>

    <div id="admin" class="page">
      <?php include "./admin.php"; ?>
    </div>

    <div id="products" class="page">
      <?php include "./products.php"; ?>
    </div>

    <div id="orders" class="page">
      <?php include "./order.php"; ?>
    </div>

    <div id="reports" class="page">
      <?php include "./reports.php"; ?>
    </div>

    <div id="customer" class="page">
      <?php include "./customer.php"; ?>
    </div>
    
  </div>
</div>

<script src="./assets/index.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>

</html>
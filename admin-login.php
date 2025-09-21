<?php
session_start();
if (isset($_SESSION['login_status']) && $_SESSION['login_status'] === true) {
    header("Location: ./admin/index.php");
    exit();
}

include "./config/connection.php";

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pwd   = $_POST['password'];

    $sql = "SELECT id, email, password FROM users WHERE email='$email'";
    $res = mysqli_query($conn, $sql);

    if ($res && mysqli_num_rows($res) > 0) {
        $data = mysqli_fetch_assoc($res);
        if (password_verify($pwd, $data['password'])) {
            $_SESSION['login_status'] = true;
            $_SESSION['user_email'] = $data['email'];
            $_SESSION['success_msg'] = 'User Login Successful';
            header("Location: ./admin/index.php");
            exit();
        } else {
            $_SESSION['error_msg'] = 'Password does not match';
        }
    } else {
        $_SESSION['error_msg'] = 'No user found with this email!';
    }
}

include "./header.php";
?>

<section class="form-section">
  <div class="form-content">
    <h2 class="form-title">Login to Admin</h2>
    <form action="" method="POST" class="form-wrapper" id="login-form">
      <div class="form-container">
        <label for="email">Your Email</label>
        <input type="email" id="email" name="email" required />
      </div>
      <div class="form-container">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required />
      </div>

      <!-- Display Messages -->
      <?php if (isset($_SESSION['error_msg'])): ?>
        <div class="alert alert-error">
          ❌ <?= $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?>
        </div>
      <?php endif; ?>

      <?php if (isset($_SESSION['success_msg'])): ?>
        <div class="alert alert-success">
          ✅ <?= $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
        </div>
      <?php endif; ?>

      <button type="submit" name="submit" class="btn btn-primary">Login</button>
    </form>
  </div>
</section>

<?php include "./footer.php"; ?>

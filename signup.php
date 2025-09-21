<?php
session_start();
include "./config/connection.php"; // adjust path

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $cpassword = trim($_POST['cpassword']);

    if (empty($fname) || empty($lname) || empty($email) || empty($password) || empty($cpassword)) {
        $message = "<p class='error'>All required fields must be filled.</p>";
    } elseif ($password !== $cpassword) {
        $message = "<p class='error'>Passwords do not match.</p>";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM customers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            $message = "<p class='error'>Email is already registered.</p>";
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Insert new customer
            $insert = $conn->prepare("INSERT INTO customers (fullname, email, phone, password) VALUES (?, ?, ?, ?)");
            $fullname = $fname . " " . $lname;
            $insert->bind_param("ssss", $fullname, $email, $phone, $hashedPassword);

            if ($insert->execute()) {
                $message = "<p class='success'>Account created successfully! Redirecting to login...</p>";
                header("refresh:2; url=customer-login.php"); 
            } else {
                $message = "<p class='error'>Something went wrong. Please try again.</p>";
            }
        }
    }
}
?>

<?php include "./header.php" ?>

<!-- Customer Signup Form -->
<section class="form-section">
  <div class="form-content signup">
    <h2 class="form-title">Signup Your Account</h2>
    <p>Create Your New Account for Personalized Shopping Experience</p>

    <?php if (!empty($message)) echo $message; ?>

    <form action="" method="POST" class="form-wrapper" id="signup-form">
      <div class="form-row">
        <div class="form-container">
          <label for="fname">First Name</label>
          <input type="text" class="fname" id="fname" name="fname" required />
        </div>
        <div class="form-container">
          <label for="lname">Last Name</label>
          <input type="text" class="lname" id="lname" name="lname" required />
        </div>
      </div>
      <div class="form-row">
        <div class="form-container">
          <label for="email">Email</label>
          <input type="email" class="email" id="email" name="email" required />
        </div>
        <div class="form-container">
          <label for="phone">Phone</label>
          <input type="tel" class="phone" id="phone" name="phone" />
        </div>
      </div>
      <div class="form-row">
        <div class="form-container">
          <label for="password">Password</label>
          <input type="password" class="password" id="password" name="password" required />
        </div>
        <div class="form-container">
          <label for="cpassword">Confirm Password</label>
          <input type="password" class="cpassword" id="cpassword" name="cpassword" required />
        </div>
      </div>
      <div class="terms-container">
        <input type="checkbox" id="agreeTerms" name="agreeTerms" required />
        <label for="agreeTerms" class="terms-text">
          I agree to <a href="#" id="termsLink">Terms and Condition</a>
        </label>
      </div>
      <button type="submit" class="create-btn" id="createBtn">Create Account</button>
    </form>

    <div class="login-link">
      Already have an Account?
      <a href="./customer-login.php" id="loginLink">Login</a>
    </div>
  </div>
</section>

<?php include "./footer.php" ?>

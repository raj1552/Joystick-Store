<?php
session_start();
include "./config/connection.php"; // adjust path if needed

$message = "";

// Get redirect URL
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : './index.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $message = "<p class='error'>All fields are required.</p>";
    } else {
        $stmt = $conn->prepare("SELECT id, fullname, email, password FROM customers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 1) {
            $row = $res->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                // Store session with consistent variable names
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['fullname'];

                header("Location: $redirect");
                exit();
            } else {
                $message = "<p class='error'>Invalid password.</p>";
            }
        } else {
            $message = "<p class='error'>Email not found.</p>";
        }
    }
}
?>

<?php include "./header.php"; ?>

<section class="form-section">
  <div class="form-content">
    <h2 class="form-title">Login</h2>
    <?php if (!empty($message)) echo $message; ?>
    <form action="" method="POST" class="form-wrapper">
      <div class="form-container">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required />
      </div>
      <div class="form-container">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required />
      </div>
      <button type="submit" class="btn btn-primary">Login</button>
    </form>
  </div>
</section>

<?php include "./footer.php"; ?>

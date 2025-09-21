<?php session_start();

if(isset($_POST['submit'])) {
    $email = $_POST['email'];
    $pwd = $_POST['password'];

    include "../config/connection.php";
    $sql = "SELECT id,email, password FROM users WHERE email='$email'";
    $res = mysqli_query($conn, $sql);

    if($res) {
        while($data = mysqli_fetch_assoc($res)) {
            if($data['password'] == $pwd){
                $_SESSION['login_status'] = true;
            $_SESSION['msg'] = 'User Login Sucessfull';
            header("location: /myphp/admin/index.php");
            }
        }  
    } 
      else {
            $_SESSION['msg'] = 'Password not Matched';
        } 
}
else{
        $_SESSION['msg'] = "User Login Failed";
    }
include "./header.php"; ?>
    <div class="login-container">
        <div class="logo">
            <h2>Welcome Back</h2>
            <p>Please sign in to your account</p>
        </div>  
        <!-- Admin Login Form -->
        <form action="#"  method="POST" class="form" id="admin-form" novalidate>
            <div class="form-group">
                <label for="email">Admin Username</label>
                <input 
                    type="email" 
                    id="email" 
                    class="email" 
                    name="email"
                    placeholder="Enter admin username"
                    required
                >
            </div>

            <div class="form-group">
                <label for="pwd">Admin Password</label>
                <input 
                    type="password" 
                    id="pwd" 
                    class="password" 
                    name="password"
                    placeholder="Enter admin password"
                    required
                >
            </div>

            <div class="forgot-password">
                <a href="#" onclick="showMessage('Please contact IT support for password reset', 'success')">Forgot Password?</a>
            </div>

           <button type="submit" name="submit" class="btn btn-admin">
                Sign In as Admin
            </button>
        </form>
    </div>
</body>
</html>
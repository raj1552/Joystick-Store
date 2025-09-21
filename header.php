<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "./config/connection.php";

$cartCount = 0;
$isLoggedIn = false;
$userData = null;

if (isset($_SESSION['user_id'])) {
    $isLoggedIn = true;
    $userId = $_SESSION['user_id'];

    // Get cart count
    $sql = "SELECT COUNT(*) AS totalItems
            FROM cart c
            WHERE customer_id = 12
            AND status = 'pending';";

    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $cartCount = $row['totalItems'] ?? 0;
    }

    // Get user data for profile
    $userSql = "SELECT * FROM customers WHERE id = '$userId'";
    $userResult = mysqli_query($conn, $userSql);

    if ($userResult && mysqli_num_rows($userResult) > 0) {
        $user = mysqli_fetch_assoc($userResult);
        $userData = [
            'id' => $user['id'],
            'name' => $user['fullname'] ?? $user['username'] ?? 'User',
            'email' => $user['email'] ?? '',
            'phone' => $user['phone'] ?? '',
            'avatar' => strtoupper(substr(($user['fullname'] ?? $user['username'] ?? 'U'), 0, 1))
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" type="" href="./public/css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css"
        integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ=="
        crossorigin="anonymous" referreferxpolicy="no-referrer" />
    <title>LevelUp</title>
</head>

<body>
    <div class="wrapper"></div>
    <!-- header -->
    <div class="container">
        <header class="header">
            <!-- Logo Section -->
            <div class="logo-container">
                <img src="./public/assets/logo.png" alt="Company Logo" />
            </div>

            <!-- Navigation Section -->
            <nav class="nav">
                <ul class="nav-list">
                    <li class="nav-item active">
                        <a href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a href="./shop.php">Shop</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" onclick="return false;">
                            More
                            <svg class="dropdown-arrow" viewBox="0 0 12 12" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 8L2 4H10L6 8Z" fill="currentColor" />
                            </svg>
                        </a>
                        <div class="dropdown-menu">
                            <?php if (!$isLoggedIn): ?>
                                <a href="./admin-login.php">Admin Login</a>
                                <a href="./customer-login.php">Customer Login</a>
                                <a href="./signup.php">Sign Up</a>
                            <?php else: ?>
                                <a href="./admin-login.php">Admin Login</a>
                            <?php endif; ?>
                        </div>
                    </li>
                </ul>
            </nav>

            <!-- Right Section -->
            <div class="right-section">
                <!-- Search Bar -->
                <div class="search-container">
                    <div class="search-box">
                        <input type="text" class="search-input" placeholder="Search products..." />
                        <button class="search-btn" type="submit">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M21 21L16.514 16.506L21 21ZM19 10.5C19 15.194 15.194 19 10.5 19C5.806 19 2 15.194 2 10.5C2 5.806 5.806 2 10.5 2C15.194 2 19 5.806 19 10.5Z"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- User Profile (Show only when logged in) -->
                <?php if ($isLoggedIn && $userData): ?>
                    <div class="user-profile logged-in" id="userProfile">
                        <div class="profile-avatar" id="profileAvatar"><?php echo $userData['avatar']; ?></div>

                        <div class="profile-dropdown">
                            <div class="profile-header">
                                <div class="profile-avatar" id="profileAvatarLarge"><?php echo $userData['avatar']; ?></div>
                                <div class="profile-name" id="profileNameLarge">
                                    <?php echo htmlspecialchars($userData['name']); ?>
                                </div>
                                <div class="profile-email" id="profileEmail">
                                    <?php echo htmlspecialchars($userData['email']); ?>
                                </div>
                            </div>

                            <div class="profile-menu">
                                <a href="./customer-dashboard.php">
                                    <i class="fas fa-tachometer-alt"></i>
                                    <span>Dashboard</span>
                                </a>
                                <a href="./myorders.php">
                                    <i class="fas fa-shopping-bag"></i>
                                    <span>My Orders</span>
                                </a>
                                <a href="./logout.php" class="logout">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>Logout</span>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Cart -->
                <div class="cart-header">
                    <a href="./cart.php" class="user-cart">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_48_480)">
                                <path
                                    d="M21.5999 20.746L20.2257 5.27117C20.1962 4.92761 19.9067 4.66749 19.568 4.66749H16.741C16.7018 2.08589 14.5913 3.8147e-06 11.9999 3.8147e-06C9.40852 3.8147e-06 7.29809 2.08589 7.25882 4.66749H4.43183C4.08827 4.66749 3.80361 4.92761 3.77416 5.27117L2.39993 20.746C2.39993 20.7656 2.39502 20.7853 2.39502 20.8049C2.39502 22.5669 4.00974 24 5.99747 24H18.0024C19.9901 24 21.6048 22.5669 21.6048 20.8049C21.6048 20.7853 21.6048 20.7656 21.5999 20.746ZM11.9999 1.32516C13.8601 1.32516 15.3766 2.81718 15.4159 4.66749H8.58398C8.62324 2.81718 10.1398 1.32516 11.9999 1.32516ZM18.0024 22.6749H5.99747C4.75085 22.6749 3.7398 21.8503 3.72017 20.8344L5.03551 5.99755H7.25392V8.00982C7.25392 8.37792 7.54839 8.6724 7.91649 8.6724C8.28459 8.6724 8.57907 8.37792 8.57907 8.00982V5.99755H15.4159V8.00982C15.4159 8.37792 15.7104 8.6724 16.0785 8.6724C16.4466 8.6724 16.741 8.37792 16.741 8.00982V5.99755H18.9594L20.2797 20.8344C20.2601 21.8503 19.2441 22.6749 18.0024 22.6749Z"
                                    fill="black" />
                            </g>
                            <defs>
                                <clipPath id="clip0_48_480">
                                    <rect width="24" height="24" fill="white" />
                                </clipPath>
                            </defs>
                        </svg>
                        <span class="cart-badge"><?php echo $cartCount; ?></span>
                    </a>
                </div>
            </div>
        </header>
    </div>
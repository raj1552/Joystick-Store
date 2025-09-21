<?php
session_start();
include "./config/connection.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ./customer-login.php");
    exit();
}

$user_id = $_SESSION['user_id'] ?? 0;

// Get user information
$userSql = "SELECT * FROM customers WHERE id = '$user_id'";
$userResult = mysqli_query($conn, $userSql);
$user = mysqli_fetch_assoc($userResult);

// Get order statistics
$statsSql = "SELECT 
    COUNT(*) as total_orders,
    SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered_orders,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
    SUM(CASE WHEN status = 'shipped' THEN 1 ELSE 0 END) as shipped_orders,
    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders,
    COALESCE(SUM(total_amount), 0) as total_spent,
    MAX(order_date) as last_order_date
    FROM orders 
    WHERE customer_id = $user_id";

$statsResult = mysqli_query($conn, $statsSql);
$stats = mysqli_fetch_assoc($statsResult);

// Get recent orders (last 5)
$recentOrdersSql = "SELECT 
    o.id AS order_id,
    o.order_date,
    o.total_amount,
    o.status,
    COUNT(od.id) as item_count
    FROM orders o
    LEFT JOIN order_details od ON o.id = od.order_id
    WHERE o.customer_id = $user_id
    GROUP BY o.id
    ORDER BY o.order_date DESC 
    LIMIT 5";

$recentOrdersResult = mysqli_query($conn, $recentOrdersSql);

// Get cart count
$cartSql = "SELECT COUNT(*) as cart_items FROM cart WHERE customer_id = $user_id AND status = 'pending'";
$cartResult = mysqli_query($conn, $cartSql);
$cartData = mysqli_fetch_assoc($cartResult);
$cartCount = $cartData['cart_items'] ?? 0;

include "./header.php";
?>

<style>
.dashboard-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.dashboard-header {
    background: linear-gradient(135deg, #667eea 100%);
    border-radius: 16px;
    color: white;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.user-welcome {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 20px;
}

.user-avatar-large {
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    font-weight: bold;
    backdrop-filter: blur(10px);
    border: 3px solid rgba(255, 255, 255, 0.3);
}

.user-info h1 {
    font-size: 2.2em;
    margin-bottom: 5px;
    font-weight: 600;
}

.user-info p {
    opacity: 0.9;
    font-size: 1.1em;
}

.quick-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 20px;
}

.stat-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: transform 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
}

.stat-card h3 {
    font-size: 2em;
    margin-bottom: 5px;
    font-weight: 700;
}

.stat-card p {
    opacity: 0.9;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-bottom: 30px;
}

.dashboard-card {
    background: white;
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    border: 1px solid #e2e8f0;
}

.dashboard-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f1f5f9;
}

.card-title {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 1.3em;
    font-weight: 600;
    color: #1e293b;
}

.card-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
}

.view-all-btn {
    background: none;
    border: none;
    color: #667eea;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    padding: 8px 16px;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.view-all-btn:hover {
    background: #667eea;
    color: white;
}

.order-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    margin-bottom: 10px;
    transition: all 0.2s ease;
}

.order-item:hover {
    border-color: #667eea;
    background: #f8fafc;
}

.order-details {
    flex: 1;
}

.order-details h4 {
    font-size: 1em;
    margin-bottom: 4px;
    color: #1e293b;
}

.order-details p {
    font-size: 0.85em;
    color: #64748b;
}

.order-status {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8em;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-delivered { background: #dcfce7; color: #166534; }
.status-pending { background: #fef3c7; color: #92400e; }
.status-shipped { background: #dbeafe; color: #1d4ed8; }
.status-cancelled { background: #fecaca; color: #dc2626; }

.profile-section {
    background: white;
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
}

.profile-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.info-item {
    padding: 15px;
    background: #f8fafc;
    border-radius: 10px;
    border-left: 4px solid #667eea;
}

.info-item label {
    font-weight: 600;
    color: #64748b;
    font-size: 0.9em;
    display: block;
    margin-bottom: 5px;
}

.info-item span {
    color: #1e293b;
    font-size: 1.1em;
}

.no-orders {
    text-align: center;
    padding: 40px 20px;
    color: #64748b;
}

.no-orders h3 {
    margin-bottom: 10px;
    color: #1e293b;
}

.btn {
    display: inline-block;
    padding: 12px 24px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
    margin-top: 15px;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.action-buttons {
    display: flex;
    gap: 15px;
    margin-top: 20px;
}

@media (max-width: 768px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
    
    .quick-stats {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .user-welcome {
        flex-direction: column;
        text-align: center;
    }
    
    .profile-info {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="dashboard-container">
    <!-- Dashboard Header with User Info -->
    <div class="dashboard-header">
        <div class="user-welcome">
            <div class="user-avatar-large">
                <?php echo strtoupper(substr(($user['fullname'] ?? $user['username'] ?? 'U'), 0, 1)); ?>
            </div>
            <div class="user-info">
                <h1>Welcome back, <?php echo htmlspecialchars($user['fullname'] ?? $user['username'] ?? 'User'); ?>!</h1>
                <p><?php echo htmlspecialchars($user['email'] ?? 'No email provided'); ?></p>
                <?php if ($stats['last_order_date']): ?>
                    <p>Last order: <?php echo date('M d, Y', strtotime($stats['last_order_date'])); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="quick-stats">
            <div class="stat-card">
                <h3><?php echo $stats['total_orders']; ?></h3>
                <p>Total Orders</p>
            </div>
            <div class="stat-card">
                <h3>NRs. <?php echo number_format($stats['total_spent']); ?></h3>
                <p>Total Spent</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $cartCount; ?></h3>
                <p>Cart Items</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $stats['delivered_orders']; ?></h3>
                <p>Completed Orders</p>
            </div>
        </div>
    </div>

    <!-- Profile Information Section -->
    <div class="profile-section">
        <div class="card-header">
            <div class="card-title">
                <div class="card-icon">
                    <i class="fas fa-user"></i>
                </div>
                Personal Information
            </div>
            <a href="./profile.php" class="view-all-btn">Edit Profile</a>
        </div>
        
        <div class="profile-info">
            <div class="info-item">
                <label>Full Name</label>
                <span><?php echo htmlspecialchars($user['fullname'] ?? 'Not provided'); ?></span>
            </div>
            <div class="info-item">
                <label>Email Address</label>
                <span><?php echo htmlspecialchars($user['email'] ?? 'Not provided'); ?></span>
            </div>
            <div class="info-item">
                <label>Phone Number</label>
                <span><?php echo htmlspecialchars($user['phone'] ?? 'Not provided'); ?></span>
            </div>
            <div class="info-item">
                <label>Member Since</label>
                <span><?php echo date('M d, Y', strtotime($user['created_at'] ?? 'now')); ?></span>
            </div>
        </div>
    </div>

    <!-- Dashboard Grid -->
    <div class="dashboard-grid">
        <!-- Recent Orders -->
        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-title">
                    <div class="card-icon">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    Recent Orders
                </div>
                <a href="./myorders.php" class="view-all-btn">View All</a>
            </div>

            <?php if (mysqli_num_rows($recentOrdersResult) > 0): ?>
                <?php while ($order = mysqli_fetch_assoc($recentOrdersResult)): ?>
                    <div class="order-item">
                        <div class="order-details">
                            <h4>Order #<?php echo $order['order_id']; ?></h4>
                            <p><?php echo $order['item_count']; ?> item(s) • <?php echo date('M d, Y', strtotime($order['order_date'])); ?></p>
                            <p><strong>NRs. <?php echo number_format($order['total_amount']); ?></strong></p>
                        </div>
                        <span class="order-status status-<?php echo strtolower($order['status']); ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-orders">
                    <h4>No Orders Yet</h4>
                    <p>Start shopping to see your orders here!</p>
                    <a href="./shop.php" class="btn btn-primary">Start Shopping</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Order Status Summary -->
        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-title">
                    <div class="card-icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    Order Summary
                </div>
            </div>

            <div class="order-item">
                <div class="order-details">
                    <h4>Delivered Orders</h4>
                    <p>Successfully completed orders</p>
                </div>
                <span class="order-status status-delivered">
                    <?php echo $stats['delivered_orders']; ?>
                </span>
            </div>

            <div class="order-item">
                <div class="order-details">
                    <h4>Pending Orders</h4>
                    <p>Orders being processed</p>
                </div>
                <span class="order-status status-pending">
                    <?php echo $stats['pending_orders']; ?>
                </span>
            </div>

            <div class="order-item">
                <div class="order-details">
                    <h4>Shipped Orders</h4>
                    <p>Orders on the way</p>
                </div>
                <span class="order-status status-shipped">
                    <?php echo $stats['shipped_orders']; ?>
                </span>
            </div>

            <?php if ($stats['cancelled_orders'] > 0): ?>
                <div class="order-item">
                    <div class="order-details">
                        <h4>Cancelled Orders</h4>
                        <p>Orders that were cancelled</p>
                    </div>
                    <span class="order-status status-cancelled">
                        <?php echo $stats['cancelled_orders']; ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons">
        <a href="./shop.php" class="btn btn-primary">
            <i class="fas fa-shopping-cart"></i> Continue Shopping
        </a>
        <a href="./myorders.php" class="btn btn-primary">
            <i class="fas fa-list"></i> View All Orders
        </a>
        <a href="./cart.php" class="btn btn-primary">
            <i class="fas fa-shopping-bag"></i> View Cart (<?php echo $cartCount; ?>)
        </a>
    </div>
</div>

<?php include "./footer.php"; ?>
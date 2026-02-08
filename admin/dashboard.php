<?php
include "../config/connection.php";

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT 
    o.id AS `id`,
    c.fullname AS `Customer`,
    p.name AS `Product`,
    o.total_amount AS `Price`,
    o.status AS `Status`,
    od.quantity AS `Quantity`,
    DATE(od.created_at) AS `OrderDate`
FROM orders o
JOIN customers c ON o.customer_id = c.id
JOIN order_details od ON o.id = od.order_id
JOIN products p ON od.product_id = p.id LIMIT 5;";
$res = mysqli_query($conn, $sql);

$sql_sales_chart = "SELECT 
    DATE_FORMAT(o.created_at, '%Y-%m') as month,
    SUM(p.price * oi.quantity) as total_sales,
    COUNT(o.id) as order_count
    FROM orders o 
    JOIN order_details oi ON o.id = oi.order_id 
    JOIN products p ON oi.product_id = p.id 
    GROUP BY DATE_FORMAT(o.created_at, '%Y-%m') 
    ORDER BY month ASC";
$res_sales_chart = mysqli_query($conn, $sql_sales_chart);

$chart_data = [];
$chart_labels = [];
$chart_sales = [];

while ($row_chart = mysqli_fetch_assoc($res_sales_chart)) {
    $chart_labels[] = date('M Y', strtotime($row_chart['month'] . '-01'));
    $chart_sales[] = floatval($row_chart['total_sales']);
}

// Total Products
$sql_products = "SELECT COUNT(*) AS total_products FROM products";
$res_products = mysqli_query($conn, $sql_products);
$row_products = mysqli_fetch_assoc($res_products);
$total_products = $row_products['total_products'];

// Total Orders
$sql_orders = "SELECT COUNT(*) AS total_orders FROM orders";
$res_orders = mysqli_query($conn, $sql_orders);
$row_orders = mysqli_fetch_assoc($res_orders);
$total_orders = $row_orders['total_orders'];

// Total Customers
$sql_customers = "SELECT COUNT(*) AS total_customers FROM customers";
$res_customers = mysqli_query($conn, $sql_customers);
$row_customers = mysqli_fetch_assoc($res_customers);
$total_customers = $row_customers['total_customers'];

// Total Sales (Completed orders only) - Simple query
$sql_total_sales = "SELECT SUM(total_amount) as total_sales 
                    FROM orders";
$res_total_sales = mysqli_query($conn, $sql_total_sales);
$row_total_sales = mysqli_fetch_assoc($res_total_sales);
$total_sales = $row_total_sales['total_sales'] ?? 0;
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-number"><?php echo $total_products; ?></div>
        <div class="stat-label">Total Products</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?php echo $total_orders; ?></div>
        <div class="stat-label">Total Orders</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?php echo $total_customers; ?></div>
        <div class="stat-label">Total Customers</div>
    </div>
</div>

<div class="chart-container">
    <div class="chart-title">Sales Overview (Last 6 Months)</div>
    <canvas id="salesChart" width="400" height="200"
        data-labels="<?php echo htmlspecialchars(json_encode($chart_labels)); ?>"
        data-sales="<?php echo htmlspecialchars(json_encode($chart_sales)); ?>">
    </canvas>
</div>

<div class="table-container">
    <div class="table-header">
        <div class="table-title">Recent Orders</div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Product</th>
                <th>Price</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1;
            while ($row = mysqli_fetch_assoc($res)) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['Customer']; ?></td>
                    <td><?php echo $row['Product']; ?></td>
                    <td>Rs.<?php echo number_format($row['Price'], 2); ?></td>
                    <td> <?php if ($row['Status'] === 'Completed'): ?>
                            <span class="status status-active"><?php echo $row['Status']; ?></span>
                        <?php else: ?>
                            <span class="status status-inactive"><?php echo $row['Status']; ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php $i++;
            } ?>
        </tbody>
    </table>
</div>
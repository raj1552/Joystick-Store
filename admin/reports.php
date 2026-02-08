<?php
include "../config/connection.php";

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Date filter handling
$date_filter = $_GET['date_filter'] ?? 'last_30_days';
$custom_start = $_GET['start_date'] ?? '';
$custom_end = $_GET['end_date'] ?? '';

// Build date condition
$date_condition = "";
switch ($date_filter) {
    case 'today':
        $date_condition = "AND DATE(o.created_at) = CURDATE()";
        break;
    case 'yesterday':
        $date_condition = "AND DATE(o.created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
        break;
    case 'last_7_days':
        $date_condition = "AND o.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        break;
    case 'last_30_days':
        $date_condition = "AND o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        break;
    case 'last_3_months':
        $date_condition = "AND o.created_at >= DATE_SUB(NOW(), INTERVAL 3 MONTH)";
        break;
    case 'last_year':
        $date_condition = "AND o.created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
        break;
    case 'custom':
        if ($custom_start && $custom_end) {
            $date_condition = "AND DATE(o.created_at) BETWEEN '$custom_start' AND '$custom_end'";
        }
        break;
}

// Sales Overview
$sql_sales_overview = "SELECT 
    COUNT(o.id) as total_orders,
    SUM(o.total_amount) as total_revenue,
    AVG(o.total_amount) as avg_order_value,
    COUNT(DISTINCT o.customer_id) as unique_customers
    FROM orders o 
    WHERE 1=1 $date_condition";
$res_sales_overview = mysqli_query($conn, $sql_sales_overview);
$sales_overview = mysqli_fetch_assoc($res_sales_overview);

// Sales by Status
$sql_status = "SELECT 
    o.status,
    COUNT(*) as count,
    SUM(o.total_amount) as revenue
    FROM orders o 
    WHERE 1=1 $date_condition
    GROUP BY o.status";
$res_status = mysqli_query($conn, $sql_status);
$status_data = [];
while ($row = mysqli_fetch_assoc($res_status)) {
    $status_data[] = $row;
}

// Daily Sales Chart (Last 30 days)
$sql_daily_sales = "SELECT 
    DATE(o.created_at) as order_date,
    COUNT(*) as order_count,
    SUM(o.total_amount) as daily_revenue
    FROM orders o 
    WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY DATE(o.created_at)
    ORDER BY order_date ASC";
$res_daily_sales = mysqli_query($conn, $sql_daily_sales);
$daily_sales_data = [];
$daily_labels = [];
$daily_revenue = [];
while ($row = mysqli_fetch_assoc($res_daily_sales)) {
    $daily_labels[] = date('M d', strtotime($row['order_date']));
    $daily_revenue[] = floatval($row['daily_revenue']);
}

// Top Products
$sql_top_products = "SELECT 
    p.name,
    p.price,
    SUM(od.quantity) as total_sold,
    SUM(od.subtotal) as total_revenue
    FROM order_details od
    JOIN products p ON od.product_id = p.id
    JOIN orders o ON od.order_id = o.id
    WHERE 1=1 $date_condition
    GROUP BY od.product_id, p.name, p.price
    ORDER BY total_sold DESC
    LIMIT 10";
$res_top_products = mysqli_query($conn, $sql_top_products);
$top_products = [];
while ($row = mysqli_fetch_assoc($res_top_products)) {
    $top_products[] = $row;
}

// Top Customers
$sql_top_customers = "SELECT 
    c.fullname,
    c.email,
    COUNT(o.id) as order_count,
    SUM(o.total_amount) as total_spent
    FROM orders o
    JOIN customers c ON o.customer_id = c.id
    WHERE 1=1 $date_condition
    GROUP BY o.customer_id, c.fullname, c.email
    ORDER BY total_spent DESC
    LIMIT 10";
$res_top_customers = mysqli_query($conn, $sql_top_customers);
$top_customers = [];
while ($row = mysqli_fetch_assoc($res_top_customers)) {
    $top_customers[] = $row;
}

// Monthly Growth
$sql_monthly_growth = "SELECT 
    DATE_FORMAT(o.created_at, '%Y-%m') as month,
    COUNT(*) as order_count,
    SUM(o.total_amount) as revenue,
    COUNT(DISTINCT o.customer_id) as unique_customers
    FROM orders o 
    WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(o.created_at, '%Y-%m')
    ORDER BY month ASC";
$res_monthly_growth = mysqli_query($conn, $sql_monthly_growth);
$monthly_data = [];
$monthly_labels = [];
$monthly_revenue = [];
while ($row = mysqli_fetch_assoc($res_monthly_growth)) {
    $monthly_labels[] = date('M Y', strtotime($row['month'] . '-01'));
    $monthly_revenue[] = floatval($row['revenue']);
}

// Product Categories Performance
$sql_category_performance = "SELECT 
    c.name as category_name,
    COUNT(od.id) as items_sold,
    SUM(od.subtotal) as category_revenue
    FROM order_details od
    JOIN products p ON od.product_id = p.id
    JOIN categories c ON p.category_id = c.id
    JOIN orders o ON od.order_id = o.id
    WHERE 1=1 $date_condition
    GROUP BY c.id, c.name
    ORDER BY category_revenue DESC";
$res_category_performance = mysqli_query($conn, $sql_category_performance);
$category_performance = [];
while ($row = mysqli_fetch_assoc($res_category_performance)) {
    $category_performance[] = $row;
}

include "header.php" 
?>

<div class="reports-container">
    <!-- Filter Section -->
    <div class="filter-section">
        <form method="GET" action="">
            <div class="filter-row">
                <div class="filter-group">
                    <label>Date Range</label>
                    <select name="date_filter" id="dateFilter" onchange="toggleCustomDate()">
                        <option value="today" <?= $date_filter == 'today' ? 'selected' : '' ?>>Today</option>
                        <option value="yesterday" <?= $date_filter == 'yesterday' ? 'selected' : '' ?>>Yesterday</option>
                        <option value="last_7_days" <?= $date_filter == 'last_7_days' ? 'selected' : '' ?>>Last 7 Days</option>
                        <option value="last_30_days" <?= $date_filter == 'last_30_days' ? 'selected' : '' ?>>Last 30 Days</option>
                        <option value="last_3_months" <?= $date_filter == 'last_3_months' ? 'selected' : '' ?>>Last 3 Months</option>
                        <option value="last_year" <?= $date_filter == 'last_year' ? 'selected' : '' ?>>Last Year</option>
                        <option value="custom" <?= $date_filter == 'custom' ? 'selected' : '' ?>>Custom Range</option>
                    </select>
                </div>
                
                <div id="customDates" style="display: <?= $date_filter == 'custom' ? 'flex' : 'none' ?>; gap: 15px;">
                    <div class="filter-group">
                        <label>Start Date</label>
                        <input type="date" name="start_date" value="<?= $custom_start ?>">
                    </div>
                    <div class="filter-group">
                        <label>End Date</label>
                        <input type="date" name="end_date" value="<?= $custom_end ?>">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn-filter">Apply Filter</button>
        </form>
    </div>

    <!-- Sales Overview Stats -->
    <div class="stats-overview">
        <div class="stat-card">
            <div class="stat-number"><?= number_format($sales_overview['total_orders'] ?? 0) ?></div>
            <div class="stat-label">Total Orders</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">Rs. <?= number_format($sales_overview['total_revenue'] ?? 0) ?></div>
            <div class="stat-label">Total Revenue</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">Rs. <?= number_format($sales_overview['avg_order_value'] ?? 0) ?></div>
            <div class="stat-label">Avg Order Value</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= number_format($sales_overview['unique_customers'] ?? 0) ?></div>
            <div class="stat-label">Unique Customers</div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="reports-grid">
        <!-- Daily Sales Chart -->
        <div class="chart-container">
            <div class="chart-title">Daily Revenue Trend (Last 30 Days)</div>
            <canvas id="dailySalesChart" width="400" height="200"
                data-labels="<?= htmlspecialchars(json_encode($daily_labels)) ?>"
                data-revenue="<?= htmlspecialchars(json_encode($daily_revenue)) ?>">
            </canvas>
        </div>

        <!-- Order Status Breakdown -->
        <div class="status-breakdown">
            <div class="chart-title">Order Status Breakdown</div>
            <?php foreach ($status_data as $status): ?>
                <div class="status-item">
                    <div>
                        <div class="status-label"><?= ucfirst($status['status']) ?></div>
                        <div class="status-revenue">Rs. <?= number_format($status['revenue']) ?></div>
                    </div>
                    <div class="status-value"><?= $status['count'] ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Top Products & Customers -->
    <div class="tables-grid">
        <!-- Top Products -->
        <div class="table-container">
            <div class="table-header">
                <div class="table-title">Top Selling Products</div>
            </div>
            <div class="table-content">
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Sold</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($top_products as $product): ?>
                            <tr>
                                <td><?= htmlspecialchars($product['name']) ?></td>
                                <td><?= $product['total_sold'] ?></td>
                                <td>Rs. <?= number_format($product['total_revenue']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Customers -->
        <div class="table-container">
            <div class="table-header">
                <div class="table-title">Top Customers</div>
            </div>
            <div class="table-content">
                <table>
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Orders</th>
                            <th>Total Spent</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($top_customers as $customer): ?>
                            <tr>
                                <td>
                                    <div><?= htmlspecialchars($customer['fullname']) ?></div>
                                    <div style="font-size: 12px; color: #9ca3af;"><?= htmlspecialchars($customer['email']) ?></div>
                                </td>
                                <td><?= $customer['order_count'] ?></td>
                                <td>Rs. <?= number_format($customer['total_spent']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Monthly Growth Chart -->
    <div class="full-width-section">
        <div class="chart-title">Monthly Revenue Growth (Last 12 Months)</div>
        <canvas id="monthlyGrowthChart" width="400" height="200"
            data-labels="<?= htmlspecialchars(json_encode($monthly_labels)) ?>"
            data-revenue="<?= htmlspecialchars(json_encode($monthly_revenue)) ?>">
        </canvas>
    </div>

    <!-- Category Performance -->
    <div class="table-container">
        <div class="table-header">
            <div class="table-title">Category Performance</div>
        </div>
        <div class="table-content">
            <table>
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Items Sold</th>
                        <th>Revenue</th>
                        <th>% of Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_category_revenue = array_sum(array_column($category_performance, 'category_revenue'));
                    foreach ($category_performance as $category): 
                        $percentage = $total_category_revenue > 0 ? ($category['category_revenue'] / $total_category_revenue) * 100 : 0;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($category['category_name']) ?></td>
                            <td><?= $category['items_sold'] ?></td>
                            <td>Rs. <?= number_format($category['category_revenue']) ?></td>
                            <td><?= number_format($percentage, 1) ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function toggleCustomDate() {
    const dateFilter = document.getElementById('dateFilter').value;
    const customDates = document.getElementById('customDates');
    customDates.style.display = dateFilter === 'custom' ? 'flex' : 'none';
}

// Chart.js configurations
document.addEventListener('DOMContentLoaded', function() {
    // Daily Sales Chart
    const dailyChart = document.getElementById('dailySalesChart');
    if (dailyChart) {
        const dailyLabels = JSON.parse(dailyChart.dataset.labels);
        const dailyRevenue = JSON.parse(dailyChart.dataset.revenue);

        new Chart(dailyChart, {
            type: 'line',
            data: {
                labels: dailyLabels,
                datasets: [{
                    label: 'Daily Revenue',
                    data: dailyRevenue,
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rs. ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    // Monthly Growth Chart
    const monthlyChart = document.getElementById('monthlyGrowthChart');
    if (monthlyChart) {
        const monthlyLabels = JSON.parse(monthlyChart.dataset.labels);
        const monthlyRevenue = JSON.parse(monthlyChart.dataset.revenue);

        new Chart(monthlyChart, {
            type: 'bar',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Monthly Revenue',
                    data: monthlyRevenue,
                    backgroundColor: 'rgba(102, 126, 234, 0.8)',
                    borderColor: '#667eea',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rs. ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
<?php
// Create this as test-payment.php to test your database connection and table

include "./config/connection.php";

// Test database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
echo "✅ Database connected successfully<br>";

// Check if payments table exists and show structure
$result = $conn->query("DESCRIBE payments");
if (!$result) {
    die("❌ Payments table doesn't exist or error: " . $conn->error);
}

echo "✅ Payments table structure:<br>";
while ($row = $result->fetch_assoc()) {
    echo "- " . $row['Field'] . " (" . $row['Type'] . ")<br>";
}

// Test insert
echo "<br>🧪 Testing payment insert...<br>";

$test_order_id = 999; // Use a test order ID
$test_amount = 100.50;
$test_method = 'esewa';
$test_status = 'pending';

$stmt = $conn->prepare("
    INSERT INTO payments 
    (order_id, amount, payment_method, payment_status, payment_date, created_at, updated_at)
    VALUES (?, ?, ?, ?, NOW(), NOW(), NOW())
");

if (!$stmt) {
    die("❌ Prepare failed: " . $conn->error);
}

$stmt->bind_param("idss", $test_order_id, $test_amount, $test_method, $test_status);

if ($stmt->execute()) {
    $payment_id = $conn->insert_id;
    echo "✅ Test payment inserted successfully with ID: $payment_id<br>";
    
    // Verify the insert
    $verify = $conn->query("SELECT * FROM payments WHERE id = $payment_id");
    if ($verify && $row = $verify->fetch_assoc()) {
        echo "📋 Inserted record:<br>";
        foreach ($row as $key => $value) {
            echo "- $key: $value<br>";
        }
        
        // Clean up test record
        $conn->query("DELETE FROM payments WHERE id = $payment_id");
        echo "<br>🧹 Test record cleaned up<br>";
    }
} else {
    echo "❌ Insert failed: " . $stmt->error . "<br>";
}

// Check current payment records
echo "<br>📊 Current payment records (last 5):<br>";
$recent = $conn->query("SELECT * FROM payments ORDER BY created_at DESC LIMIT 5");
if ($recent && $recent->num_rows > 0) {
    while ($row = $recent->fetch_assoc()) {
        echo "ID: {$row['id']}, Order: {$row['order_id']}, Method: {$row['payment_method']}, Status: {$row['payment_status']}, Created: {$row['created_at']}<br>";
    }
} else {
    echo "No payment records found.<br>";
}

echo "<br>✅ Test completed!";
?>
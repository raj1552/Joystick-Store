<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "./config/connection.php";

// If user not logged in, redirect
if (!isset($_SESSION['user_id'])) {
    header("Location: ./customer-login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch customer info
$sql = "SELECT * FROM customers WHERE id = '$user_id'";
$res = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($res);

$fullname = $data['fullname'];
$nameParts = explode(" ", $fullname, 2);
$firstname = $nameParts[0];
$lastname = isset($nameParts[1]) ? $nameParts[1] : "";

// ✅ Always calculate total for display
$sqlTotal = "SELECT SUM(c.quantity * p.price) AS total
             FROM cart c
             JOIN products p ON c.product_id = p.id
             WHERE c.customer_id = '$user_id' AND c.status = 'pending'";
$resTotal = mysqli_query($conn, $sqlTotal);
$rowTotal = mysqli_fetch_assoc($resTotal);
$total_amount = $rowTotal['total'] ?? 0;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';
    $city = isset($_POST['city']) ? trim($_POST['city']) : '';
    $state = isset($_POST['state']) ? trim($_POST['state']) : '';
    $zip = isset($_POST['zip']) ? trim($_POST['zip']) : '';

    // Validate required fields
    if (empty($address) || empty($city) || empty($state) || empty($zip)) {
        echo "<script>alert('Please fill in all shipping address fields!');</script>";
    } else {
        // Start transaction
        mysqli_autocommit($conn, false);

        try {
            // Insert into orders table
            $sqlOrder = "INSERT INTO orders (customer_id, total_amount, status, created_at) 
                         VALUES ('$user_id', '$total_amount', 'pending', NOW())";
            
            if (!mysqli_query($conn, $sqlOrder)) {
                throw new Exception("Error inserting order: " . mysqli_error($conn));
            }

            $order_id = mysqli_insert_id($conn);

            // Get cart items
            $sqlCart = "SELECT c.product_id, c.quantity, p.price, c.color
                        FROM cart c
                        JOIN products p ON c.product_id = p.id
                        WHERE c.customer_id = '$user_id' AND c.status = 'pending'";
            $resCart = mysqli_query($conn, $sqlCart);

            if (!$resCart) {
                throw new Exception("Error fetching cart items: " . mysqli_error($conn));
            }

            $orderDetailsInserted = 0;
            while ($row = mysqli_fetch_assoc($resCart)) {
                $product_id = $row['product_id'];
                $color = $row['color'];
                $quantity = $row['quantity'];
                $unit_price = $row['price'];
                $subtotal = $quantity * $unit_price;

                $sqlInsert = "INSERT INTO order_details 
                    (order_id, color, address, city, state, zip, product_id, quantity, unit_price, subtotal, created_at) 
                    VALUES 
                    ('$order_id', '$color', '$address', '$city', '$state', '$zip', '$product_id', '$quantity', '$unit_price', '$subtotal', NOW())";
                
                if (!mysqli_query($conn, $sqlInsert)) {
                    throw new Exception("Error inserting order details: " . mysqli_error($conn));
                }
                $orderDetailsInserted++;
            }

            // Update cart status to 'completed' and set order_id
            $sqlUpdate = "UPDATE cart 
                         SET status = 'completed', order_id = '$order_id', updated_at = NOW() 
                         WHERE customer_id = '$user_id' AND status = 'pending'";
            
            if (!mysqli_query($conn, $sqlUpdate)) {
                throw new Exception("Error updating cart: " . mysqli_error($conn));
            }
            
            // Optional: Log how many cart items were updated
            $affected_rows = mysqli_affected_rows($conn);
            echo "<script>console.log('Cart items updated: $affected_rows');</script>";

            // Commit transaction
            mysqli_commit($conn);
            
            // Store order info in session for payment
            $_SESSION['pending_order_id'] = $order_id;
            $_SESSION['order_total'] = $total_amount;
            
            // Instead of redirecting to orders.php, show payment selection
            echo "<script>
                alert('Order created successfully! Please select payment method.');
                setTimeout(function() {
                    document.querySelector('.popup-overlay').classList.remove('show');
                    var paymentOverlay = document.querySelector('#paymentOverlay');
                    if (paymentOverlay) {
                        paymentOverlay.classList.add('show');
                        paymentOverlay.dataset.total = '$total_amount';
                    } else {
                        console.error('Payment overlay not found');
                        // Fallback: redirect to a payment selection page
                        window.location.href = 'payment-select.php?order_id=$order_id&total=$total_amount';
                    }
                }, 500);
            </script>";

        } catch (Exception $e) {
            // Rollback transaction
            mysqli_rollback($conn);
            echo "<script>alert('Error placing order: " . $e->getMessage() . "');</script>";
        }

        // Restore autocommit
        mysqli_autocommit($conn, true);
    }
}
?>

<!-- Rest of your HTML remains the same -->
<div class="popup-overlay" id="popupOverlay">
    <div class="popup-dialog">
        <div class="popup-header">
            <h2>Checkout</h2>
            <button class="close-btn" id="closeBtn">&times;</button>
        </div>

        <form action="" method="POST" class="form-wrapper">
            <!-- Hidden user ID -->
            <input type="hidden" name="customer_id" value="<?php echo $data['id']; ?>" />

            <!-- Personal Info -->
            <div class="popup-content">
                <h3>Personal Information</h3>
                <div class="form-row">
                    <div class="form-container">
                        <label for="fname">First Name</label>
                        <input type="text" id="fname" name="fname" value="<?php echo htmlspecialchars($firstname) ?>" readonly />
                    </div>
                    <div class="form-container">
                        <label for="lname">Last Name</label>
                        <input type="text" id="lname" name="lname" value="<?php echo htmlspecialchars($lastname) ?>" readonly />
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-container">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email"
                            value="<?php echo htmlspecialchars($data['email']); ?>" required />
                    </div>
                    <div class="form-container">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone"
                            value="<?php echo htmlspecialchars($data['phone']); ?>" required />
                    </div>
                </div>
            </div>

            <!-- Shipping Info -->
            <div class="popup-content">
                <h3>Shipping Address</h3>
                <div class="form-row">
                    <div class="form-container">
                        <label for="address">Address</label>
                        <input type="text" id="address" name="address" required />
                    </div>
                    <div class="form-container">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" required />
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-container">
                        <label for="state">State/Province*</label>
                        <input type="text" id="state" name="state" required />
                    </div>
                    <div class="form-container">
                        <label for="zip">ZIP/Postal Code*</label>
                        <input type="number" id="zip" name="zip" required />
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="order-summary">
                <h4>Order Summary</h4>
                <div class="total">
                    <h3>Total</h3>
                    <p class="highlight">
                        NRs. <?= isset($total_amount) ? number_format($total_amount) : "0"; ?>
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="popup-footer">
                <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
                <button type="submit" class="btn btn-primary" id="confirmBtn">Confirm & Pay</button>
            </div>
        </form>
    </div>
</div>
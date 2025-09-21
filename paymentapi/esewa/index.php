<?php 
// Get parameters from URL and validate them properly
$total_amount = isset($_GET['total']) ? floatval($_GET['total']) : 0;
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

// Debug: Log received parameters
error_log("eSewa Gateway - Raw GET parameters: " . print_r($_GET, true));
error_log("eSewa Gateway - Processed parameters: total=$total_amount, order_id=$order_id");

// Validate that both parameters are present and valid
if (empty($order_id) || $order_id <= 0) {
    error_log("ERROR: Invalid or missing order_id: " . $_GET['order_id']);
    echo "<script>
        alert('Order ID is missing or invalid. Please try again.');
        window.location.href = '../../cart.php';
    </script>";
    exit();
}

if (empty($total_amount) || $total_amount <= 0) {
    error_log("ERROR: Invalid or missing total amount: " . $_GET['total']);
    echo "<script>
        alert('Total amount is missing or invalid. Please try again.');
        window.location.href = '../../cart.php';
    </script>";
    exit();
}

// Generate transaction UUID
$transaction_uuid = date('ymd-His') . '-' . $order_id;

error_log("eSewa Gateway - Generated transaction UUID: $transaction_uuid");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Sewa Payment Gateway</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <link href="./app.css" rel="stylesheet" type="text/css">
</head>
<body>
    <div class="apibox">
        <img src="./api.png" alt="">
        <div class="apibox__detail">
            <h2 class="apibox__title">Complete Your Payment</h2>
            <div class="meta-box">
                <span class="meta-box__item">
                    Rs. <strong><?php echo number_format($total_amount); ?></strong>
                </span>
            </div>
            <div class="text-box">
                <p>Order ID: #<?php echo htmlspecialchars($order_id); ?></p>
                <p>Please review your payment details before proceeding.</p>
            </div>
            
            <form action="https://rc-epay.esewa.com.np/api/epay/main/v2/form" method="POST" onsubmit="generateSignature()">
                <div class="field-group">
                    <label for="amount">Amount:</label>
                    <input type="text" id="amount" name="amount" value="<?= $total_amount ?>" class="form" readonly>
                </div>
                <div class="field-group">
                    <label for="tax_amount">Tax Amount:</label>
                    <input type="text" id="tax_amount" name="tax_amount" value="0" class="form" readonly>
                </div>
                <div class="field-group">
                    <label for="total_amount">Total Amount:</label>
                    <input type="text" id="total_amount" name="total_amount" value="<?= $total_amount ?>" class="form" readonly>
                </div>
                <div class="field-group">
                    <label for="transaction_uuid">Transaction UUID:</label>
                    <input type="text" id="transaction_uuid" name="transaction_uuid" value="<?= $transaction_uuid ?>" class="form" readonly>
                </div>
                <div class="field-group">
                    <label for="product_code">Product Code:</label>
                    <input type="text" id="product_code" name="product_code" value="EPAYTEST" class="form" readonly>
                </div>
                <div class="field-group">
                    <label for="product_service_charge">Product Service Charge:</label>
                    <input type="text" id="product_service_charge" name="product_service_charge" value="0" class="form" readonly>
                </div>
                <div class="field-group">
                    <label for="product_delivery_charge">Product Delivery Charge:</label>
                    <input type="text" id="product_delivery_charge" name="product_delivery_charge" value="0" class="form" readonly>
                </div>
                
                <!-- Updated Success URL to include order_id -->
                <div class="field-group">
                    <label for="success_url">Success URL:</label>
                    <input type="text" id="success_url" name="success_url" 
                           value="http://localhost/LevelUp/thankyou.php?>" 
                           class="form" readonly>
                </div>
                
                <!-- Updated Failure URL to include order_id -->
                <div class="field-group">
                    <label for="failure_url">Failure URL:</label>
                    <input type="text" id="failure_url" name="failure_url" 
                           value="http://localhost/LevelUp/paymentapi/thankyou.php?>" 
                           class="form" readonly>
                </div>
                
                <div class="field-group">
                    <label for="signed_field_names">Signed Field Names:</label>
                    <input type="text" id="signed_field_names" name="signed_field_names" 
                           value="total_amount,transaction_uuid,product_code" class="form" readonly>
                </div>
                <div class="field-group">
                    <label for="signature">Signature:</label>
                    <input type="text" id="signature" name="signature" value="" class="form" readonly>
                </div>
                
                <!-- Hidden secret key - don't show in form -->
                <input type="hidden" id="secret" name="secret" value="8gBm/:&EnhH.1/q">
                
                <div class="button-group">
                    <button type="submit" class="button">Pay with eSewa</button>
                    <a href="../../cart.php" class="button button-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/hmac-sha256.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/enc-base64.min.js"></script>
    <script>
        // Function to generate signature
        function generateSignature() {
            var total_amount = document.getElementById("total_amount").value;
            var transaction_uuid = document.getElementById("transaction_uuid").value;
            var product_code = document.getElementById("product_code").value;
            var secret = document.getElementById("secret").value;

            console.log('Generating signature with:', {
                total_amount: total_amount,
                transaction_uuid: transaction_uuid,
                product_code: product_code
            });

            var hash = CryptoJS.HmacSHA256(
                `total_amount=${total_amount},transaction_uuid=${transaction_uuid},product_code=${product_code}`,
                `${secret}`
            );
                
            var hashInBase64 = CryptoJS.enc.Base64.stringify(hash);
            document.getElementById("signature").value = hashInBase64;
            
            console.log('Generated signature:', hashInBase64);
        }

        // Generate signature when page loads
        document.addEventListener('DOMContentLoaded', function() {
            generateSignature();
        });
    </script>
</body>
</html>
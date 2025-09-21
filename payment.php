<?php
// Get the total amount from session if available
$total_amount = $_SESSION['order_total'] ?? 0;
$order_id = $_SESSION['pending_order_id'] ?? 0;

// Debug: Log session values
error_log("Payment Popup - Session order_id: " . ($order_id ?: 'NOT SET'));
error_log("Payment Popup - Session total: " . ($total_amount ?: 'NOT SET'));

// Convert to proper types for validation
$total_amount = (float)$total_amount;
$order_id = (int)$order_id;
?>

<!-- Payment Popup -->
<div class="popup-overlay" id="paymentOverlay" data-total="<?= $total_amount ?>">
    <div class="popup-dialog">
        <div class="popup-header">
            <h2>Payment Method</h2>
            <button class="close-btn" id="paymentCloseBtn">&times;</button>
        </div>
        
        <form action="./process-payment.php" method="POST" id="paymentForm">
            <!-- Hidden fields to pass order info -->
            <input type="hidden" name="order_id" value="<?= $order_id ?>" id="hiddenOrderId" />
            <input type="hidden" name="total_amount" value="<?= $total_amount ?>" id="hiddenTotalAmount" />
            
            <div class="popup-content payment">
                <div class="payment-options">
                    <label>
                        <input type="radio" name="paymentMethod" value="esewa" checked>
                        <img src="./public/assets/esewa.png" alt="eSewa" />
                    </label>
                    <label>
                        <input type="radio" name="paymentMethod" value="khalti">
                        <img src="./public/assets/logo1.png" alt="Khalti" />
                    </label>
                    <label>
                        <input type="radio" name="paymentMethod" value="cod">
                          <img src="./public/assets/cod.png" alt="Cash On Delivery" />
                    </label>
                </div>
                
                <div class="order-summary">
                    <h4>Payment Summary</h4>
                    <?php if ($order_id > 0 && $total_amount > 0): ?>
                        <p>Order ID: #<?= $order_id ?></p>
                        <div class="total">
                            <h3>Total Amount</h3>
                            <p class="highlight">NRs. <?= number_format($total_amount) ?></p>
                        </div>
                    <?php else: ?>
                        <p style="color: red;">⚠️ Order information not found. Please try again.</p>
                        <p style="color: red;">Order ID: <?= $order_id ?>, Amount: <?= $total_amount ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="popup-footer">
                <button type="button" class="btn btn-secondary" id="paymentCancelBtn">Cancel</button>
                <button type="submit" class="btn btn-primary" id="paymentConfirmBtn" 
                        <?= ($order_id <= 0 || $total_amount <= 0) ? 'disabled' : '' ?>>
                    Checkout
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const paymentForm = document.getElementById("paymentForm");
    const paymentConfirmBtn = document.getElementById("paymentConfirmBtn");
    
    // Check if order data is available
    const orderIdField = document.getElementById("hiddenOrderId");
    const totalField = document.getElementById("hiddenTotalAmount");
    
    console.log('Form validation - Order ID:', orderIdField.value, 'Total:', totalField.value);
    
    // Convert to numbers for proper validation
    const orderId = parseInt(orderIdField.value) || 0;
    const totalAmount = parseFloat(totalField.value) || 0;
    
    if (orderId <= 0 || totalAmount <= 0) {
        console.error('Order ID or total amount is missing/invalid', {
            orderId: orderId,
            totalAmount: totalAmount,
            originalOrderId: orderIdField.value,
            originalTotal: totalField.value
        });
        paymentConfirmBtn.disabled = true;
        paymentConfirmBtn.textContent = 'Order Info Missing';
        return;
    }
    
    console.log('Form validation passed', { orderId, totalAmount });
    
    // Update button text based on payment method selection
    document.querySelectorAll('input[name="paymentMethod"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            if (this.value === 'cod') {
                paymentConfirmBtn.textContent = 'Confirm Order';
            } else {
                paymentConfirmBtn.textContent = 'Pay Now';
            }
        });
    });
    
    // Set initial button state
    const initialMethod = document.querySelector('input[name="paymentMethod"]:checked')?.value;
    if (initialMethod === 'cod') {
        paymentConfirmBtn.textContent = 'Confirm Order';
    } else {
        paymentConfirmBtn.textContent = 'Pay Now';
    }
    
    // Handle form submission
    paymentForm.addEventListener('submit', function(e) {
        const selectedMethod = document.querySelector('input[name="paymentMethod"]:checked')?.value;
        
        console.log('Form submission attempt', {
            method: selectedMethod,
            orderId: orderIdField.value,
            total: totalField.value
        });
        
        if (!selectedMethod) {
            alert("Please select a payment method.");
            e.preventDefault();
            return;
        }
        
        // Re-validate order data before submission
        if (orderId <= 0 || totalAmount <= 0) {
            alert("Order information is missing or invalid. Please try again.");
            console.error('Form submission blocked due to invalid data');
            e.preventDefault();
            return;
        }
        
        // Show loading state
        paymentConfirmBtn.textContent = 'Processing...';
        paymentConfirmBtn.disabled = true;
        
        // Form will submit to process-payment.php
        console.log('Submitting payment form', {
            method: selectedMethod,
            orderId: orderId,
            total: totalAmount
        });
    });
    
    // Close payment popup handlers
    document.getElementById("paymentCloseBtn")?.addEventListener("click", () => {
        document.getElementById("paymentOverlay")?.classList.remove("show");
    });
    
    document.getElementById("paymentCancelBtn")?.addEventListener("click", (e) => {
        e.preventDefault();
        document.getElementById("paymentOverlay")?.classList.remove("show");
    });
});
</script>
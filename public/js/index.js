document.addEventListener("DOMContentLoaded", () => {
  const checkoutBtn = document.getElementById("checkoutBtn"); // Proceed to checkout
  const popupOverlay = document.getElementById("popupOverlay"); // Checkout popup
  const confirmBtn = document.getElementById("confirmBtn"); // Confirm & Pay in checkout
  const closeBtn = document.getElementById("closeBtn"); // X button in checkout
  const cancelBtn = document.getElementById("cancelBtn"); // Cancel in checkout

  const paymentOverlay = document.getElementById("paymentOverlay"); // Payment popup
  const paymentCloseBtn = document.getElementById("paymentCloseBtn"); // X button in payment
  const paymentCancelBtn = document.getElementById("paymentCancelBtn"); // Cancel in payment
  const paymentForm = document.getElementById("paymentForm"); // Payment form

  // Show checkout popup
  checkoutBtn?.addEventListener("click", () => {
    popupOverlay?.classList.add("show");
  });

  // Close checkout popup (X / Cancel)
  closeBtn?.addEventListener("click", () =>
    popupOverlay?.classList.remove("show")
  );
  cancelBtn?.addEventListener("click", () =>
    popupOverlay?.classList.remove("show")
  );

  // Let form submit normally to PHP for checkout
  confirmBtn?.addEventListener("click", (e) => {
    // Validate form first
    const address = document.getElementById("address").value.trim();
    const city = document.getElementById("city").value.trim();
    const state = document.getElementById("state").value.trim();
    const zip = document.getElementById("zip").value.trim();

    if (!address || !city || !state || !zip) {
      alert("Please fill in all shipping address fields!");
      e.preventDefault();
      return;
    }

    // Let the form submit normally to PHP
    // PHP will handle showing the payment popup
  });

  // Show/Hide payment popup functions
  function showPaymentPopup() {
    paymentOverlay?.classList.add("show");
    paymentOverlay?.style.setProperty("display", "flex");
  }

  function hidePaymentPopup() {
    paymentOverlay?.classList.remove("show");
    paymentOverlay?.style.setProperty("display", "none");
  }

  // Close payment popup (X / Cancel)
  paymentCloseBtn?.addEventListener("click", hidePaymentPopup);
  paymentCancelBtn?.addEventListener("click", hidePaymentPopup);

  // FIXED: Let ALL payment methods submit to PHP first
  // Remove the click event listener that was preventing form submission
  // The form will now always submit to process-payment.php first

  // Update button text based on payment method selection
  document
    .querySelectorAll('input[name="paymentMethod"]')
    .forEach(function (radio) {
      radio.addEventListener("change", function () {
        const confirmBtn = document.getElementById("paymentConfirmBtn");
        if (this.value === "cod") {
          confirmBtn.textContent = "Confirm Order";
        } else {
          confirmBtn.textContent = "Pay Now";
        }
        // Remove the type changes - let it always be submit
        confirmBtn.type = "submit";
      });
    });

  // Set initial button state
  const initialPaymentMethod = document.querySelector(
    'input[name="paymentMethod"]:checked'
  )?.value;
  const initialConfirmBtn = document.getElementById("paymentConfirmBtn");
  if (initialPaymentMethod === "cod") {
    initialConfirmBtn.textContent = "Confirm Order";
  } else {
    initialConfirmBtn.textContent = "Pay Now";
  }
  initialConfirmBtn.type = "submit"; // Always submit to PHP

  // Add form submission handler for loading state
  paymentForm?.addEventListener("submit", function(e) {
    const submitBtn = document.getElementById("paymentConfirmBtn");
    const selectedMethod = document.querySelector('input[name="paymentMethod"]:checked')?.value;
    
    if (!selectedMethod) {
      alert("Please select a payment method.");
      e.preventDefault();
      return;
    }
    
    // Show loading state
    submitBtn.disabled = true;
    if (selectedMethod === 'cod') {
      submitBtn.textContent = 'Processing...';
    } else {
      submitBtn.textContent = 'Redirecting to payment...';
    }
    
    // Let form submit to process-payment.php
    console.log('Submitting payment form to PHP for method:', selectedMethod);
  });
});

// Search functionality
document
  .querySelector(".search-input")
  .addEventListener("keypress", function (e) {
    if (e.key === "Enter") {
      e.preventDefault();
      const query = e.target.value.trim();
      if (query) {
        // Redirect to search page or handle search
        window.location.href = `./search.php?q=${encodeURIComponent(query)}`;
      }
    }
  });

document.querySelector(".search-btn").addEventListener("click", function (e) {
  e.preventDefault();
  const query = document.querySelector(".search-input").value.trim();
  if (query) {
    // Redirect to search page or handle search
    window.location.href = `./search.php?q=${encodeURIComponent(query)}`;
  }
});
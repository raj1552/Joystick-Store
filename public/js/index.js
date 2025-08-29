const checkoutBtn = document.getElementById("checkoutBtn");
const popupOverlay = document.getElementById("popupOverlay");
const confirmOverlay = document.getElementById("confirmOverlay")
const closeBtn = document.getElementById("closeBtn"); // Close button for checkout popup
const cancelBtn = document.getElementById("cancelBtn");
const confirmBtn = document.getElementById("confirmBtn");
const paymentOverlay = document.getElementById("paymentOverlay");
const paymentCloseBtn = document.getElementById("paymentCloseBtn"); // Close button for payment popup
const paymentCancelBtn = document.getElementById("paymentCancelBtn");
const paymentConfirmBtn = document.getElementById("paymentConfirmBtn")

// Show popup when checkout button is clicked
checkoutBtn.addEventListener("click", () => {
  popupOverlay.classList.add("show");
});

// Confirm button action - show payment popup and close checkout popup
confirmBtn.addEventListener("click", () => {
  popupOverlay.classList.remove("show");
  paymentOverlay.classList.add("show");
});

paymentConfirmBtn.addEventListener("click", () => {
  paymentOverlay.classList.remove("show")
  confirmOverlay.classList.add("show")
})

// Close popup functions
function closePopup() {
  popupOverlay.classList.remove("show");
}

function closePayment() {
  paymentOverlay.classList.remove("show");
}

// Event listeners for checkout popup
closeBtn.addEventListener("click", closePopup);
cancelBtn.addEventListener("click", closePopup);

// Event listeners for payment popup
paymentCloseBtn.addEventListener("click", closePayment);
paymentCancelBtn.addEventListener("click", closePayment);

// Close popup when clicking outside
popupOverlay.addEventListener("click", (e) => {
  if (e.target === popupOverlay) {
    closePopup();
  }
});

paymentOverlay.addEventListener("click", (e) => {
  if (e.target === paymentOverlay) {
    closePayment(); // Fixed: was calling closePopup() instead of closePayment()
  }
});

// Close popup with Escape key
document.addEventListener("keydown", (e) => {
  if (e.key === "Escape") {
    if (paymentOverlay.classList.contains("show")) {
      closePayment();
    } else if (popupOverlay.classList.contains("show")) {
      closePopup();
    }
  }
});

//Cart Price update on increase and decrease
let quantity = 1;
const basePrice = 30000;

const quantityDisplay = document.getElementById("quantity");
const priceDisplay = document.getElementById("price");
const decreaseBtn = document.getElementById("decreaseBtn");
const increaseBtn = document.getElementById("increaseBtn");

function updateDisplay() {
  quantityDisplay.textContent = quantity;
  const totalPrice = basePrice * quantity;
  priceDisplay.textContent = `NRs. ${totalPrice.toLocaleString()}`;
  decreaseBtn.disabled = quantity <= 1;
}

decreaseBtn.addEventListener("click", () => {
  if (quantity > 1) {
    quantity--;
    updateDisplay();
  }
});

increaseBtn.addEventListener("click", () => {
  quantity++;
  updateDisplay();
});

updateDisplay();

function showPage(pageId) {
  // Hide all pages
  const pages = document.querySelectorAll(".page");
  pages.forEach((page) => page.classList.remove("active"));

  // Show selected page
  document.getElementById(pageId).classList.add("active");

  // Update nav items
  const navItems = document.querySelectorAll(".nav-item");
  navItems.forEach((item) => item.classList.remove("active"));
  event.target.closest(".nav-item").classList.add("active");

  // Update page title
  const titles = {
    dashboard: "Dashboard",
    customer: "Customer Management",
    admin: "Admin Management",
    products: "Product Management",
    orders: "Order Management",
    reports: "Report Management",
    logout: "logout",
  };
  document.getElementById("page-title").textContent = titles[pageId];
}

function showAddProduct() {
  document.getElementById("add-product-form").style.display = "block";
}

function hideAddProduct() {
  document.getElementById("add-product-form").style.display = "none";
}

// Add some interactive functionality
document.addEventListener("DOMContentLoaded", function () {
  // Animate stat cards on load
  const statCards = document.querySelectorAll(".stat-card");
  statCards.forEach((card, index) => {
    setTimeout(() => {
      card.style.opacity = "0";
      card.style.transform = "translateY(20px)";
      card.style.transition = "all 0.5s ease";
      setTimeout(() => {
        card.style.opacity = "1";
        card.style.transform = "translateY(0)";
      }, 100);
    }, index * 100);
  });

  // Add hover effects to table rows
  const tableRows = document.querySelectorAll("tbody tr");
  tableRows.forEach((row) => {
    row.addEventListener("mouseenter", function () {
      this.style.transform = "scale(1.01)";
      this.style.transition = "transform 0.2s ease";
    });
    row.addEventListener("mouseleave", function () {
      this.style.transform = "scale(1)";
    });
  });
});

// const qtyInput = document.getElementById("quantity");
// const amountInput = document.getElementById("tamount");
// const price = parseFloat(document.getElementById("price").value);

// qtyInput.addEventListener("input", () => {
//   let qty = parseInt(qtyInput.value) || 0;
//   amountInput.value = qty * price; // recalculates instantly
// });

// // chart.js - Separate JavaScript file
// document.addEventListener('DOMContentLoaded', function() {
//     const canvas = document.getElementById('salesChart');
    
//     if (canvas) {
//         const ctx = canvas.getContext('2d');
        
//         // Get data from data attributes
//         const chartLabels = JSON.parse(canvas.dataset.labels || '[]');
//         const chartSales = JSON.parse(canvas.dataset.sales || '[]');
        
//         const salesChart = new Chart(ctx, {
//             type: 'line',
//             data: {
//                 labels: chartLabels,
//                 datasets: [{
//                     label: 'Sales Amount (Rs.)',
//                     data: chartSales,
//                     backgroundColor: 'rgba(54, 162, 235, 0.2)',
//                     borderColor: 'rgba(54, 162, 235, 1)',
//                     borderWidth: 2,
//                     fill: true,
//                     tension: 0.4
//                 }]
//             },
//             options: {
//                 responsive: true,
//                 maintainAspectRatio: false,
//                 scales: {
//                     y: {
//                         beginAtZero: true,
//                         ticks: {
//                             callback: function(value) {
//                                 return 'Rs. ' + value.toLocaleString();
//                             }
//                         }
//                     }
//                 },
//                 plugins: {
//                     legend: {
//                         display: true,
//                         position: 'top'
//                     },
//                     tooltip: {
//                         callbacks: {
//                             label: function(context) {
//                                 return 'Sales: Rs. ' + context.parsed.y.toLocaleString();
//                             }
//                         }
//                     }
//                 }
//             }
//         });
//     }
// });

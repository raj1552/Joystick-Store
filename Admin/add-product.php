<?php
include "../config/connection.php";
include "./header.php";
?>

<div id="add-product-form">
    <div class="form-container">
        <h3 style="margin-bottom: 20px; color: #2c3e50;">Add New Product</h3>
        <form action="./insert-product.php" method="POST" enctype="multipart/form-data" novalidate>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Product Name</label>
                    <input type="text" name="product_name" class="form-input" placeholder="Enter product name" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Price (Rs.)</label>
                    <input type="number" name="price" class="form-input" placeholder="Enter price" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Stock Quantity</label>
                    <input type="number" name="stock" class="form-input" placeholder="Enter stock quantity" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-input" rows="4" placeholder="Enter product description" required></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Color & Images</label><br>
                    <label>
                        <input type="checkbox" class="color-checkbox" data-color="Black" name="colors[]" value="Black"> Black
                    </label>
                    <input type="file" name="image_Black" class="color-image" data-color="Black" accept="image/*" disabled><br>

                    <label>
                        <input type="checkbox" class="color-checkbox" data-color="White" name="colors[]" value="White"> White
                    </label>
                    <input type="file" name="image_White" class="color-image" data-color="White" accept="image/*" disabled><br>

                    <label>
                        <input type="checkbox" class="color-checkbox" data-color="Blue" name="colors[]" value="Blue"> Blue
                    </label>
                    <input type="file" name="image_Blue" class="color-image" data-color="Blue" accept="image/*" disabled><br>

                    <label>
                        <input type="checkbox" class="color-checkbox" data-color="Orange" name="colors[]" value="Orange"> Orange
                    </label>
                    <input type="file" name="image_Orange" class="color-image" data-color="Orange" accept="image/*" disabled>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Compatibility</label>
                    <label><input type="checkbox" name="details[Compatibility][]" value="PC"> PC</label>
                    <label><input type="checkbox" name="details[Compatibility][]" value="PlayStation"> PlayStation</label>
                    <label><input type="checkbox" name="details[Compatibility][]" value="Xbox"> Xbox</label>
                </div>
            </div>
            <div style="text-align: right;">
                <a href="./index.php" class="btn" style="background: #95a5a6; color: white; margin-right: 10px;">Cancel</a>
                <button type="submit" name="submit" class="btn btn-primary">Add Product</button>
            </div>
        </form>
    </div>
</div>

<script>
const colorCheckboxes = document.querySelectorAll('.color-checkbox');
const colorImages = document.querySelectorAll('.color-image');

colorCheckboxes.forEach(cb => {
    cb.addEventListener('change', () => {
        const color = cb.dataset.color;
        const imageInput = document.querySelector(`.color-image[data-color="${color}"]`);
        imageInput.disabled = !cb.checked;
        if (!cb.checked) imageInput.value = '';
    });
});

document.querySelector('form').addEventListener('submit', function(e) {
    let valid = true;
    colorCheckboxes.forEach(cb => {
        const color = cb.dataset.color;
        const imageInput = document.querySelector(`.color-image[data-color="${color}"]`);
        if (cb.checked && imageInput.files.length === 0) {
            alert(`Please upload an image for ${color} color.`);
            valid = false;
        }
    });

    if (!valid) e.preventDefault();
});
</script>
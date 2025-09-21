<?php
include "../config/connection.php";
include "./header.php" ?>

<!-- Add Customer Form -->
<div id="add-product-form">
    <div class="form-container">
        <h3 style="margin-bottom: 20px; color: #2c3e50;">Add Admin</h3>
        <form action="./insert-admin.php" method="POST" novalidate>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Admin Name</label>
                    <input type="text" class="form-input" id="fname" name="fullname" placeholder="Enter Admin Name"
                        value="">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-input" id="email" name="email" placeholder="Enter Email" value="">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="number" class="form-input" id="phone" name="phone" placeholder="Enter Phone" value="">
                </div>
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <input type="text" class="form-input" id="addr" name="address" placeholder="Enter Address" value="">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-input" id="password" name="password" placeholder="Enter Password"
                        value="">
                </div>
            </div>
            <div style="text-align: right;">
                <a href="./index.php" class="btn"
                    style="background: #95a5a6; color: white; margin-right: 10px;">Cancel</a>
                <button type="submit" name="submit" class="btn btn-primary">Add Now</button>
            </div>
        </form>
    </div>
</div>
</div>
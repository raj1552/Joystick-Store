<?php 
$total_amount = isset($_GET['total']) ? $_GET['total'] : 1000; // fallback if not passed
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khalti API Integration</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://khalti.s3.ap-south-1.amazonaws.com/KPG/dist/2020.12.17.0.0.0/khalti-checkout.iffe.js"></script>

    <link href="./app.css" rel="stylesheet" type="text/css">
</head>
<body>
    <div class="apibox">
        <img src="./api.png" alt="">
        <div class="apibox__detail">
            <h2 class="apibox__title">Purchase a Product</h2>
            <div clas="meta-box">
                <span class="meta-box__item">
                    Rs. <strong><?php echo $total_amount ?></strong>
                </span>
            </div>
            <div class="text-box">
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Recusandae, praesentium.</p>
            </div>
            <!-- Place this where you need payment button -->
            <button id="payment-button" class="button">Pay with Khalti</button>
            <!-- Place this where you need payment button -->
            <!-- Paste this code anywhere in you body tag -->
        </div>
    </div>
    <script>
        var totalAmount = <?php echo $total_amount * 100 ?>;
        var config = {
            // replace the publicKey with yours
            "publicKey": "test_public_key_183a8a3f929342d689aec307bda80e82",
            "productIdentity": "123456780",
            "productName": "Drago",
            "productUrl": "http://gameofthrones.wikia.com/wiki/Dragons",
            "paymentPreference": [
                "KHALTI",
                "EBANKING",
                "MOBILE_BANKING",
                "CONNECT_IPS",
                "SCT",
                ],
            "eventHandler": {
                onSuccess (payload) {
                    // hit merchant api for initiating verfication
                    console.log(payload);
                },
                onError (error) {
                    console.log(error);
                },
                onClose () {
                    console.log('widget is closing');
                }
            }
        };

        var checkout = new KhaltiCheckout(config);
        var btn = document.getElementById("payment-button");
        btn.onclick = function () {
            // minimum transaction amount must be 10, i.e 1000 in paisa.
            checkout.show({amount: totalAmount});
        }
    </script>
    <!-- Paste this code anywhere in you body tag -->
</body>
</html>
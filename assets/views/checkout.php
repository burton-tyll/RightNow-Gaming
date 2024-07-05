<?php

require_once '../../vendor/autoload.php';
require_once '../../vendor/secrets.php';

require_once('../Class/StripePayment.php');

use App\StripePayment;


if(isset($_POST['cart'])){
    $cart_encoded = $_POST['cart'];
    $cart_json = base64_decode($cart_encoded);
    $cart = json_decode($cart_json, true);
}

$YOUR_DOMAIN = 'http://localhost:4242';

$payment = new StripePayment(STRIPE_SECRET);

try {
    $checkoutSessionUrl = $payment->createCheckoutSession($cart, $YOUR_DOMAIN);
    header("HTTP/1.1 303 See Other");
    header("Location: " . $checkoutSessionUrl);
    exit();
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Buy cool new product</title>
    <link rel="stylesheet" href="style.css">
    <!-- <script src="https://js.stripe.com/v3/"></script> -->
</head>
<body>
    <section>
        <?php foreach($cart as $thisone): ?>
        <div class="product">
            <img src="https://i.imgur.com/EHyR2nP.png" alt="The cover of The Last of Us" />
            <div class="description">
                <h3><?php echo $thisone['name']; ?></h3>
                <h5><?php echo $thisone['price'] ?></h5>
            </div>
        </div>
        <?php endforeach ?>
        <form action="/checkout.php" method="POST">
            <button type="submit" id="checkout-button">Checkout</button>
        </form>
    </section>
</body>
</html>

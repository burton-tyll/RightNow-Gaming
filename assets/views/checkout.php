<?php
// Démarrer la session en premier
session_start();

// Inclusion des fichiers nécessaires
require_once '../../vendor/autoload.php';
require_once '../../vendor/secrets.php';
require_once '../Class/User.php';
require_once '../Class/StripePayment.php';

use App\StripePayment;

$user = new User();

// Vérifiez que l'utilisateur est connecté et que l'ID de l'utilisateur est disponible dans la session
if (isset($_SESSION['user_id'])) {
    $userid = $_SESSION['user_id'];
} else {
    die('Utilisateur non connecté.');
}

// Vérifiez que les variables POST sont définies avant de les utiliser
if (isset($_POST['address'], $_POST['cp'], $_POST['city'])) {
    $address = $_POST['address'];
    $cp = $_POST['cp'];
    $city = $_POST['city'];
    $newAddress = ''.$address.', '.$cp.', '.$city.'';

    // Mettre à jour l'adresse de l'utilisateur
    $user->updateAddress($userid, $newAddress);
} else {
    die('Informations d\'adresse manquantes.');
}

// Test de débogage
var_dump('test');

if (isset($_POST['cart'])) {
    $cart_encoded = $_POST['cart'];
    $cart_json = base64_decode($cart_encoded);
    $cart = json_decode($cart_json, true);
    $_SESSION['finalcart'] = $cart_encoded;
} else {
    die('Panier manquant.');
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
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Buy cool new product</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
    <section>
        <?php if (isset($cart)) : ?>
            <?php foreach ($cart as $thisone) : ?>
                <div class="product">
                    <img src="https://i.imgur.com/EHyR2nP.png" alt="The cover of The Last of Us" />
                    <div class="description">
                        <h3><?php echo htmlspecialchars($thisone['name']); ?></h3>
                        <h5><?php echo htmlspecialchars($thisone['price']); ?></h5>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <div class="client-address">
            <h2>Adresse de facturation</h2>
            <p><?php echo htmlspecialchars($firstname . ' ' . $name); ?></p>
        </div>
        <form action="/checkout.php" method="POST">
            <button type="submit" id="checkout-button">Checkout</button>
        </form>
    </section>
</body>
</html>

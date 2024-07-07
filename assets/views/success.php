<?php

session_start();

$cart = null;
$total_price = null;

if(isset($_SESSION['finalcart'])){
    $cart_encoded = $_SESSION['finalcart'];
    $cart_json = base64_decode($cart_encoded);
    $cart = json_decode($cart_json, true);
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include('../templates/global.php')?>
    <link rel="stylesheet" href="../styles/cart.css">
    <link rel="stylesheet" href="../styles/payment.css">
    <link rel="stylesheet" href="../styles/success.css">
    <title>RightnowGaming</title>
</head>
<body>
    <header id="header-cart">
        <div>
            <a href="../../index.php"><img src="../img/logo.png" alt="logo" id="img-logo"></a>
        </div>
        <div id="passive-number">
            <span>1</span>
        </div>
        <p id="passive-step">Panier</p>
        <span id="passive-line"></span>
        <div id="passive-number">
            <span>2</span>
        </div>
        <p id="passive-step">Paiement</p>
        <span id="active-line"></span>
        <div id="active-number">
            <span>3</span>
        </div>
        <p id="active-step">Activation du jeu</p>
    </header>
    <main>
        <div class="container">
            <div class="content">
                <h1>Félicitations pour votre achat !</h1>
                <p style="color: green;">Le code du jeu vous a été envoyé par email.</p>
                <div class="order-summary">
                    <h2>Rappel de votre commande</h2>
                    <?php foreach($cart as $item): ?>
                        <div class="item">
                            <h2><?php echo $item['name'] ?></h2>
                            <p><?php echo $item['price'].'€' ?></p>
                        </div>
                        <?php 
                        $total_price += $item['price'];
                        ?>
                    <?php endforeach ?>
                    <h2>TOTAL</h2>
                    <h2 class="total-p"><?php echo $total_price . '€' ?></h2>
                </div>
                <a href="../../index.php" class="back-link">Revenir à l'accueil</a>
            </div>
        </div>
    </main>
</body>
</html>
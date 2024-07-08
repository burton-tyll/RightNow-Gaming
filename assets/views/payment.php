<?php

    require_once('../Class/Game.php');
    require_once('../Class/Game_platform.php');

    $game = new Game();
    $gamePlatform = new Game_platform;

    $gameIds = null;
    $totalPrice = null;

    if(isset($_GET['game'])){
        $gameIds = $_GET['game'];
    }

    // Convertis les IDs en entiers si nécessaire
    $gameIds = array_map('intval', $gameIds);

    $cart = [];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include ('../templates/global.php') ?>
    <link rel="stylesheet" href="../styles/cart.css">
    <link rel="stylesheet" href="../styles/payment.css">
    <script src="../script/payment.js" defer></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="https://js.stripe.com/v3/" defer></script>
    <script>

    </script>
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
        <span id="active-line"></span>
        <div id="active-number">
            <span>2</span>
        </div>
        <p id="active-step">Paiement</p>
        <span id="passive-line"></span>
        <div id="passive-number">
            <span>3</span>
        </div>
        <p id="passive-step">Activation du jeu</p>
    </header>
    <main>
        <form action="checkout.php" method="POST">
            <div class="infos">
                <section id="billing">
                    <h1>Adresse de facturation</h1>
                    <div>
                        <input type="text" name="name" placeholder="Nom">
                        <input type="text" name="firstname" placeholder="Prénom">
                        <input type="text" name="address" placeholder="Adresse" required>
                        <input type="text" name="cp" placeholder="Code Postal" required>
                        <input type="text" name="city" placeholder="Ville" required>
                </div>
                </section>
                <section id="resume">
                    <h1>Résumé</h1>
                    <div class="order">
                        <?php foreach ($gameIds as $thisone): ?>
            
                            <?php
                                $orderedGame = $game->getGameById($thisone);
                                $platform = $gamePlatform->getGamePlateform($thisone);
                            ?>
                            <?php if($orderedGame['special_offer'] !== null){
                                $price = $orderedGame['price'] - $orderedGame['price'] * ($orderedGame['special_offer'] / 100);
                                $price = round($price, 2);
                                $totalPrice += $price;
                            }else{
                                $price = $orderedGame['price'];
                                $totalPrice += $price;
                            }
                            ?>
                            <?php
                                //On ajoute chaque jeu au panier de commande
                                $item = ['name' =>$orderedGame['name'], 'price' =>$price];
                                array_push($cart, $item);
                                $cart_json = json_encode($cart);
                                $cart_encoded = base64_encode($cart_json);
                            ?>
                            <div class="game">
                                <div class="first-line">
                                    <h2><?php echo $orderedGame['name']?></h2>
                                    <p><?php echo $price . '€' ?></p>
                                </div>
                                <div class="second-line">
                                    <p>PLATEFORME</p>
                                </div>
                            </div>
                        <?php endforeach ?>
                        <div class="submit-payment">
                            <div class="total">
                                <h3>TOTAL</h3>
                                <h3><?php echo $totalPrice . '€'; ?></h3>
                            </div>
                                <input type="hidden" name="cart" value="<?php echo $cart_encoded ?>">
                                <button class="pay" type="submit">Payer</button>
                            <p>En cliquant sur "Payer" je reconnais avoir lu et accepté les termes et conditions, et la politique de confidentialité.</p>
                        </div>
                    </div>
                </section>
            </div>
        </form>
    </main>
</body>
</html>
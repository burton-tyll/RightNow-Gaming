<?php
require_once('../Class/Game.php');
require_once('../Class/Game_platform.php');
require_once('../Class/Genre.php');

$game = new Game();
$game_platforms = new Game_platform();
$game_genres = new Genre();

session_start();

$cartItems = [];
$totalPrice = 0; // Prix total sans réduction
$totalDiscountedPrice = 0; // Prix total avec réduction
$totalSavings = 0; // Économies totales

if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $gameId => $data) {
        $gameDetails = $game->getGameById($gameId);
        if ($gameDetails) {
            $gamePlatforms = $game_platforms->getGamePlateform($gameId);
            foreach ($data['platforms'] as $platformId => $quantity) {
                $platformName = isset($gamePlatforms[$platformId]) ? $gamePlatforms[$platformId] : 'Plateforme inconnue';
                
                // Calcul du prix avec la réduction en pourcentage
                $price = $gameDetails['price'];
                $specialOffer = $gameDetails['special_offer'];
                $discountedPrice = $price;
                if ($specialOffer && $specialOffer > 0) {
                    // Calculer le prix après réduction
                    $discountedPrice = $price * (1 - ($specialOffer / 100));
                }

                $cartItems[] = [
                    'id' => $gameId,
                    'name' => $gameDetails['name'],
                    'price' => $price,
                    'discountedPrice' => $discountedPrice,
                    'image' => $gameDetails['image'],
                    'platform' => $platformName,
                    'quantity' => $quantity,
                    'special_offer' => $specialOffer
                ];

                // Calculer les montants totaux
                $totalPrice += $price * $quantity;
                $totalDiscountedPrice += $discountedPrice * $quantity;
                $totalSavings += ($price - $discountedPrice) * $quantity;
            }
        }
    }
}

function convertBlobToBase64($blob) {
    return 'data:image/jpeg;base64,' . base64_encode($blob);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/cart.css">
    <?php include('../templates/global.php'); ?>
    <link rel="icon" type="image/x-icon" href="../img/favicon.png">
    <title>RightNow Gaming - Mon Panier</title>
</head>
<body>
    <header id="header-cart">
        <div>
            <img src="../img/logo.png" alt="logo" id="img-logo">
        </div>
        <div id="active-number">
            <span>1</span>
        </div>
        <p id="active-step">Panier</p>
        <span id="passive-line"></span>
        <div id="passive-number">
            <span>2</span>
        </div>
        <p id="passive-step">Paiement</p>
        <span id="passive-line"></span>
        <div id="passive-number">
            <span>3</span>
        </div>
        <p id="passive-step">Activation du jeu</p>
    </header>
    <main>
        <section id="cart-section">
            <div>
                <h2>Mon Panier:</h2>
                <!-- Vérifier si le panier est vide -->
                <?php if (empty($cartItems)) :?>
                    <h1>Le panier est vide!</h1>
                    <a href="../../index.php">Retourner aux achats</a>
                <?php else: ?>
                    <ul id="cart-items-list">
                        <?php foreach ($cartItems as $item): ?>
                            <li class="cart-item">
                                <div class="item-details">
                                    <div>
                                        <img src="<?php echo convertBlobToBase64($item['image']); ?>" alt="Image du jeu" class="item-image">
                                    </div>
                                    <div class="item-name-price-platform-name-quantity">
                                        <p class="item-name"><strong><?php echo htmlspecialchars($item['name']); ?></strong></p>
                                        <div id="div-special-offer-price">
                                            <?php if ($item['special_offer'] && $item['special_offer'] > 0): ?>
                                                <div>
                                                <p id="price-before-special-offer"><?php echo number_format($item['price'], 2, ',', ' ') . ' €'; ?></p>
                                                <p id="special-offer"><?php echo '-' . htmlspecialchars($item['special_offer']) . '%'; ?></p>
                                                </div>
                                            <?php endif; ?>
                                            <p id="discounted-price"><?php echo number_format($item['discountedPrice'], 2, ',', ' ') . ' €'; ?></p>
                                        </div>
                                        <p class="item-platform-name"><strong><?php echo htmlspecialchars($item['platform']); ?></strong></p>
                                        <p class="item-quantity">Quantité: <?php echo htmlspecialchars($item['quantity']); ?></p>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div id="cart-summary">
                    <h2>Résumé:</h2>
                    <div id="cart-summary-prices">
                        <div id="price-ammount">
                        <div id="price-titles">
                        <p class="total-price">Prix de base: </p>
                        <p class="total-savings">Économies totales:</p>
                        <p id="discounted-price-final">Total: </p>
                        </div>
                        <div id="prices">
                        <p class="total-price"><?php echo number_format($totalPrice, 2, ',', ' ') . ' €'; ?></p>
                        <p class="total-savings"><?php echo number_format($totalSavings, 2, ',', ' ') . ' €'; ?></p>
                        <p><?php echo number_format($totalDiscountedPrice, 2, ',', ' ') . ' €'; ?></p>
                        </div>
                        </div>
                        <div id="go-back-paiement-links">
                        <a href="checkout.php" id="checkout">Passer à la caisse</a>
                        <p>ou</p>
                        <a href="../../index.php" id="continue-shopping">Continuer vos achats</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>

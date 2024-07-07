<?php
require_once('../Class/Game.php');
require_once('../Class/Game_platform.php');
require_once('../Class/Genre.php');

$game = new Game();
$game_platforms = new Game_platform();
$game_genres = new Genre();

session_start();
$cartItems = [];


if (isset($_SESSION['cart'])){
    foreach ($_SESSION['cart'] as $gameId => $quantity) {
        $gameDetails = $game->getGameById($gameId);
        if ($gameDetails) {
            $gamePlatforms = $game_platforms->getGamePlateform($gameId);
            $gameGenres = $game_genres->getGameGenre($gameId);
    
            $cartItems[] = [
                'id' => $gameId,
                'name' => $gameDetails['name'],
                'price' => $gameDetails['price'],
                'image' => $gameDetails['image'],
                'platforms' => $gamePlatforms,
                'quantity' => $quantity
            ];
        }
    }
}

// Construis la chaîne de requête
$queryString = '';
foreach ($cartItems as $item) {
    $queryString .= 'game[]=' . $item['id'] . '&';
}
// Supprime le dernier '&'
$queryString = rtrim($queryString, '&');

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
            <h1>Mon Panier</h1>
            
            <!-- Vérifier si le panier est vide -->
            <?php if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) :?>
                <h1>Le panier est vide!</h1>
                <a href="../../index.php">Retourner aux achats</a>
            <?php elseif ($cartItems): ?>
                <ul id="cart-items-list">
                    <?php foreach ($cartItems as $item): ?>
                        <li class="cart-item">
                            <img src="<?php echo convertBlobToBase64($item['image']); ?>" alt="Image du jeu" class="item-image">
                            <div class="item-details">
                                <h2 class="item-name"><?php echo htmlspecialchars($item['name']); ?></h2>
                                <p class="item-price"><?php echo htmlspecialchars($item['price']) . ' €'; ?></p>
                                <p class="item-quantity">Quantité: <?php echo htmlspecialchars($item['quantity']); ?></p>
                                <p class="item-platforms">Plateformes: <?php echo implode(', ', array_map('htmlspecialchars', $item['platforms'])); ?></p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div id="cart-summary">
                    <?php
                    $totalPrice = array_reduce($cartItems, function ($sum, $item) {
                        return $sum + ($item['price'] * $item['quantity']);
                    }, 0);
                    ?>
                    <p id="total-price">Prix total: <?php echo number_format($totalPrice, 2, ',', ' ') . ' €'; ?></p>
                </div>

                <a href="../../index.php" id="continue-shopping">Continuer vos achats</a>
                <a href="payment.php?<?php echo htmlspecialchars($queryString) ?>" id="checkout">Passer à la caisse</a>
            <?php else: ?>
                <p>Votre panier est vide.</p>
                <a href="../../index.php" id="continue-shopping">Continuer vos achats</a>
            <?php endif; ?>

        </section>
    </main>
</body>
</html>

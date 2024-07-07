<?php
    require_once('./assets/Class/Game.php');
    require_once('./assets/Class/Game_platform.php');

    $game = new Game();
    $gamePlatform = new Game_platform();


    session_start();

    $newGames = $game->getGamesOrderedByDate();
    $bestSellers = $game->getGamesOrderedByRate();

    $theBestGame = $game->getTheBestGame();


    function convertBlobToBase64($blob) {
        return 'data:image/jpeg;base64,' . base64_encode($blob);
    }

    //HEADER

    function getStatus() {
        if (!isset($_SESSION['user'])) {
            return 'Connexion';
        } else {
            return 'Déconnexion';
        }
    }
    
    function disconnect() {
        session_destroy();
        header("Location: ./assets/views/connexion.php"); // Redirection après déconnexion
        exit();
    }
    
    if (isset($_GET['action']) && $_GET['action'] == 'logout') {
        disconnect();
    } 
    $status = getStatus();

    //GESTIONNAIRE D'ADMINISTRATION

    function getRole(){
        if (isset($_SESSION['admin'])){
            return 'Admin';
        }else{
            return 'User';
        }
    }

    $role = getRole();

    // Calculer le nombre total d'articles dans le panier
    function getCartCount() {
        return isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
    }

    $totalItems = getCartCount();
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-------GLOBAL ASSETS------>
    <link rel="stylesheet" href="./assets/styles/global.css">
    <link rel="icon" type="image/x-icon" href="./assets/img/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css">
    <script src="./assets/script/global.js" defer></script>
    <!-------------------------->
    <link rel="stylesheet" href="./assets/styles/index.css">
    <script src='./assets/script/promo.js' defer></script>
    <title>RightNow Gaming</title>
</head>
<body>
    <header>
        <nav>
            <a href="#" class="logo"><img src="./assets/img/logo.png" alt="logo"></a>
            <ul>
                <li><a href="./assets/views/pc.php"><img src="./assets/img/pc.png" alt="pc_logo"><p>PC</p></a></li>
                <li><a href="./assets/views/playstation.php"><img src="./assets/img/playstation.png" alt="playstation_logo"><p>Playstation</p></a></li>
                <li><a href="./assets/views/xbox.php"><img src="./assets/img/xbox.png" alt="xbox_logo"><p>Xbox</p></a></li>
                <li><a href="./assets/views/nintendo.php"><img src="./assets/img/nintendo.png" alt="nintendo_logo"><p>Nintendo</p></a></li>
            </ul>
            <div class="user-buttons">
                <a href="./assets/views/cart.php" id="icon-cart">
                    <img src="./assets/img/icon-cart.svg" alt="img cart"></img>
                    <p id="total-items"><?php echo $totalItems; ?></p>
                </a>
                <img src="./assets/img/user.png" alt="userImage" class="userButton">
            </div>
            <ul class="profil-dropdown">
            <li><a href="./assets/views/profil.php">Profil</a></li>
            <?php if ($role == 'Admin'): ?>
                <li><a href="./assets/views/paneladmin.php">Panel admin</a></li>
            <?php endif ?>
            <li><a href="">Mes achats</a></li>
            <li>
                <?php if ($status == 'Connexion'): ?>
                    <a href="./assets/views/connexion.php"><?php echo $status; ?></a>
                <?php else: ?>
                    <a href="?action=logout"><?php echo $status; ?></a>
                <?php endif; ?>
            </li>
        </ul>
        </nav>
    </header>
    <!--Accueil-->
    <main>
        <section id="accueil" style="background-image: url('<?php echo convertBlobToBase64($theBestGame[0]['image']); ?>'); background-size: cover">

        </section>
        <section id="nouveautés">
            <div class="section-title">
                <h1>Nouveautés</h1><a class="showmore" href="./assets/views/newGames.php?games=all"><img src="./assets/img/arrow_down.png" alt="flèche bas"></a>
            </div>
            <div class="games-grid">
            <?php $count = 0;
            foreach($newGames as $game): 
                if($count >= 6) { break; }?>
                <div class="games-grid-item">
                    <a href="assets/views/game-details.php?id=<?php echo $game['id']; ?>">
                        <div class="resizeContainer"><img src="<?php echo convertBlobToBase64($game['image']); ?>" alt="gameImage" class="games-grid-item-img"></div>
                    </a>
                    <div class="games-grid-item-infos">
                        <p><?php echo $game['name']; ?></p>
                        <?php if($game['special_offer'] != 0): ?>
                            <?php 
                            $price = $game['price'] - ($game['price'] * ($game['special_offer'] / 100));
                            $price = round($price, 2);
                            ?>
                            <div class="promo">-<?php echo $game['special_offer']; ?>%</div>
                        <?php endif; ?>
                        <?php if($game['price'] == 0.1): ?>
                            <p></p>
                        <?php else: ?>
                            <p class="prices"><?php echo isset($price) ? $price . '€' : $game['price'] . '€'; ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php 
            $count++;
            endforeach; ?>
            </div>
        </section>
        <section id="meilleures-ventes">
            <div class="section-title">
                <h1>Les mieux notés</h1><a class="showmore" href="./assets/views/best-ratedgames.php?games=all"><img src="./assets/img/arrow_down.png" alt="flèche bas"></a>
            </div>
            <div class="games-grid">
            <?php
                $count = 0;
                foreach($bestSellers as $game):
                    if($count >= 3) { break; }
                    $class = $count == 0 ? 'img-large' : '';
                ?>
                <div class="<?php echo 'games-grid-item ' . $class; ?>">
                        <div class="resizeContainer">
                        <a href="assets/views/game-details.php?id=<?php echo $game['id']; ?>&price=<?php echo isset($price) ? $price : $game['price']; ?>">
                            <img src="<?php echo convertBlobToBase64($game['image']); ?>" alt="gameImage" class="games-grid-item-img"></div>
                        </a>
                    <div class="games-grid-item-infos">
                        <p><?php echo $game['name']; ?></p>
                        <?php 
                        if($game['special_offer'] != 0) {
                            $price = $game['price'] - $game['price'] * ($game['special_offer'] / 100);
                            $price = round($price, 2);
                            echo '<div class="promo">-' . $game['special_offer'] . '%</div>';
                        }
                        ?>
                        <?php if($game['price'] == 0.1): ?>
                            <p></p>
                        <?php else: ?>
                            <p class="prices"><?php echo isset($price) ? $price . '€' : $game['price'] . '€'; ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php 
                $count++;
                endforeach; ?>
            </div>
        </section>
    </main>
    <?php include('./assets/templates/footer.php') ?>
</body>
</html>

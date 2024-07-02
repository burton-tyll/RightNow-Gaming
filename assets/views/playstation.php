<?php
    // Inclusion de la classe Game_platform
    require_once('../Class/Game_platform.php');

    // Création d'une instance de Game_platform
    $game_platform = new Game_platform();

    // Démarrage de la session
    session_start();

    // Récupération des jeux par plateforme, triés par date et note
    $newGames = $game_platform->getAllGamesByPlatformOrderedByDate(2);
    $bestSellers = $game_platform->getAllGamesByPlatformOrderedByRate(2);
    $theBestGame = $game_platform->getTheBestGameByPlateform(2);

    // Fonction pour convertir un BLOB en base64
    function convertBlobToBase64($blob) {
        return 'data:image/jpeg;base64,' . base64_encode($blob);
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-------GLOBAL ASSETS------>
    <?php include('../templates/global.php') ?>
    <!-------------------------->
    <link rel="stylesheet" href="../styles/index.css">
    <title>RightNow Gaming</title>
</head>
<body>
    <!-- Inclusion de l'en-tête -->
    <?php include('../templates/header.php') ?>

    <!-- Accueil -->
    <main>
        <section id="accueil" style="background-image: url('<?php echo convertBlobToBase64($theBestGame[0]['image']); ?>'); background-size: cover">
        </section>

        <!-- Section Nouveautés -->
        <section id="nouveautés">
            <div class="section-title">
                <h1>Nouveautés</h1>
            </div>
            <div class="games-grid">
                <?php
                $count = 0;
                foreach($newGames as $game):
                    if($count >= 6) { break; } ?>
                    <div class="games-grid-item">
                        <div class="resizeContainer">
                            <a href="./game-details.php?id=<?php echo $game['id_game']; ?>&price=<?php echo ($game['price']); ?>">
                                <img src="<?php echo convertBlobToBase64($game['image']) ?>" alt="gameImage" class="games-grid-item-img">
                            </a>
                        </div>
                        <div class="games-grid-item-infos">
                            <p><?php echo $game['name'] ?></p>
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
                endforeach ?>
            </div>
        </section>

        <!-- Section Meilleures Ventes -->
        <section id="meilleures-ventes">
            <div class="section-title">
                <h1>Les mieux notés</h1>
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
                        <a href="./game-details.php?id=<?php echo $game['id_game']; ?>&price=<?php echo urlencode($game['price']); ?>">
                            <img src="<?php echo convertBlobToBase64($game['image']) ?>" alt="gameImage" class="games-grid-item-img">
                        </a>
                    </div>
                    <div class="games-grid-item-infos">
                        <p><?php echo $game['name'] ?></p>
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
                endforeach ?>
            </div>
        </section>
    </main>
    <?php include('../templates/footer.php') ?>
</body>
</html>

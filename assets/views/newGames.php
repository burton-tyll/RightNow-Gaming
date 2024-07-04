<?php

    require_once('../Class/Game.php');
    require_once('../Class/Game_platform.php');

    $game = new Game();
    $game_platform = new Game_platform();

    function getPlatform(){
        $currentPlatform = $_GET['games'];
        if($currentPlatform == 'pc'){
            return 1;
        }elseif($currentPlatform == 'playstation'){
            return 2;
        }elseif($currentPlatform == 'xbox'){
            return 3;
        }elseif($currentPlatform == 'nintendo'){
            return 4;
        }
    }

    $gameOptions = ['pc', 'playstation', 'xbox', 'nintendo'];
    $platform = getPlatform();

    $allNewGames = $game->getGamesOrderedByDate();
    $platformNewGames = $game_platform->getAllGamesByPlatformOrderedByDate($platform);

    function convertBlobToBase64($blob) {
        return 'data:image/jpeg;base64,' . base64_encode($blob);
    }

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include('../templates/global.php') ?>
    <link rel="stylesheet" href="../styles/newgames.css">
    <link rel="stylesheet" href="../styles/index.css">
    <title>Document</title>
</head>
<body>
    <?php include('../templates/header.php') ?>
    <main>
        <section id="nouveautés">
            <div class="games-grid">
                <!---SI L'UTILISATEUR VIENT DE LA PAGE INDEX--->
                <?php if($_GET['games'] == 'all'): ?>
                    <?php foreach($allNewGames as $game): ?>
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
                    <?php endforeach ?>
                <!--SI L'UTILISATEUR VIENT D'UNE PAGE DE PLATEFORME---->
                <?php elseif (isset($_GET['games']) && in_array($_GET['games'], $gameOptions)): ?>
                    <?php foreach($platformNewGames as $game): ?>
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
                    <?php endforeach ?>
                <?php endif ?>
            </div>
        </section>
    </main>
    <?php include('../templates/footer.php'); ?>
</body>
</html>
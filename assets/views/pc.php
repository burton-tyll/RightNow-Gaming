<?php
    require_once('../Class/Game_platform.php');

    $game_platform = new Game_platform();

    session_start();

    $newGames = $game_platform->getAllGamesByPlatformOrderedByDate(1);
    $bestSellers = $game_platform->getAllGamesByPlatformOrderedByRate(1);
    $theBestGame = $game_platform->getTheBestGameByPlateform(1);


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
    <?php include('../templates/header.php') ?>
    <!--Accueil-->
    <main>
        <section id="accueil" style="background-image: url('<?php echo convertBlobToBase64($theBestGame[0]['image']); ?>'); background-size: cover">

        </section>
        <section id="nouveautés">
            <div class="section-title">
                <h1>Nouveautés</h1>
            </div>
            <div class="games-grid">
                <?php
                    $count = 0;
                    foreach($newGames as $game):
                        if($count >= 6){break;}?>
                        <div class="games-grid-item">
                            <img src="<?php echo convertBlobToBase64($game['image']) ?>" alt="gameImage" class="games-grid-item-img">
                            <div class="games-grid-item-infos">
                                <p><?php echo$game['name']?></p>
                                <?php 
                                    if($game['special_offer'] != 0) {
                                        $price = $game['price'] - $game['price'] * ($game['special_offer'] / 100);
                                        echo '<div class="promo">-'. $game['special_offer'] .'%</div>';
                                    }
                                ?>
                                <p class="prices"><?php echo isset($price) ? $price .'€' : $game['price'].'€'; ?></p>
                            </div>
                        </div>
                    <?php endforeach ?>
                    <?php
                        $count ++;
                    ?>
        </section>
        <section id="meilleures-ventes">
            <div class="section-title">
                <h1>Les mieux notés</h1>
            </div>
            <div class="games-grid">
            <?php
                    $count = 0;
                    foreach($bestSellers as $game):
                        if($count >= 3){break;}
                        $class = $count == 0 ? 'img-large' : '';
                        ?>
                        <div class="<?php echo 'games-grid-item '.$class;?>">
                            <img src="<?php echo convertBlobToBase64($game['image']) ?>" alt="gameImage" class="games-grid-item-img">
                            <div class="games-grid-item-infos">
                                <p><?php echo$game['name']?></p>
                                <?php 
                                    if($game['special_offer'] != 0) {
                                        $price = $game['price'] - $game['price'] * ($game['special_offer'] / 100);
                                        echo '<div class="promo">-'. $game['special_offer'] .'%</div>';
                                    }
                                ?>
                                <p class="prices"><?php echo isset($price) ? $price .'€' : $game['price'].'€'; ?></p>
                            </div>
                        </div>
                    <?php
                        $count ++;
                    ?>
                    <?php endforeach ?>
                    
            </div>

        </section>
    </main>
</body>
</html>
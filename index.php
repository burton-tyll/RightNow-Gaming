<?php
    require_once('./assets/Class/Game.php');

    $game = new Game();

    session_start();

    $newGames = $game->getGamesOrderedByDate();
    $bestSellers = $game->getGamesOrderedByRate();

    $theBestGame = $game->getTheBestGame();

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
    <link rel="stylesheet" href="./assets/styles/global.css">
    <link rel="icon" type="image/x-icon" href="./assets/img/favicon.png">
    <script src="./assets/script/global.js" defer></script>
    <!-------------------------->
    <link rel="stylesheet" href="./assets/styles/index.css">
    <title>RightNow Gaming</title>
</head>
<body>
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
                    foreach($newGames as $game){
                        if($count >= 6){break;}
                        echo ('
                        <div class="games-grid-item">
                            <img id="randomImage" src="'.convertBlobToBase64($game['image']).'" alt="gameImage" class="games-grid-item-img">
                            <div class="games-grid-item-infos">
                                <p>'.$game['name'].'</p>
                                <p>'.$game['price'].'€</p>
                            </div>
                        </div>
                        ');
                        $count ++;
                    }
                ?>
        </section>
        <section id="meilleures-ventes">
            <div class="section-title">
                <h1>Les mieux notés</h1>
            </div>
            <div class="games-grid">
            <?php
                    $count = 0;
                    foreach($bestSellers as $game){
                        if($count >= 3){break;}
                        $class = $count == 0 ? 'img-large' : '';
                        echo ('
                        <div class="games-grid-item ' . $class . '">
                            <img id="randomImage" src="'.convertBlobToBase64($game['image']).'" alt="gameImage" class="games-grid-item-img">
                            <div class="games-grid-item-infos">
                                <p>'.$game['name'].'</p>
                                <p>'.$game['price'].'€</p>
                            </div>
                        </div>
                        ');
                        $count ++;
                    }
                ?>
            </div>
        </section>
    </main>
</body>
</html>
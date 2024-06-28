<?php
require_once('../Class/Game.php');
require_once('../Class/Game_platform.php');
require_once('../Class/Genre.php');

$game = new Game();
$game_platforms = new Game_platform();
$game_genres = new Genre();

session_start();

function convertBlobToBase64($blob) {
    return 'data:image/jpeg;base64,' . base64_encode($blob);
}

// Récupérer l'ID du jeu et le prix depuis l'URL
$gameId = isset($_GET['id']) ? intval($_GET['id']) : null;
$price = isset($_GET['price']) ? floatval($_GET['price']) : null;
if ($gameId === null) {
    // Rediriger ou afficher un message d'erreur si l'ID est manquant
    header("Location: ../index.php");
    exit();
}

// Récupérer les détails du jeu
$gameDetails = $game->getGameById($gameId);
if (!$gameDetails) {
    echo "Détails du jeu introuvables.";
    exit();
}

$gamePlatforms = $game_platforms->getGamePlateform($gameId);

$gameGenres = $game_genres->getGameGenre($gameId);

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
    header("Location: ../views/connexion.php"); // Redirection après déconnexion
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
    } else {
        return 'User';
    }
}

$role = getRole();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-------GLOBAL ASSETS------>
    <link rel="stylesheet" href="../styles/game-details.css">
    <?php include('../templates/global.php')?>
    <link rel="icon" type="image/x-icon" href="../img/favicon.png">
    <script src="../script/comments.js"></script>
    <script src="../script/global.js" defer></script>
    <!-------------------------->
    <title>RightNow Gaming - Détails du jeu</title>
</head>
<body>
    <header>
        <nav>
            <a href="#" class="logo"><img src="../img/logo.png" alt="logo"></a>
            <ul>
                <li><a href="../views/pc.php"><img src="../img/pc.png" alt="pc_logo"><p>PC</p></a></li>
                <li><a href="../views/playstation.php"><img src="../img/playstation.png" alt="playstation_logo"><p>Playstation</p></a></li>
                <li><a href="../views/xbox.php"><img src="../img/xbox.png" alt="xbox_logo"><p>Xbox</p></a></li>
                <li><a href="../views/nintendo.php"><img src="../img/nintendo.png" alt="nintendo_logo"><p>Nintendo</p></a></li>
            </ul>
            <div class="user-buttons">
                <img src="../img/user.png" alt="userImage" class="userButton">
            </div>
            <ul class="profil-dropdown">
                <li><a href="">Profil</a></li>
                <?php if ($role == 'Admin'): ?>
                    <li><a href="../views/paneladmin.php">Panel admin</a></li>
                <?php endif ?>
                <li><a href="">Mes achats</a></li>
                <li>
                    <?php if ($status == 'Connexion'): ?>
                        <a href="../views/connexion.php"><?php echo $status; ?></a>
                    <?php else: ?>
                        <a href="?action=logout"><?php echo $status; ?></a>
                    <?php endif; ?>
                </li>
            </ul>
        </nav>
    </header>
    <main>
        <section id="section-game-details">
            <div id="img-game-details">
       
                <img src="<?php echo isset($gameDetails['image']) ? convertBlobToBase64($gameDetails['image']) : 'default-image-path'; ?>" alt="img game" id='img-game'>
      
            <div id="div-game-details-comments">
                <div id="div-game-name-studio-platform-genre-price">
                    <div id="div-game-name-fav">
                        <h1 id="game-name"><?php echo isset($gameDetails['name']) ? htmlspecialchars($gameDetails['name']) : 'Nom non disponible'; ?></h1>
                    </div>
                    <h5 id="studio"><?php echo isset($gameDetails['studio']) ? htmlspecialchars($gameDetails['studio']) : 'Studio non disponible'; ?></h5>
                    <div id="div-platform">
                        <?php
                        // Afficher les plateformes
                        if ($gamePlatforms) {
                            foreach ($gamePlatforms as $platform) {
                                echo '<p class="platform">' . htmlspecialchars($platform) . '</p>' . '-';
                            }
                        } else {
                            echo '<p class="platform">Plateformes non disponibles</p>';
                        }
                        ?>
                        <?php
                        // Afficher l'état de stock
                        $stock = isset($gameDetails['quantity']) ? intval($gameDetails['quantity']) : 0;
                        if ($stock >= 1) {
                            echo '<p id="stock">En stock</p>';
                        } else {
                            echo '<p id="stock" style="color:red">Rupture de stock</p>';
                        }
                        ?>
                    </div>
                    <div id='div-genres'>
                        <?php
                        // Afficher les genres
                        if ($gameGenres) {
                            foreach ($gameGenres as $genre) {
                                echo '<p class="genre">' . htmlspecialchars($genre) . '</p>' ;
                            }
                        } else {
                            echo '<p class="genre">Genres non disponibles</p>';
                        }
                        ?>
                    </div>
                    <div id="div-special-offer-price">
                        <?php if(isset($gameDetails['special_offer']) && $gameDetails['special_offer'] != 0): ?>
                            <p id="price-before-special-offer"><?php echo isset($gameDetails['price']) ? htmlspecialchars($gameDetails['price']) . '€' : 'Prix non disponible'; ?></p>
                            <p id="special-offer"><?php echo isset($gameDetails['special_offer']) ? htmlspecialchars('-' . $gameDetails['special_offer']) . '%' : 'Offre spéciale non disponible'; ?></p>
                            <?php
                            // Calcul du prix avec réduction
                            $price = $price - ($price * ($gameDetails['special_offer'] / 100));
                            $price = round($price, 2);
                            ?>
                        <?php endif; ?>
                    </div>
                    <h2 id="price"><?php echo isset($price) ? htmlspecialchars($price) . '€' : 'Prix non disponible'; ?></h2>
                    <div id='div-btn-icon-fav'>
                        <button>Ajouter au panier</button>
                        <img src="../img/icon-heart-empty.svg" alt="fav icon" id="favorite-icon">
                    </div>
                </div>
            </div>
            </div>
            <div id='div-about-comments'>
                <div id='div-about'>
                    <h3 id="about">À propos</h3>
                    <p><?php echo isset($gameDetails['description']) ? htmlspecialchars($gameDetails['description']) : 'Description non disponible.'; ?></p>
                </div>
                        <div class="chat-container">

                            <div class="chat-header">
                                <p id="title-comments">Commentaires</p>
                                <div id="div-mark">
                                    <h2 id="average-note">Note moyenne:</h2>
                                    <p><?php echo isset($gameDetails['rate']) ? htmlspecialchars($gameDetails['rate']) . '/5' : 'Note non disponible'; ?></p>
                                    <img src="../img/star-icon.svg" alt="star icon" id="star-icon">
                                </div>
                            </div>
                            <div class="chat-messages" id="chat-messages">
                                <!-- Messages vont être ajoutés ici -->
                            </div>
                            <div class="chat-input">
                                <input type="text" id="message-input" placeholder="Entrez votre message...">
                                <button onclick="sendMessage()">Envoyer</button>
                            </div>
                        </div>
                    </div>
                
        </section>
    </main>
</body>
</html>

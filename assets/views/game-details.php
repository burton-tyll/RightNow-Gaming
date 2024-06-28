<?php
require_once('../Class/Game.php');
require_once('../Class/Game_platform.php');

$game = new Game();
$game_platforms = new Game_platform();

session_start();

function convertBlobToBase64($blob) {
    return 'data:image/jpeg;base64,' . base64_encode($blob);
}

// Récupérer l'ID du jeu depuis l'URL
$gameId = isset($_GET['id']) ? intval($_GET['id']) : null;
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
    }else{
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
            <div id="div-game-img-text">
                <img src="<?php echo isset($gameDetails['image']) ? convertBlobToBase64($gameDetails['image']) : 'default-image-path'; ?>" alt="img game">
                <h3 id="about">À propos</h3>
                <p><?php echo isset($gameDetails['description']) ? htmlspecialchars($gameDetails['description']) : 'Description non disponible.'; ?></p>
            </div>
            <div id="div-game-details-comments">
                <div id="div-game-name-studio-platform-genre-price">
                    <div id="div-game-name-fav">
                        <h1 id="game-name"><?php echo isset($gameDetails['name']) ? htmlspecialchars($gameDetails['name']) : 'Nom non disponible'; ?></h1>
                        <img src="../img/icon-heart-empty.svg" alt="fav icon" id="favorite-icon">
                    </div>
                    <h5 id="studio"><?php echo isset($gameDetails['studio']) ? htmlspecialchars($gameDetails['studio']) : 'Studio non disponible'; ?></h5>
                    <div id="div-platform">
                        <?php
                        // Afficher les plateformes
                        if ($gamePlatforms) {
                            foreach ($gamePlatforms as $platform) {
                                echo '<p class="platform">' . htmlspecialchars($platform) . '</p>';
                            }
                        } else {
                            echo '<p class="platform">Plateformes non disponibles</p>';
                        }
                        ?>
                        <p id="stock">En stock</p>
                    </div>
                    <h3 id="genre"><?php echo isset($gameDetails['genre']) ? htmlspecialchars($gameDetails['genre']) : 'Genre non disponible'; ?></h3>
                    <div id="div-special-offer-price">
                        <p id="price-before-special-offer"><?php echo isset($gameDetails['price']) ? htmlspecialchars($gameDetails['price']) . '€' : 'Prix non disponible'; ?></p>
                        <p id="special-offer"><?php echo isset($gameDetails['special_offer']) ? htmlspecialchars($gameDetails['special_offer']) . '%' : 'Offre spéciale non disponible'; ?></p>
                    </div>
                    <h2 id="price"><?php echo isset($gameDetails['price']) ? htmlspecialchars($gameDetails['price']) . '€' : 'Prix non disponible'; ?></h2>
                    <button>Ajouter au panier</button>
                </div>
                <div id="div-comments">
                    <div id="div-mark">
                        <h2>Note moyenne:</h2>
                        <p><?php echo isset($gameDetails['rate']) ? htmlspecialchars($gameDetails['rate']) . '/5' : 'Note non disponible'; ?></p>
                        <img src="../img/star-icon.png" alt="star icon">
                    </div>
                    <div id="all-comments">
                        <!-- Afficher les commentaires des utilisateurs -->
                    </div>
                    <div id="user-comment">
                        <!-- Formulaire pour ajouter un commentaire -->
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>

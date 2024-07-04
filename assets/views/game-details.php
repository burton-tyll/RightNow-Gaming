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

// Fonction pour ajouter un jeu au panier
function addToCart($gameId) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$gameId])) {
        $_SESSION['cart'][$gameId]++;
    } else {
        $_SESSION['cart'][$gameId] = 1;
    }

    return ['status' => 'success', 'message' => 'Jeu ajouté au panier'];
}

// Gestion de l'ajout au panier via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $gameId = isset($input['id']) ? intval($input['id']) : null;

    if ($gameId !== null) {
        $result = addToCart($gameId);
        echo json_encode($result);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID du jeu manquant']);
    }
    exit();
}

// Vider le panier (pour le test)
if (isset($_GET['action']) && $_GET['action'] === 'clear_cart') {
    unset($_SESSION['cart']);
    header("Location: game-details.php?id=" . intval($_GET['id']));
    exit();
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
$gameGenres = $game_genres->getGameGenre($gameId);

// Calculer le nombre total de jeux dans le panier
$totalItems = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/game-details.css">
    <?php include('../templates/global.php')?>
    <link rel="icon" type="image/x-icon" href="../img/favicon.png">
    <script src="../script/comments.js" defer></script>
    <script src="../script/global.js" defer></script>
    <title>RightNow Gaming - Détails du jeu</title>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addToCartButton = document.querySelector('#add-to-cart-button');
            addToCartButton.addEventListener('click', function () {
                const gameId = <?php echo json_encode($gameId); ?>;

                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: gameId }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Jeu ajouté au panier');
                        // Recharger la page pour voir les modifications
                        window.location.reload();
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });

            // Mettre à jour le nombre total de jeux dans le panier
            const updateCartCount = () => {
                fetch('../Class/Cart.php', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.total) {
                        document.querySelector('#total-items').textContent = data.total;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            };

            updateCartCount();
        });
    </script>
</head>
<body>

    <!-- Inclusion de l'en-tête -->
    <?php include('../templates/header.php') ?>

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
                            if ($gamePlatforms) {
                                foreach ($gamePlatforms as $platform) {
                                    echo '<p class="platform">' . htmlspecialchars($platform) . '</p>' . '-';
                                }
                            }
                            ?>
                            <?php
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
                            if ($gameGenres) {
                                foreach ($gameGenres as $genre) {
                                    echo '<p class="genre">' . htmlspecialchars($genre) . '</p>' ;
                                }
                            }
                            ?>
                        </div>
                        <?php if ($stock >= 1): ?>
                            <div id="div-special-offer-price">
                                <?php if(isset($gameDetails['special_offer']) && $gameDetails['special_offer'] != 0): ?>
                                    <p id="price-before-special-offer"><?php echo isset($gameDetails['price']) ? htmlspecialchars($gameDetails['price']) . '€' : 'Prix non disponible'; ?></p>
                                    <p id="special-offer"><?php echo isset($gameDetails['special_offer']) ? htmlspecialchars('-' . $gameDetails['special_offer']) . '%' : 'Offre spéciale non disponible'; ?></p>
                                    <?php
                                    $price = $gameDetails['price'] - ($gameDetails['price'] * ($gameDetails['special_offer'] / 100));
                                    $price = round($price, 2);
                                    ?>
                                <?php else: ?>
                                    <?php $price = isset($gameDetails['price']) ? $gameDetails['price'] : null; ?>
                                <?php endif; ?>
                            </div>
                            <h2 id="price"><?php echo isset($price) ? htmlspecialchars($price) . '€' : 'Prix non disponible'; ?></h2>
                            <div id='div-btn-icon-fav'>
                                <button id="add-to-cart-button">Ajouter au panier</button>
                                <button id="btn-fav">
                                <img src="../img/icon-heart-empty.svg" alt="fav icon" id="favorite-icon">
                                </button>
                            </div>
                        <?php endif; ?>
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
                            <h2 id="average-note">Note moyenne: &nbsp;</h2>
                            <p id="note"><?php echo isset($gameDetails['rate']) ? htmlspecialchars($gameDetails['rate']) . '/5' : 'Note non disponible'; ?></p>
                            <img src="../img/star-icon.svg" alt="star icon" id="star-icon">
                        </div>
                    </div>
                    <div class="chat-messages" id="chat-messages">
                    </div>
                    <div class="chat-input">
                        <textarea type="text" id="comment-input" placeholder="Entrez votre commentaire..."></textarea>
                        <select name="ranking" id="select-ranking">
                            <option value="stars" class="stars">...</option>
                            <option value="0stars" class="stars">0</option>
                            <option value="1stars" class="stars">1</option>
                            <option value="2stars" class="stars">2</option>
                            <option value="3stars" class="stars">3</option>
                            <option value="4stars" class="stars">4</option>
                            <option value="5stars" class="stars">5</option>
                        </select>
                        <p id="outOf">/5 </p>
                        <div>
                        <img src="../img/star-icon.svg" alt="star icon" id="outOfStars" >
                        </div>
                        <button onclick="sendMessage()" id="btn-send-comment">Envoyer</button>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>

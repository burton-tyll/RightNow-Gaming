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

// Fonction pour ajouter un jeu au panier avec la plateforme sélectionnée
function addToCart($gameId, $platformId) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Vérifiez que la plateforme est valide
    if ($platformId !== null) {
        if (!isset($_SESSION['cart'][$gameId])) {
            $_SESSION['cart'][$gameId] = ['quantity' => 0, 'platforms' => []];
        }

        // Ajouter la plateforme si elle n'est pas déjà ajoutée
        if (!isset($_SESSION['cart'][$gameId]['platforms'][$platformId])) {
            $_SESSION['cart'][$gameId]['platforms'][$platformId] = 0;
        }

        $_SESSION['cart'][$gameId]['platforms'][$platformId]++;
    } else {
        return ['status' => 'error', 'message' => 'ID de la plateforme manquant'];
    }

    return ['status' => 'success', 'message' => 'Jeu ajouté au panier'];
}

// Gestion de l'ajout au panier via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $gameId = isset($input['id']) ? intval($input['id']) : null;
    $platformId = isset($input['platformId']) ? intval($input['platformId']) : null;

    if ($gameId !== null && $platformId !== null) {
        $result = addToCart($gameId, $platformId);
        echo json_encode($result);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID du jeu ou ID de la plateforme manquant']);
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
$totalItems = isset($_SESSION['cart']) ? array_sum(array_map(function($data) {
    return isset($data['platforms']) ? array_sum($data['platforms']) : 0;
}, $_SESSION['cart'])) : 0;

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
    <script src="../script/game-platform-selection.js" defer></script>
    <title>RightNow Gaming - Détails du jeu</title>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Ajout de l'événement pour la sélection des plateformes
            document.querySelectorAll('.platform').forEach(platform => {
                platform.addEventListener('click', function () {
                    document.querySelectorAll('.platform').forEach(p => p.classList.remove('platform-selected'));
                    this.classList.add('platform-selected'); // Marque la plateforme comme sélectionnée
                });
            });

            // Ajout du jeu au panier
            const addToCartButton = document.querySelector('#add-to-cart-button');
            addToCartButton.addEventListener('click', function () {
                const gameId = <?php echo json_encode($gameId); ?>;
                const platformElement = document.querySelector('.platform-selected');
                if (platformElement) {
                    const platformId = platformElement.dataset.platformId;

                    fetch('', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id: gameId, platformId: platformId }),
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
                } else {
                    alert('Veuillez sélectionner une plateforme.');
                }
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
                                foreach ($gamePlatforms as $platformId => $platformName) {
                                    echo '<p class="platform" data-platform-id="' . htmlspecialchars($platformId) . '">' . htmlspecialchars($platformName) . '</p>' . '-';
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
                                    <p id="special-offer"><?php echo isset($gameDetails['special_offer']) ? htmlspecialchars('-' . $gameDetails['special_offer'] . '€') : 'Offre spéciale non disponible'; ?></p>
                                    <?php $price = $gameDetails['price'] - $gameDetails['special_offer']; ?>
                                <?php else: ?>
                                    <?php $price = isset($gameDetails['price']) ? htmlspecialchars($gameDetails['price']) : null; ?>
                                <?php endif; ?>
                            </div>
                            <h2 id="price"><?php echo isset($price) ? htmlspecialchars($price) . '€' : 'Prix non disponible'; ?></h2>
                            <div id='div-btn-icon-fav'>
                                <button id="add-to-cart-button">Ajouter au panier</button>
                                <!-- <button id="clear-cart"><a href="game-details.php?id=<?php echo htmlspecialchars($gameId); ?>&action=clear_cart">Vider le panier</a></button> -->
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>

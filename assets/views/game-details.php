<?php

session_start();

require_once('../Class/Game.php');
require_once('../Class/Game_platform.php');
require_once('../Class/Genre.php');
require_once('../Class/Comment.php');  // Assurez-vous que ce fichier est inclus

$game = new Game();
$game_platforms = new Game_platform();
$game_genres = new Genre();
$comment = new Comment();  

//---------------
    //---------------GESTIONNAIRE DE CONNEXION UTILSATEUR
    //---------------

    function getStatus() {
        if (!isset($_SESSION['user'])) {
            return 'Connexion';
        } else {
            return 'Déconnexion';
        }
    }
    
    function disconnect() {
        session_destroy();
        header("Location: connexion.php"); // Redirection après déconnexion
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
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            return 0;
        }
    
        return array_sum(array_column($_SESSION['cart'], 'quantity'));
    }
    
    $totalItems = getCartCount();

// Vérifier que l'utilisateur est connecté
$userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;

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
// if (isset($_GET['action']) && $_GET['action'] === 'clear_cart') {
//     unset($_SESSION['cart']);
//     header("Location: game-details.php?id=" . intval($_GET['id']));
//     exit();
// }

// Récupérer l'ID du jeu depuis l'URL
$gameId = isset($_GET['id']) ? intval($_GET['id']) : null;

// Récupérer les détails du jeu
$gameDetails = $game->getGameById($gameId);
if (!$gameDetails && !isset($_GET['action'])) {
    echo "Détails du jeu introuvables.";
    exit();
}

$gamePlatforms = $game_platforms->getGamePlateform($gameId);
$gameGenres = $game_genres->getGameGenre($gameId);

// Calculer le nombre total de jeux dans le panier
$totalItems = isset($_SESSION['cart']) ? array_sum(array_map(function($data) {
    return isset($data['platforms']) ? array_sum($data['platforms']) : 0;
}, $_SESSION['cart'])) : 0;

// Avoir la note moyenne d'un jeu
$averageRating = $comment->getAverageRatingForGame($gameId); 

// Avoir les commentaires d'un jeu
$comments = $comment->getCommentsByGameId($gameId);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="game-id" content="<?php echo $gameId; ?>">
    <link rel="stylesheet" href="../styles/game-details.css">
    <?php include('../templates/global.php')?>
    <link rel="icon" type="image/x-icon" href="../img/favicon.png">
    <script src="../script/global.js" defer></script>
    <script src="../script/game-platform-selection.js" defer></script>
    <title>RightNow Gaming - Détails du jeu</title>
    <script>
function sendMessage() {
    const commentInput = document.querySelector('#comment-input');
    const rankingSelect = document.querySelector('#select-ranking');
    const ranking = rankingSelect.value.replace('stars', ''); // Extraire le nombre d'étoiles
    const content = commentInput.value;

    // Vérifiez si la note est un nombre et est un entier
    const rating = parseInt(ranking, 10);

    if (content && !isNaN(rating)) {
        fetch('../Class/Comment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'add_comment',
                id_game: <?php echo $gameId; ?>,
                rating: rating,  // Envoyer la note, y compris 0
                content: content,
                id_user: <?php echo $_SESSION['user_id']; ?>  // Inclure l'ID utilisateur
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Commentaire ajouté avec succès.');
                commentInput.value = ''; // Réinitialiser le champ commentaire
                rankingSelect.value = 'stars'; // Réinitialiser la sélection des étoiles

                // Mettre à jour la note du jeu affichée sur la page
                document.querySelector('#note').textContent = data.newAverageRating + '/5';

                // Recharger la page pour afficher le nouveau commentaire
                window.location.href = `game-details.php?id=<?php echo $gameId; ?>&reload=true`;
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    } else {
        alert('Veuillez entrer un commentaire et une note valide.');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Vérifiez si le paramètre `reload` est présent dans l'URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('reload') === 'true') {
        // Recharger les commentaires après le rechargement de la page
        fetch('../Class/Comment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'get_comments',
                id_game: <?php echo $gameId; ?>,
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const chatMessages = document.querySelector('#chat-messages');
                chatMessages.innerHTML = '';  // Clear existing comments
                data.comments.forEach(comment => {
                    const commentHTML = `
                        <div class="message">
                            <div class="message-username">
                                <strong>${comment.username}</strong>
                            </div>
                            <div class="message-content">
                                <p>${comment.content}</p>
                            </div>
                            <div class="message-rating">
                                <p>Note: ${comment.rating}/5</p>
                            </div>
                            <div class="message-date">
                                <p>${comment.created_at}</p>
                            </div>
                        </div>
                    `;
                    chatMessages.insertAdjacentHTML('beforeend', commentHTML);
                });
            } else {
                console.error('Erreur lors du chargement des commentaires:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
});
</script>

</head>
<body>

<header>
    <nav>
        <a href="../../index.php" class="logo"><img src="../img/logo.png" alt="logo"></a>
        <ul>
            <li><a href="./pc.php"><img src="../img/pc.png" alt="pc_logo"><p>PC</p></a></li>
            <li><a href="./playstation.php"><img src="../img/playstation.png" alt="playstation_logo"><p>Playstation</p></a></li>
            <li><a href="./xbox.php"><img src="../img/xbox.png" alt="xbox_logo"><p>Xbox</p></a></li>
            <li><a href="./nintendo.php"><img src="../img/nintendo.png" alt="nintendo_logo"><p>Nintendo</p></a></li>
        </ul>
            
        <div class="user-buttons">
            <a href="./cart.php" id="icon-cart">
                <img src="../img/icon-cart.svg" alt="img cart"></img>
                <p id="total-items"><?php echo $totalItems; ?></p>
            </a>
            <img src="../img/user.png" alt="userImage" class="userButton">
        </div>
        <ul class="profil-dropdown">
            <li><a href="">Profil</a></li>
            <?php if ($role == 'Admin'): ?>
                <li><a href="paneladmin.php">Panel admin</a></li>
            <?php endif ?>
            <li><a href="">Mes achats</a></li>
            <li>
                <?php if ($status == 'Connexion'): ?>
                    <a href="connexion.php"><?php echo $status; ?></a>
                <?php else: ?>
                    <a href="?action=logout"><?php echo $status; ?></a>
                <?php endif; ?>
            </li>
        </ul>

    </nav>
</header>


    <main>
        <div id="section-game-details">
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
            <div id='div-about-comments'>
                <div id='div-about'>
                    <h3 id="about">À propos</h3>
                    <p><?php echo isset($gameDetails['description']) ? htmlspecialchars($gameDetails['description']) : 'Description non disponible.'; ?></p>
                </div>
                <div class="chat-container">
                    <div class="chat-header">
                        <p id="title-comments">Commentaires</p>
                        <div id="div-mark">
                            <p id="note">Note moyenne: <?php echo isset($gameDetails['rate']) ? htmlspecialchars($gameDetails['rate']) . '/5' : 'Note non disponible'; ?></p>
                            <img src="../img/star-icon.svg" alt="star icon" id="star-icon">
                        </div>
                    </div>
                    <div class="chat-messages" id="chat-messages">
                        <?php if ($comments): ?>
                            <?php foreach ($comments as $comment): ?>
                                <div class="message">
                                    <div class="message-username">
                                        <strong><?php echo htmlspecialchars($comment['username']); ?></strong>
                                    </div>
                                    <div class="message-content-date-rating">
                                        <div class="message-content-date">
                                        <div class="message-content">
                                            <p><?php echo htmlspecialchars($comment['content']); ?></p>
                                        </div>

                                        <div class="message-date">
                                            <p><?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?></p>
                                        </div>
                                        </div>
                                        <div class="message-rating">
                                            <p><?php echo htmlspecialchars($comment['rating']); ?>/5<div><img src="../img/star-icon.svg" alt="star icon"></div></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p id="no-comments">Aucun commentaire pour ce jeu.</p>
                        <?php endif; ?>
                    </div>
                    <?php if ($userId): ?>
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
                        <?php endif; ?>

                </div>
            </div>
        </section>
    </main>
</body>
</html>

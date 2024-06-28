<?php

    require_once('../Class/User.php');
    require_once('../Class/Game.php');
    require_once('../Class/Game_platform.php');
    require_once('../Class/Genre.php');

    $user = new User();
    $game = new Game();
    $game_platform = new Game_platform();
    $game_genre = new Genre();

    $games = $game->getAllGames();

    $users = $user->read();

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

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include('../templates/global.php'); ?>
    <link rel="stylesheet" href="../styles/paneladmin.css">
    <title>Panel Admin</title>
</head>
<body>
    <header>
        <nav>
            <a href="../../index.php" class="logo"><img src="../img/logo.png" alt="logo"></a>
            <h1 class="admin_title">PANEL ADMIN</h1>
            <div class="user-buttons">
                <img src="../img/user.png" alt="userImage" class="userButton">
            </div>
            <ul class="profil-dropdown">
            <li><a href="">Profil</a></li>
            <?php if ($role == 'Admin'): ?>
                <li><a href="./paneladmin.php">Panel admin</a></li>
            <?php endif ?>
            <li><a href="">Mes achats</a></li>
            <li>
                <?php if ($status == 'Connexion'): ?>
                    <a href="./connexion.php"><?php echo $status; ?></a>
                <?php else: ?>
                    <a href="?action=logout"><?php echo $status; ?></a>
                <?php endif; ?>
            </li>
        </ul>
        </nav>
    </header>
    <main>
        <section id="adminSection">
            <div class="menu">
                <ul>
                    <li><a id="userPage" href="?users">Gérer des utilisateurs</a></li>
                    <li><a id="productPage" href="?products">Gérer des produits</a></li>
                    <li><a id="orderPage" href="?orders">Gérer des commandes</a></li>
                </ul>
                <ul class="actionsList">
                    <?php if(isset($_GET['products'])): ?>
                        <li><button style="background-color: green;"><a href="addGame.php">Ajouter un jeu</a></button></li>
                        <li></li>
                    <?php endif ?>
                </ul>
            </div>
            <div class="content">
                <?php if(isset($_GET['users'])): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>Nom d'utilisateur</th>
                                <th>Prénom</th>
                                <th>Nom</th>
                                <th>E-mail</th>
                                <th>Adresse</th>
                                <th>Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $userRole = null;
                                foreach($users as $thisone){
                                    if($thisone['admin'] == 1){
                                        $userRole = "Admin";
                                    }else{
                                        $userRole = "Utilisateur";
                                    } 
                                    echo
                                    '
                                    <tr>
                                        <td><a href="./user-management.php?username=' . $thisone['username'] . '">' . $thisone['id'] . '</a></td>
                                        <td><a href="./user-management.php?username=' . $thisone['username'] . '">' . $thisone['username'] . '</a></td>
                                        <td><a href="./user-management.php?username=' . $thisone['username'] . '">' . $thisone['first_name'] . '</a></td>
                                        <td><a href="./user-management.php?username=' . $thisone['username'] . '">' . $thisone['name'] . '</a></td>
                                        <td><a href="./user-management.php?username=' . $thisone['username'] . '">' . $thisone['email'] . '</a></td>
                                        <td><a href="./user-management.php?username=' . $thisone['username'] . '">' . $thisone['adress'] . '</a></td>
                                        <td><a href="./user-management.php?username=' . $thisone['username'] . '">' . $userRole . '</a></td>
                                    </tr>
                                    ';
                                }
                            ?>
                        </tbody>
                    </table>
                <?php endif ?>
                <?php if(isset($_GET['products'])): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>Nom du jeu</th>
                                <th>Prix</th>
                                <th>Quantité</th>
                                <th>Vendus</th>
                                <th>Date de sortie</th>
                                <th>Note</th>
                                <th>Catégorie</th>
                                <th>Plateforme</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach ($games as $thisone) {
                                    // Récupérer les genres du jeu
                                    $gameGenre = $game_genre->getGameGenre($thisone['id']);
                                    // Récupérer les plateformes du jeu
                                    $gamePlatform = $game_platform->getGamePlateform($thisone['id']);
                                    // Créer une chaîne pour les genres
                                    $genres = '';
                                    $i = 0;
                                    foreach ($gameGenre as $genre) {
                                        $i++;
                                        if ($i > 1){
                                            $genres .= $genre . ', ';
                                        } else{
                                            $genres .= $genre;
                                        }
                                    }
                                    // Créer une chaîne pour les plateformes
                                    $platforms = '';
                                    $n = 0;
                                    foreach ($gamePlatform as $platform){
                                        $n++;
                                        if ($n > 1){
                                            $platforms .= ', ' . $platform ;
                                        } else{
                                            $platforms .= $platform;
                                        }
                                    }

                                    echo
                                    '
                                    <tr>
                                        <td><a href="./product-management.php?id=' . $thisone['id'] . '">' . $thisone['id'] . '</a></td>
                                        <td><a href="./product-management.php?id=' . $thisone['id'] . '">' . $thisone['name'] . '</a></td>
                                        <td><a href="./product-management.php?id=' . $thisone['id'] . '">' . $thisone['price'] . '</a></td>
                                        <td><a href="./product-management.php?id=' . $thisone['id'] . '">' . $thisone['quantity'] . '</a></td>
                                        <td><a href="./product-management.php?id=' . $thisone['id'] . '">' . $thisone['sales'] . '</a></td>
                                        <td><a href="./product-management.php?id=' . $thisone['id'] . '">' . $thisone['release_date'] . '</a></td>
                                        <td><a href="./product-management.php?id=' . $thisone['id'] . '">' . $thisone['rate'] . '</a></td>
                                        <td><a href="./product-management.php?id=' . $thisone['id'] . '">' . $genres . '</a></td>
                                        <td><a href="./product-management.php?id=' . $thisone['id'] . '">' . $platforms . '</a></td>
                                    </tr>
                                    ';
                                }
                            ?>
                        </tbody>
                    </table>
                <?php endif ?>
            </div>
        </section>
    </main>
</body>
</html>
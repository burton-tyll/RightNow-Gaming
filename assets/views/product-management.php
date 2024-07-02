<?php

    require_once('../Class/Game.php');

    $game = new Game();

    $games = $game->getAllGames();

    $stock = null;
    $notification = null;

    session_start();

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

    //GESTIONNAIRE JEUX
    if(isset($_POST['delete'])){
        $game->deleteGame($_POST['delete']);
        echo '<script>alert("Le jeu a été supprimé avec succès")</script>';
        // header('Location: ./paneladmin.php?products');
        exit;
    }

    $currentId = null;
    $currentName = null;
    $currentStock = null;
    $currentPrice = null;
    $currentOffer = null;

    //GET CURRENT GAME
    if(isset($_GET['id'])){
        foreach($games as $thisone){
            if($_GET['id'] == $thisone['id']){
                $currentId = $thisone['id'];
                $currentName = $thisone['name'];
                $currentStock = $thisone['quantity'];
                $currentPrice = $thisone['price'];
                $currentOffer = $thisone['special_offer'];
            }
        }
    } ;

    //Update Game

    if(!empty($_POST['price'])){
        $game->updateGameByID($_POST['id'], 'price', $_POST['price']);
        echo ('<script>show</script>')
        header("Location: ?id=".$_POST["id"]."");
    };

    if(!empty($_POST['special_offer'])){
        $game->updateGameByID($_POST['id'], 'special_offer', $_POST['special_offer']);
        header("Location: ?id=".$_POST["id"]."");
    };

    if(!empty($_POST['stock'])){
        $game->updateGameByID($_POST['id'], 'quantity', $_POST['stock']);
        header("Location: ?id=".$_POST["id"]."");
    };

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include('../templates/global.php') ?>
    <link rel="stylesheet" href="../styles/paneladmin.css">
    <title>Gestion utilisateurs</title>
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
        <section id="gestionnaire">
            <div class="content">
                <a class="back" href="paneladmin.php"><img src="../img/back.png" alt="bouton_retour"></a>
                <h1>Gestionnaire de produits</h1>
                <div class="actions">
                    <div>
                        <p id="notification" style="color: green;"><?php echo $notification; ?></p>
                        <h2><?php echo $currentName ?></h2>
                    </div>
                    <form action="product-management.php" method="POST">
                        <input type="hidden" name="delete" value="<?php echo $currentId ?>">
                        <button class="bin" type="submit"><img src="../img/bin.png" alt="supprimer le jeu"></button>
                    </form>
                    <form action="product-management.php" method="POST" class="productsForm">
                        <input type="hidden" name="id" value="<?php echo $currentId ?>">

                        <label for="price">Prix:</label>
                        <input type="number" name="price" placeholder="<?php echo $currentPrice .'€';?>">

                        <label for="special_offer">Réduction:</label>
                        <input type="number" name="special_offer" placeholder="<?php echo $currentOffer .'%' ?>">

                        <label for="stock">Stock:</label>
                        <input type="number" name="stock" placeholder="<?php echo $currentStock .' en stock'; ?>">

                        <button type="submit">Envoyer les modifications</button>
                    </form>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
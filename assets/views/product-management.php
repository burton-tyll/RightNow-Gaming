<?php

    require_once('../Class/Game.php');

    $game = new Game();

    $games = $game->getAllGames();

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

    //GESTIONNAIRE UTILISATEURS
    if(isset($_GET['delete'])){
        $game->deleteGame($_GET['delete']);
        echo '<script>alert("Le jeu a été supprimé avec succès")</script>';
        header('Location: ./paneladmin.php?products');
        exit;
    }

    if(isset($_GET['upgrade'])){
        $user->upgradeToAdmin($_GET['upgrade']);
        echo '<script>alert("L\'utilisateur a été promu Administrateur!")</script>';
    }

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
                        <h2><?php if(isset($_GET['id'])){
                            foreach($games as $thisone){
                                if($_GET['id'] == $thisone['id']){
                                    echo $thisone['name'];
                                }
                            }
                        } ?></h2>
                    </div>
                    <form action="user-management.php" method="GET">
                        <input type="hidden" name="upgrade" value="<?php if(isset($_GET['username'])){echo $_GET['username'];} ?>">
                        <button type="submit">Promouvoir administrateur</button>
                    </form>
                    <button type="submit">Réinitialiser le mot de passe</button>
                    <form action="product-management.php" method="GET">
                        <input type="hidden" name="delete" value="<?php if(isset($_GET['id'])){echo $_GET['id'];} ?>">
                        <button type="submit" class="delete">Supprimer</button>
                    </form>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
<?php

    require_once('../Class/User.php');

    $user = new User();

    $users = $user->read();

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
        $user->deleteUser($_GET['delete']);
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
                <h1>Gestionnaire utilisateurs</h1>
                <div class="actions">
                    <div>
                        <img src="../img/user.png" alt="userImage" class="user-img">
                        <h2>burton-tyll</h2>
                    </div>
                    <button>Promouvoir administrateur</button>
                    <button>Réinitialiser le mot de passe</button>
                    <form action="user-management.php" method="GET">
                        <input type="hidden" name="delete" value="<?php if(isset($_GET['username'])){echo $_GET['username'];} ?>">
                        <button type="submit" class="delete">Supprimer</button>
                    </form>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
<?php

    require_once('../Class/User.php');

    $user = new User();

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
            <div class="sideMenu">
                <ul>
                    <li><a href="?users">Gérer des utilisateurs</a></li>
                    <li><a href="?products">Gérer des produits</a></li>
                    <li><a href="?orders">Gérer des commandes</a></li>
                </ul>
            </div>
            <div class="content">
                <?php if(isset($_GET['users'])): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>Nom d'utilisateur</th>
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
                                        <td><a href="./user-management?username=' . $thisone['username'] . '">' . $thisone['id'] . '</a></td>
                                        <td><a href="./user-management?username=' . $thisone['username'] . '">' . $thisone['username'] . '</a></td>
                                        <td><a href="./user-management?username=' . $thisone['username'] . '">' . $thisone['email'] . '</a></td>
                                        <td><a href="./user-management?username=' . $thisone['username'] . '">' . $thisone['adress'] . '</a></td>
                                        <td><a href="./user-management?username=' . $thisone['username'] . '">' . $userRole . '</a></td>
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
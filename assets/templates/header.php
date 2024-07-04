<?php
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
    
?>

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

<?php

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
        <ul class="profil-dropdown">
            <li>Profil</li>
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



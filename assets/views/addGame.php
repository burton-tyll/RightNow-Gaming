<?php
require_once('../Class/Game.php');
require_once('../Class/Game_platform.php');

$game = new Game();
$platform = new Game_platform();

$allPlatforms = $platform->getAllPlatforms();

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

//Gestion du formulaire

if (isset($_POST['submit'])) {
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['file'];
        
        // Vérifier le type de fichier
        $fileType = mime_content_type($file['tmp_name']);
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        
        if (in_array($fileType, $allowedTypes)) {
            // Lire le contenu du fichier
            $imageData = file_get_contents($file['tmp_name']);
            
            if (isset($_POST['name']) && isset($_POST['description']) && isset($_POST['price']) && 
                isset($_POST['special_offer']) && isset($_POST['studio']) && isset($_POST['quantity']) && 
                isset($_POST['release_date']) && isset($_POST['rate'])) {
                
                $name = $_POST['name'];
                $description = $_POST['description'];
                $price = $_POST['price'];
                $special_offer = $_POST['special_offer'];
                $studio = $_POST['studio'];
                $quantity = $_POST['quantity'];
                $release_date = $_POST['release_date'];
                $rate = $_POST['rate'];
                $id_platform = $_POST['platform'];

                // Ajouter le jeu et récupérer son ID
                $gameId = $game->addGame($imageData, $name, $description, $price, $special_offer, $studio, $quantity, $release_date, $rate);

                if ($gameId) {
                    $platform->addGameToPlatform($gameId, $id_platform);
                    echo "Le jeu a été ajouté avec succès avec l'ID : " . $gameId;
                } else {
                    echo "Erreur lors de l'ajout du jeu.";
                }
            } else {
                echo "Veuillez remplir tous les champs du formulaire.";
            }
        } else {
            echo "Seules les images JPEG, PNG et GIF sont autorisées.";
        }
    } else {
        echo "Erreur lors de l'upload du fichier.";
    }
}
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include('../templates/global.php') ?>
    <link rel="stylesheet" href="../styles/paneladmin.css">
    <title>Ajouter un jeu</title>
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
        <section id="addGame">
            <h1>Ajouter un Produit</h1>
            <form action="addGame.php" method="post" enctype="multipart/form-data" class="formulaire">
                <div class="chooseFile">
                    <label for="file">Choisir une image :</label>
                    <input type="file" name="file" id="file" accept=".png, .jpg, .jpeg, .webp" required>
                </div>
                <input type="text" name="name" placeholder="Nom" required>
                <input type="text" name="description" placeholder="Description" required>
                <input type="number" name="price" placeholder="Prix" step="0.01" required>
                <input type="number" name="special_offer" placeholder="Remise" required>
                <input type="text" name="studio" placeholder="Studio" required>
                <select name="platform" id="platform">
                    <option>Choisir une plateforme de jeu</option>
                    <?php foreach($allPlatforms as $thisone): ?>
                        <option value="<?php echo($thisone['id']); ?>"><?php echo($thisone['name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="number" name="quantity" placeholder="Quantité" required>
                <input type="date" name="release_date" placeholder="Date de sortie" required>
                <input type="number" name="rate" step="0.1" placeholder="Note" required>
                <button type="submit" name="submit" class="submitButton">Envoyer</button>
            </form>
        </section>
    </main>
</body>
</html>

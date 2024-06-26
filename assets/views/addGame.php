<?php
require_once('../Class/Game.php');

$game = new Game();

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

                $game->addGame($imageData, $name, $description, $price, $special_offer, $studio, $quantity, $release_date, $rate);
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
            <form action="addGame.php" method="post" enctype="multipart/form-data" class="formulaire">
                <label for="file">Choisir une image :</label>
                <input type="file" name="file" id="file" required>
                <input type="text" name="name" placeholder="Nom" required>
                <input type="text" name="description" placeholder="Description" required>
                <input type="text" name="price" placeholder="Prix" required>
                <input type="text" name="special_offer" placeholder="Remise" required>
                <input type="text" name="studio" placeholder="Studio" required>
                <input type="text" name="quantity" placeholder="Quantité" required>
                <input type="text" name="release_date" placeholder="Date de sortie" required>
                <input type="text" name="rate" placeholder="Note" required>
                <button type="submit" name="submit">Envoyer</button>
            </form>
        </section>
    </main>
</body>
</html>

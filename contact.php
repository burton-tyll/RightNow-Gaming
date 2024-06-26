<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    echo "Votre message a été envoyé avec succès.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <!-------GLOBAL ASSETS------>
    <link rel="stylesheet" href="./assets/styles/global.css">
    <link rel="icon" type="image/x-icon" href="./assets/img/favicon.png">
    <script src="./assets/script/global.js" defer></script>
    <!-------------------------->
    
    <title>Formulaire de contact</title>
</head>
<body>
    <?php include('../templates/header.php') ?>
    <!--Accueil-->
    <h1>Nous contacter</h1>
    <form action="contact.php" method="post">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br>
        <label for="objet">Objet:</label>
        <input type="text" id="objet" name="objet" required>
        <br>
        <label for="message">Message:</label>
        <textarea id="message" name="message" required></textarea>
        <br>
        <button type="submit">Envoyer</button>
    </form>
</body>
</html>

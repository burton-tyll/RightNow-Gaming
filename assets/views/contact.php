<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $objet = $_POST['objet'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    $to = 'aaliyah.houidef@laplateforme.io'; 
    $subject = 'Nouveau message de contact: ' . $objet;
    $body = "Vous avez reçu un nouveau message de contact.\n\n".
            "Email: $email\n".
            "Objet: $objet\n".
            "Message:\n$message";

            $headers = "From: $email\r\n";
            $headers .= "Reply-To: $email\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        

    if (mail($to, $subject, $body, $headers)) {
        echo "Votre message a été envoyé avec succès.";
    } else {
        echo "Une erreur s'est produite lors de l'envoi de votre message.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <!-------GLOBAL ASSETS------>
    <link rel="stylesheet" href="../styles/global.css">
    <link rel="stylesheet" href="../styles/contact.css">

    <link rel="icon" type="image/x-icon" href="../img/favicon.png">
    <script src="../script/global.js" defer></script>
    <!-------------------------->
    
    <title>Formulaire de contact</title>
</head>
<body>
    <!--Navbar-->
    <?php include('../templates/header.php') ?>
    <!---------->
    <main>
        <h1>Nous contacter</h1>
        <div class="form-container">
        <form action="contact.php" method="post" class="contact-form">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                <label for="objet">Objet:</label>
                <input type="text" id="objet" name="objet" required>
                <label for="message">Votre message:</label>
                <textarea id="message" name="message" required></textarea>
                <button type="submit">Envoyer</button>
        </form>
        </div>
    </main>
</body>
</html>

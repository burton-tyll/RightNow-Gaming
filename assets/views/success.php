<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include('../templates/global.php')?>
    <link rel="stylesheet" href="../styles/cart.css">
    <link rel="stylesheet" href="../styles/payment.css">
    <title>RightnowGaming</title>
</head>
<body>
    <header id="header-cart">
        <div>
            <a href="../../index.php"><img src="../img/logo.png" alt="logo" id="img-logo"></a>
        </div>
        <div id="passive-number">
            <span>1</span>
        </div>
        <p id="passive-step">Panier</p>
        <span id="passive-line"></span>
        <div id="passive-number">
            <span>2</span>
        </div>
        <p id="passive-step">Paiement</p>
        <span id="active-line"></span>
        <div id="active-number">
            <span>3</span>
        </div>
        <p id="active-step">Activation du jeu</p>
    </header>
    <main>
        <section id="recap">
            <h1>Félicitations, votre commande est un succès! Un code vous a été envoyé par e-mail avec votre clé de jeu.</h1>
            <a href="../../index.php">Cliquez ici pour revenir à la boutique</a>
        </section>
    </main>
</body>
</html>
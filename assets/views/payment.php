<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include ('../templates/global.php') ?>
    <link rel="stylesheet" href="../styles/payment.css">
    <title>RightnowGaming</title>
</head>
<body>
    <main>
        <div class="infos">
            <section id="billing">
                <h1>Adresse de facturation</h1>
                <form action="payment.php">
                    <input type="text" placeholder="Nom">
                    <input type="text" placeholder="Prénom">
                    <input type="text" name="adress" placeholder="Adresse">
                    <input type="text" placeholder="Code Postal">
                    <input type="text" placeholder="Ville">
                </form>
            </section>
            <section id="resume">
                <h1>Résumé</h1>
                <div class="order">
                    <div class="game">
                        <div class="first-line">
                            <h2>Game_name(to_define)</h2>
                            <p>price(to_define)</p>
                        </div>
                        <div class="second-line">
                            <p>store(to_define)</p>
                        </div>
                    </div>
                    <div class="submit-payment">
                        <div class="total">
                            <h3>TOTAL</h3>
                            <h3>price(to_define)</h3>
                        </div>
                        <button class="pay">Payer</button>
                        <p>En cliquant sur "Payer" je reconnais avoir lu et accepté les termes et conditions, et la politique de confidentialité.</p>
                    </div>
                </div>
            </section>
        </div>
        <section id="payment">
            <div class="payment-form">
                <form action="payment.php">
                    <div>
                        <label for=""></label>
                        <input type="text">
                    </div>
                    <div>
                        <label for=""></label>
                        <input type="text">
                    </div>
                    <div>
                        <label for=""></label>
                        <input type="text">
                    </div>
                    <div>
                        <label for=""></label>
                        <input type="text">
                    </div>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
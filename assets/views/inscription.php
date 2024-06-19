<?php
session_start();

require_once '../../Database.php';
require_once '../Class/User.php';

$error_message = null;

$database = new Database();
$user = new User();

// Établir la connexion
$conn = $database->connect();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $username = $_POST['username'];
    $firstname = $_POST['firstname'];
    $name = $_POST['name'];
    $country = $_POST['country'];

    // Vérifier si la connexion est établie
    if (isset($email, $password, $username) && $conn) {
        // Vérifier si le mot de passe répond aux exigences
        if (strlen($password) < 8 || !preg_match("#[0-9]+#", $password) || !preg_match("#[A-Z]+#", $password) || !preg_match("#[a-z]+#", $password) || !preg_match("/[!@#$%^&*()\-_=+]/", $password)) {
            $error_message = "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
        } else {
            // Hachage du mot de passe
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Requête d'ajout utilisateur dans la BDD
            $user->addUser($username, $email, $hashed_password, $name, $firstname, $country);

            // Rediriger l'utilisateur vers une page de confirmation
            header("Location: connexion.php");
            exit;
        }
    } else {
        echo 'Échec de la connexion.';
    }
}

// Fermer la connexion
$database->disconnect();

// URL de l'API REST Countries
$url = 'https://restcountries.com/v2/all';

// Initialiser une session cURL
$ch = curl_init($url);

// Configurer cURL pour retourner la réponse sous forme de chaîne
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Désactiver la vérification SSL (pour le développement uniquement)
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

// Exécuter la requête cURL
$response = curl_exec($ch);

if ($response === false) {
    die('Erreur cURL : ' . curl_error($ch));
}

// Fermer la session cURL
curl_close($ch);

// Décoder la réponse JSON
$countries = json_decode($response, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die('Erreur de décodage JSON : ' . json_last_error_msg());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include("../../assets/templates/global.php") ?>
    <link rel="stylesheet" href="../styles/authentification.css">
    <title>RightNow Gaming || Inscription</title>
</head>
<body>
    <main>
        <div class="formulaire" id="inscription">
            <img src="../img/logo.png" alt="logo" class="logo">
            <form action="inscription.php" method="POST">
                <div class="champs">
                    <input type="email" placeholder="E-mail" name="email">
                    <input type="password" placeholder="Votre mot de passe" name="password">
                    <input type="text" placeholder="Prénom" name="firstname">
                    <input type="text" placeholder="Nom" name="name">
                    <input type="text" placeholder="Nom d'utilisateur" name="username">
                    <select name="country" id="country">
                        <?php
                        if ($countries) {
                            foreach ($countries as $country) {
                                $countryName = $country['name']; // Nom du pays
                                echo "<option value=\"$countryName\">$countryName</option>";
                            }
                        } else {
                            echo '<option value="">Aucun pays disponible</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="boutons">
                    <div class="conditions">
                        <input type="checkbox" class="agree">
                        <p>J'accepte les <a href="#">conditions de ventes</a> et la <a href="#">politique de confidentialité</a></p>
                    </div>
                    <button class="send">Envoyer</button>
                </div>
            </form>
        </div>
        <div class="wallpaper">
        </div>
    </main>
    <footer>
    </footer>
</body>
</html>


<!-- <section id="inscription">
            <div class="section">
                <form action="inscription.php" method="POST">
                    <h1>Inscrivez Vous!</h1>
                    <input type="email" placeholder="Adresse e-mail" name="email" required>
                    <input type="password" placeholder="Mot de passe" name="password" maxlength="30" required>
                    <?php if($error_message != null){echo ('<p style="color: red; width: 80%">'.$error_message.'</p>');} ?>
                    <input type="text" placeholder="Nom d'utilisateur" name="username" minlength="6" maxlength="15" required>
                    <button class="connect_button" type="submit">
                        <p class="p_connect">M'inscrire</p>
                        <div class="btn_animation"></div>
                    </button>
                </form>
            </div>
        </section> -->

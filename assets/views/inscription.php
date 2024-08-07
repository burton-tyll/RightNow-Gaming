<?php
session_start();

require_once '../../Database.php';
require_once '../Class/User.php';

$error_message = null;
$existant_account = null;
$countryEmptyMessage = null;

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
    $country = $_POST['country'] ?? null;

    // Vérifier si la connexion est établie
    if ($conn) {
        // Vérifier si le mot de passe répond aux exigences
        if (strlen($password) < 8 || !preg_match("#[0-9]+#", $password) || !preg_match("#[A-Z]+#", $password) || !preg_match("#[a-z]+#", $password) || !preg_match("/[!@#$%^&*()\-_=+]/", $password)) {
            $error_message = "Votre mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
        } else {
            // Hachage du mot de passe
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Vérification de l'existence de l'utilisateur
            $userdb = $user->read();
            $userExists = false;

            if(!empty($userdb)){
                foreach($userdb as $thisone){
                    if($thisone['email'] == $email || $thisone['username'] == $username){
                        $userExists = true;
                        $existant_account = 'Un compte est déjà inscrit à cette adresse, ou à ce nom.';
                        break;
                    }
                }
            }

            if (!$userExists) {
                // Requête d'ajout utilisateur dans la BDD
                if(!empty($country)){
                    $user->addUser($username, $email, $hashed_password, $name, $firstname, $country);
                    // Rediriger l'utilisateur vers une page de confirmation
                    header("Location: connexion.php");
                    exit;
                } else{
                    $countryEmptyMessage = 'Merci de bien vouloir sélectionner un pays!';
                }
            }
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
            <a href="../../index.php"><img src="../img/logo.png" alt="logo" class="authentificationlogo"></a>
            <form action="inscription.php" method="POST">
                <p style="color: red; font-size: 1.4rem"><?php echo $existant_account; ?></p>
                <p style="color: red; font-size: 1.4rem"><?php echo $error_message; ?></p>
                <p style="color: red; font-size: 1.4rem"><?php echo $countryEmptyMessage; ?></p>
                <div class="champs">
                    <input type="email" placeholder="E-mail" name="email" required>
                    <input type="password" placeholder="Votre mot de passe" name="password" required>
                    <input type="text" placeholder="Prénom" name="firstname" required>
                    <input type="text" placeholder="Nom" name="name" required>
                    <input type="text" placeholder="Nom d'utilisateur" name="username" required>
                    <select name="country" id="country" required>
                        <option disabled selected>Choisissez un pays</option>
                        <?php
                            foreach ($countries as $country) {
                                $countryName = $country['name']; // Nom du pays
                                echo "<option value=\"$countryName\">$countryName</option>";
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

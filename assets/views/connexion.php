<?php

session_start();

require_once '../../Database.php';
require_once '../Class/User.php';

$error_message = null;
$connexion_error = null;

$database = new Database();
$users = new User($database);

// Établir la connexion
$conn = $database->connect();


if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $username = $_POST['username'];
    $password = $_POST['password'];

    if(isset ($username, $password) && $conn){
        if (strlen($password) < 8 || !preg_match("#[0-9]+#", $password) || !preg_match("#[A-Z]+#", $password) || !preg_match("#[a-z]+#", $password) || !preg_match("/[!@#$%^&*()\-_=+]/", $password)) {
            $error_message = "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
        } else {
            $getUsers = $users->read();
            $validUser = false;

            foreach($getUsers as $user){
                //Gestion du cas ou l'utilisateur est trouvé et administrateur
                if($username == $user['username'] && password_verify($password, $user['password'])){
                    $validUser = true;
                    $admin = $user['admin'];
                    break;
                }
                //Gestion du cas ou l'utilisateur n'est pas trouvé
                else{
                    $connexion_error = "Nom d'utilisateur ou mot de passe incorrect, veuillez réessayer!";
                }
            }
            if($validUser){
                session_start();
                $_SESSION['user'] = $username;
                if($admin == 1){
                    $_SESSION['admin'] = true;
                }
                header('Location: ../../index.php');
                exit;
            }
        }
    } 
}

// Fermer la connexion
$database->disconnect();
?>


<!-----------------------------HTML------------------------------------>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include('../templates/global.php') ?>
    <link rel="stylesheet" href="../styles/authentification.css">
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script src="https://cdn.jsdelivr.net/npm/jwt-decode@3.1.2/build/jwt-decode.min.js"></script>
    <title>RightNow Gaming || Connexion</title>
</head>

<body>
    <main>
        <div class="formulaire" id="connexion">
            <a href="../../index.php"><img src="../img/logo.png" alt="logo" class="authentificationlogo"></a>
            <form action="connexion.php" method="POST" id="connexionForm">
                <div class="external-connexion">
                    <h2>Se connecter</h2>
                    <div class="external-buttons">
                        <button class="socialButton googleButton" id="googleSignInButton"><img src="../img/google.png" alt="googleImage"></button>
                        <button class="socialButton facebookButton"><img src="../img/facebook.png" alt="facebookImage"></button>
                        <button class="socialButton appleButton"><img src="../img/apple.png" alt="appleImage"></button>
                        <button class="socialButton discordButton"><img src="../img/discord.png" alt="discordImage"></button>
                    </div>
                    <div class="separator">
                        <div class="separator-line"></div>
                        <p>ou</p>
                        <div class="separator-line"></div>
                    </div>
                </div>
                <p style="color: red; font-size: 1.4rem"><?php echo $connexion_error ?></p>
                <div class="champs">
                    <input type="text" placeholder="Nom d'utilisateur" name="username" required>
                    <input type="password" placeholder="Votre mot de passe" name="password" required>
                </div>
                <div class="boutons">
                    <button class="send">Envoyer</button>
                    <div class="links">
                        <p><a href="../views/inscription.php">Pas encore de compte?</a></p>
                        <p><a href="">Mot de passe oublié?</a></p>
                    </div>
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

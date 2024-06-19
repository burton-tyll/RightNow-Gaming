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
                if($username == $user['username'] && password_verify($password, $user['password'])){
                    $validUser = true;
                    break;
                } else{
                    $connexion_error = "Nom d'utilisateur ou mot de passe incorrect, veuillez réessayer!";
                }
            }
            if($validUser){
                session_start();
                $_SESSION['user'] = $username;
                header('Location: ./homepage.php');
                exit;
            }
        }
    } else{
        echo 'Veuillez entrer des informations dans le formulaire!';
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
    <title>RightNow Gaming || Connexion</title>
</head>

<body>
    <main>
        <section id="connexion">
            <div class="section">
                <form action="connexion.php" method="POST">
                    <h1>Connectez Vous!</h1>
                    <p style="color: red; font-size: 1.5rem; width: 80%"><?php echo $connexion_error ?></p>
                    <input type="text" placeholder="Nom d'utilisateur" name="username" minlength="6" maxlength="15" required>
                    <input type="password" placeholder="Mot de passe" name="password" maxlength="30" required>
                    <?php if($error_message != null){echo ('<p style="color: red; width: 80%">'.$error_message.'</p>');} ?>
                    <button class="connect_button" type="submit">
                        <p class="p_connect">Me connecter</p>
                        <div class="btn_animation"></div>
                    </button>
                    <a href="inscription.php">Vous n'avez pas de compte? Créez en un ici!</a>
                </form>
            </div>
        </section>
    </main>
    <footer>

    </footer>
</body>

</html>
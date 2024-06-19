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

        // Vérifier si la connexion est établie
        if (isset($email, $password, $username) && $conn) {
            // Vérifier si le mot de passe répond aux exigences
            if (strlen($password) < 8 || !preg_match("#[0-9]+#", $password) || !preg_match("#[A-Z]+#", $password) || !preg_match("#[a-z]+#", $password) || !preg_match("/[!@#$%^&*()\-_=+]/", $password)) {
                $error_message = "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
            } else {
                // Hachage du mot de passe
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Requête d'ajout utilisateur dans la BDD
                $user->addUser($username, $email, $hashed_password);

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
?>


<!-----------------------------HTML------------------------------------>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include("../../assets/templates/global.php") ?>
    <title>RightNow Gaming || Inscription</title>
</head>

<body>
    <main>
        <section id="inscription">
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
        </section>
    </main>
    <footer>

    </footer>
</body>

</html>

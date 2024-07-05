<?php
session_start();
require_once '../../Database.php';
require_once '../Class/User.php';
require_once '../Class/Delivery.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

$db = new Database();
$user = new User();
$delivery = new Delivery($db->getConnection());

$userId = $_SESSION['user_id'];
$userInfo = $user->getUser('id', $userId);
$deliveries = $delivery->getDeliveryByUser($userId);

$updateMessage = "";

//affichage des formulaires
if (isset($_POST['show_email_form'])) {
    $_SESSION['show_email_form'] = !isset($_SESSION['show_email_form']) || !$_SESSION['show_email_form'];
    $_SESSION['show_password_form'] = false;
    $_SESSION['show_address_form'] = false;
} elseif (isset($_POST['show_password_form'])) {
    $_SESSION['show_email_form'] = false;
    $_SESSION['show_password_form'] = !isset($_SESSION['show_password_form']) || !$_SESSION['show_password_form'];
    $_SESSION['show_address_form'] = false;
} elseif (isset($_POST['show_address_form'])) {
    $_SESSION['show_email_form'] = false;
    $_SESSION['show_password_form'] = false;
    $_SESSION['show_address_form'] = !isset($_SESSION['show_address_form']) || !$_SESSION['show_address_form'];
} else {
//Réinitialisation des variables de session à chaque chargement de page pour s'assurer que les formulaires ne restent pas ouverts après une actualisation
    unset($_SESSION['show_email_form']);
    unset($_SESSION['show_password_form']);
    unset($_SESSION['show_address_form']);
}

if (isset($_POST['new_email']) && isset($_POST['current_password'])) {
    $newEmail = $_POST['new_email'];
    $currentPassword = $_POST['current_password'];

    if (password_verify($currentPassword, $userInfo['password'])) {
        if ($user->updateEmail($userId, $newEmail)) {
            $updateMessage = "Email mis à jour avec succès.";
        } else {
            $updateMessage = "Erreur lors de la mise à jour de l'email.";
        }
    } else {
        $updateMessage = "Mot de passe actuel incorrect.";
    }
}

if (isset($_POST['new_password']) && isset($_POST['current_password'])) {
    $newPassword = $_POST['new_password'];
    $currentPassword = $_POST['current_password'];

    if (password_verify($currentPassword, $userInfo['password'])) {
        if ($user->updatePassword($userId, $newPassword)) {
            $updateMessage = "Mot de passe mis à jour avec succès.";
        } else {
            $updateMessage = "Erreur lors de la mise à jour du mot de passe.";
        }
    } else {
        $updateMessage = "Mot de passe actuel incorrect.";
    }
}

if (isset($_POST['adress'])) {
    $newAddress = $_POST['adress'];

    if ($user->updateAddress($userId, $newAddress)) {
        $updateMessage = "Adresse mise à jour avec succès.";
    } else {
        $updateMessage = "Erreur lors de la mise à jour de l'adresse.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/profil.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css">
    <title>Profil</title>
    <?php include('../templates/global.php') ?>
</head>
<body>
    <header>
        <?php include '../templates/header.php'; ?>
    </header>
    <main>
        <section class="profile">
            <div class="avatar">
                <i class="bi bi-person-circle"></i>
                <h1><?php echo htmlspecialchars($userInfo['username']); ?></h1>
            </div>
            <div class="dashboard">
                <i class="bi bi-speedometer2"></i>
                <h2>Tableau de bord</h2>
            </div>
            <h3>Mes achats</h3>
            <table class="table table-dark table-striped">
                <thead>
                    <tr>
                        <th scope="col">Numéro de commande</th>
                        <th scope="col">Jeu</th>
                        <th scope="col">Date</th>
                        <th scope="col">Prix</th>
                        <th scope="col">Statut de la commande</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($deliveries as $delivery): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($delivery['delivery_number']); ?></td>
                            <td><?php echo htmlspecialchars($delivery['game_name']); ?></td>
                            <td><?php echo htmlspecialchars($delivery['created_at']); ?></td>
                            <td><?php echo htmlspecialchars($delivery['total_price']); ?>€</td>
                            <td><?php echo htmlspecialchars($delivery['statut']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <h3>Sécurité du compte</h3>
            <div class="account-security">
                <form action="" method="post">
                    <button class="open-btn" type="submit" name="show_email_form">Changer votre adresse email</button>
                </form>
                <form action="" method="post" class="<?php echo isset($_SESSION['show_email_form']) && $_SESSION['show_email_form'] ? '' : 'hidden'; ?>">
                    <h4>Changer votre adresse email</h4>
                    <label for="new_email">Nouvelle adresse email</label>
                    <input type="email" id="new_email" name="new_email" required>
                    <label for="current_password">Votre mot de passe actuel</label>
                    <input type="password" id="current_password" name="current_password" required>
                    <button type="submit">Valider</button>
                </form>
                <form action="" method="post">
                    <button class="open-btn" type="submit" name="show_password_form">Changer votre mot de passe</button>
                </form>
                <form action="" method="post" class="<?php echo isset($_SESSION['show_password_form']) && $_SESSION['show_password_form'] ? '' : 'hidden'; ?>">
                    <h4>Changer votre mot de passe</h4>
                    <label for="current_password">Mot de passe actuel</label>
                    <input type="password" id="current_password" name="current_password" required>
                    <label for="new_password">Nouveau mot de passe</label>
                    <input type="password" id="new_password" name="new_password" required>
                    <button type="submit">Valider</button>
                </form>
                <form action="" method="post">
                    <button class="open-btn" type="submit" name="show_address_form">Changer votre adresse de livraison</button>
                </form>
                <form action="" method="post" class="<?php echo isset($_SESSION['show_address_form']) && $_SESSION['show_address_form'] ? '' : 'hidden'; ?>">
                    <h4>Changer votre adresse de livraison</h4>
                    <label for="adress">Adresse</label>
                    <input type="text" id="adress" name="adress" required>
                    <button type="submit">Valider</button>
                </form>
            </div>
        </section>
    </main>
    <footer>
        <?php include '../templates/footer.php'; ?>
    </footer>
</body>
</html>

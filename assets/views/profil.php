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
var_dump($userId);
$userInfo = $user->getUser('id', $userId);
var_dump($userInfo); 
$deliveries = $delivery->getDeliveryByUser($userId);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <?php include('../templates/global.php') ?>
</head>
<body>
    <header>
        <?php include '../templates/header.php'; ?>
    </header>
    <main>
        <section class="profile">
            <h1>Profil de <?php echo htmlspecialchars($userInfo['username']); ?></h1>
            <h2>Mes achats</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Numéro de commande</th>
                        <th>Statut de la commande</th>
                        <th>Prix total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($deliveries as $delivery): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($delivery['created_at']); ?></td>
                            <td><?php echo htmlspecialchars($delivery['delivery_number']); ?></td>
                            <td><?php echo htmlspecialchars($delivery['statut']); ?></td>
                            <td><?php echo htmlspecialchars($delivery['total_price']); ?>€</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <h2>Sécurité du compte</h2>
            <div class="account-security">
                <form action="update_email.php" method="post">
                    <h3>Changer votre adresse email</h3>
                    <label for="new_email">Nouvelle adresse email</label>
                    <input type="email" id="new_email" name="new_email" required>
                    <label for="confirm_new_email">Confirmation nouvelle adresse email</label>
                    <input type="email" id="confirm_new_email" name="confirm_new_email" required>
                    <label for="current_password">Votre mot de passe actuel</label>
                    <input type="password" id="current_password" name="current_password" required>
                    <button type="submit">Valider</button>
                </form>
                <form action="update_password.php" method="post">
                    <h3>Changer votre mot de passe</h3>
                    <label for="current_password">Mot de passe actuel</label>
                    <input type="password" id="current_password" name="current_password" required>
                    <label for="new_password">Nouveau mot de passe</label>
                    <input type="password" id="new_password" name="new_password" required>
                    <label for="confirm_new_password">Confirmation nouveau mot de passe</label>
                    <input type="password" id="confirm_new_password" name="confirm_new_password" required>
                    <button type="submit">Valider</button>
                </form>
            </div>
        </section>
    </main>
    <footer>
        <?php include '../assets/templates/footer.php'; ?>
    </footer>
</body>
</html>

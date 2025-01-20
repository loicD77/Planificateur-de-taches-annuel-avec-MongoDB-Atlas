<?php
session_start();
require 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];

    try {
        // Obtenez le gestionnaire MongoDB
        $db = Database::getInstance();
        $manager = $db->getManager();

        // Rechercher l'utilisateur avec l'email
        $query = new MongoDB\Driver\Query(['email' => $email]);
        $rows = $manager->executeQuery('planning.users', $query)->toArray();

        if (!empty($rows)) {
            $user = $rows[0]; // Récupérer le premier résultat

            // Vérifier le mot de passe
            if (password_verify($password, $user->password)) {
                // Stocker les informations dans la session
                $_SESSION['user'] = [
                    'id' => $user->_id,
                    'name' => $user->name,
                    'role' => $user->role
                ];

                // Redirection selon le rôle
                header('Location: ' . ($user->role === 'admin' ? 'admin.php' : 'index.php'));
                exit;
            } else {
                $error = "Mot de passe incorrect.";
            }
        } else {
            $error = "Aucun utilisateur trouvé avec cet email.";
        }
    } catch (MongoDB\Driver\Exception\Exception $e) {
        $error = "Erreur lors de la connexion : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <h1>Connexion</h1>

        <!-- Affichage des messages d'erreur -->
        <?php if (isset($error)): ?>
            <p class="error" style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <!-- Formulaire de connexion -->
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
        </form>

        <!-- Lien vers la page d'inscription -->
        <p>Pas encore de compte ? <a href="register.php">Inscrivez-vous ici</a>.</p>
    </div>
</body>
</html>

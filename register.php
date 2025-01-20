<?php
require 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars(trim($_POST['username']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = htmlspecialchars(trim($_POST['role'])); // Rôle sélectionné dans le formulaire
    try {

        $db = Database::getInstance();
        $manager = $db->getManager();

        // Vérifier si l'utilisateur existe déjà
        $query = new MongoDB\Driver\Query(['_id' => $username]);
        $rows = $manager->executeQuery('planning.users', $query)->toArray();

        if (!empty($rows)) {
            $error = "Le nom d'utilisateur existe déjà.";
        } else {
            // Insérer le nouvel utilisateur avec le rôle
            $bulk = new MongoDB\Driver\BulkWrite();
            $bulk->insert([
                '_id' => $username,
                'name' => $username,
                'email' => $email,
                'password' => $password,
                'role' => $role // Ajouter le rôle (admin ou user)
            ]);
            $manager->executeBulkWrite('planning.users', $bulk);

            $success = "Inscription réussie. Redirection en cours...";
            header("refresh:3;url=login.php");
            exit;
        }
    } catch (MongoDB\Driver\Exception\Exception $e) {
        $error = "Erreur lors de l'inscription : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <h1>Inscription</h1>
        <?php if (isset($error)): ?>
            <p class="error" style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <p class="success" style="color: green;"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Nom d'utilisateur" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <label for="role">Rôle :</label>
            <select id="role" name="role" required>
                <option value="user">Utilisateur</option>
                <option value="admin">Administrateur</option>
            </select>
            <button type="submit">S'inscrire</button>
        </form>
        <p>Déjà inscrit ? <a href="login.php">Connectez-vous ici</a>.</p>
    </div>
</body>
</html>

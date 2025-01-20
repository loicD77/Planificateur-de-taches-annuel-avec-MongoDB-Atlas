<?php
// Informations de connexion
$uri = "mongodb+srv://admin:Lo200177190@cluster0.mongodb.net/planning?retryWrites=true&w=majority";
$username = "admin";
$password = "Lo200177190";
$database = "planning";

try {
    echo "<h2>Test de connexion à MongoDB Atlas</h2>";

    // Vérification des informations de connexion
    echo "<strong>Informations de connexion utilisées :</strong><br>";
    echo "Utilisateur : <strong>$username</strong><br>";
    echo "Mot de passe : <strong>$password</strong><br>";
    echo "URI : <strong>$uri</strong><br>";
    echo "Base de données cible : <strong>$database</strong><br><br>";

    // Test de connexion au fichier connect.php
    require 'connect.php';
    echo "Fichier <strong>connect.php</strong> inclus avec succès.<br>";

    // Vérification de la classe Database
    if (!class_exists('Database')) {
        die("<strong>Erreur :</strong> La classe Database n'est pas définie dans <strong>connect.php</strong>.<br>");
    } else {
        echo "La classe <strong>Database</strong> est bien définie.<br>";
    }

    // Initialisation de la classe Database
    $db = Database::getInstance();
    echo "Instance de la classe Database initialisée avec succès.<br>";

    // Vérification du client MongoDB
    $client = $db->getClient();
    if ($client instanceof MongoDB\Driver\Manager) {
        echo "Client MongoDB récupéré avec succès.<br>";
    } else {
        die("<strong>Erreur :</strong> Le client MongoDB n'est pas valide.<br>");
    }

    // Tester la connexion avec une commande ping
    $command = new MongoDB\Driver\Command(['ping' => 1]);
    $cursor = $client->executeCommand('admin', $command);
    echo "Connexion réussie à MongoDB Atlas : Commande <strong>ping</strong> exécutée avec succès.<br>";

    // Tester l'accès à la base de données
    $query = new MongoDB\Driver\Query([]);
    $cursor = $client->executeQuery("$database.tasks", $query); // Test sur la collection "tasks"
    echo "Accès à la collection <strong>$database.tasks</strong> vérifié avec succès.<br>";

    // Afficher quelques documents si disponibles
    echo "<h3>Exemple de documents dans la collection <strong>tasks</strong> :</h3>";
    foreach ($cursor as $document) {
        echo "<pre>" . json_encode($document, JSON_PRETTY_PRINT) . "</pre>";
        break; // Affiche un seul document pour éviter de surcharger la sortie
    }

} catch (MongoDB\Driver\Exception\Exception $e) {
    echo "<strong>Erreur :</strong> " . $e->getMessage() . "<br>";
} catch (Exception $e) {
    echo "<strong>Erreur générale :</strong> " . $e->getMessage() . "<br>";
}

// Résumé
echo "<h2>Résumé :</h2>";
echo "<ul>";
echo "<li>Utilisateur : <strong>$username</strong></li>";
echo "<li>Mot de passe : <strong>$password</strong></li>";
echo "<li>Cluster : <strong>cluster0.mongodb.net</strong></li>";
echo "<li>Base de données : <strong>$database</strong></li>";
echo "</ul>";
?>

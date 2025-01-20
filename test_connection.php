<?php
require 'connect.php';

try {
    echo "<h1>Test de connexion à MongoDB</h1>";

    // Tester la collection `users`
    echo "<h2>Utilisateurs :</h2>";
    $users = $usersCollection->find();
    foreach ($users as $user) {
        echo "Utilisateur : " . $user['name'] . " (Email : " . $user['email'] . ")<br>";
    }

    // Tester la collection `tasks`
    echo "<h2>Tâches :</h2>";
    $tasks = $tasksCollection->find();
    foreach ($tasks as $task) {
        echo "Tâche : " . $task['description'] . " - Assignée à : " . $task['userId'] . "<br>";
    }
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>

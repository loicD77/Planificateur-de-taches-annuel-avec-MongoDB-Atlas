<?php
require '../connect.php';

header('Content-Type: application/json');

try {
    $db = Database::getInstance();
    $manager = $db->getManager();

    // Récupérer l'année depuis les paramètres GET
    $year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

    // Pipeline pour compter les tâches par utilisateur pour une année donnée
    $pipelineTasksByUser = [
        ['$match' => ['date' => ['$regex' => "^$year"]]], // Filtrer par année
        ['$group' => [
            '_id' => '$userId', // Regrouper par utilisateur
            'taskCount' => ['$sum' => 1], // Compter les tâches
        ]],
        ['$sort' => ['taskCount' => -1]] // Trier par nombre de tâches décroissant
    ];

    // Pipeline pour compter les tâches par humeur pour une année donnée
    $pipelineTasksByMood = [
        ['$match' => ['date' => ['$regex' => "^$year"]]], // Filtrer par année
        ['$group' => [
            '_id' => '$humeur', // Regrouper par humeur
            'moodCount' => ['$sum' => 1], // Compter les tâches
        ]],
        ['$sort' => ['moodCount' => -1]] // Trier par nombre décroissant
    ];

    // Exécution des agrégations pour les tâches par utilisateur
    $statsByUser = $manager->executeCommand('planning', new MongoDB\Driver\Command([
        'aggregate' => 'tasks',
        'pipeline' => $pipelineTasksByUser,
        'cursor' => new stdClass(),
    ]))->toArray();

    // Exécution des agrégations pour les tâches par humeur
    $statsByMood = $manager->executeCommand('planning', new MongoDB\Driver\Command([
        'aggregate' => 'tasks',
        'pipeline' => $pipelineTasksByMood,
        'cursor' => new stdClass(),
    ]))->toArray();

    // Renvoyer les résultats au format JSON
    echo json_encode([
        'success' => true,
        'statsByUser' => array_map(function ($stat) {
            return [
                'userId' => $stat->_id,
                'taskCount' => $stat->taskCount
            ];
        }, $statsByUser),
        'statsByMood' => array_map(function ($stat) {
            return [
                'mood' => $stat->_id,
                'moodCount' => $stat->moodCount
            ];
        }, $statsByMood),
    ]);
} catch (MongoDB\Driver\Exception\Exception $e) {
    // Gérer les erreurs
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
    ]);
}
?>

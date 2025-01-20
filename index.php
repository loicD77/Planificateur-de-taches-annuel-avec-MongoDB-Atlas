<?php
session_start();

// Redirection si l'utilisateur n'est pas connecté
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

require 'connect.php';

$selectedYear = date('Y'); // Année par défaut : année actuelle
$order = 'asc'; // Ordre par défaut : croissant

try {
    $db = Database::getInstance();
    $manager = $db->getManager();

    // Gestion des filtres et tri
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $selectedYear = $_POST['year'] ?? $selectedYear;
        $order = $_POST['order'] ?? $order; // Récupérer l'ordre
    }

    // Définir le tri : 1 pour croissant, -1 pour décroissant
    $sortOrder = $order === 'asc' ? 1 : -1;

    // Charger les tâches pour l'année sélectionnée
    $query = new MongoDB\Driver\Query(
        ['date' => ['$regex' => "^$selectedYear"]],
        ['sort' => ['date' => $sortOrder]]
    );
    $tasks = $manager->executeQuery('planning.tasks', $query)->toArray();

    // Charger les statistiques par utilisateur
    $pipelineUsers = [
        ['$match' => ['date' => ['$regex' => "^$selectedYear"]]],
        ['$group' => [
            '_id' => '$userId',
            'taskCount' => ['$sum' => 1],
        ]],
        ['$sort' => ['taskCount' => -1]]
    ];
    $statsByUser = $manager->executeCommand('planning', new MongoDB\Driver\Command([
        'aggregate' => 'tasks',
        'pipeline' => $pipelineUsers,
        'cursor' => new stdClass(),
    ]))->toArray();

    // Charger les statistiques par humeur
    $pipelineHumeurs = [
        ['$match' => ['date' => ['$regex' => "^$selectedYear"]]],
        ['$group' => [
            '_id' => '$humeur',
            'count' => ['$sum' => 1],
        ]],
        ['$sort' => ['count' => -1]]
    ];
    $statsByHumeur = $manager->executeCommand('planning', new MongoDB\Driver\Command([
        'aggregate' => 'tasks',
        'pipeline' => $pipelineHumeurs,
        'cursor' => new stdClass(),
    ]))->toArray();

    // Statistiques des tâches terminées et non terminées
    $pipelineCompleted = [
        ['$match' => ['date' => ['$regex' => "^$selectedYear"]]],
        ['$group' => [
            '_id' => '$completed',
            'count' => ['$sum' => 1],
        ]]
    ];
    $statsByCompletion = $manager->executeCommand('planning', new MongoDB\Driver\Command([
        'aggregate' => 'tasks',
        'pipeline' => $pipelineCompleted,
        'cursor' => new stdClass(),
    ]))->toArray();
} catch (MongoDB\Driver\Exception\Exception $e) {
    die("Erreur : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planning des Corvées</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header>
        <div class="header-container">
            <h1>Planning des Corvées</h1>
            <div class="user-info">
                <p>
                    Connecté en tant que : 
                    <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong>
                    (<?= $_SESSION['user']['role'] === 'admin' ? 'Admin' : 'Non Admin' ?>)
                </p>
                <a href="logout.php" class="logout-button">Se déconnecter</a>
            </div>
            <div class="theme-toggle-container">
                <button id="theme-toggle" class="theme-toggle">Mode sombre</button>
            </div>
        </div>
    </header>
    <div class="container">
        <!-- Formulaire pour filtrer par année et ordre -->
        <form method="POST" action="" class="year-filter">
            <label for="year">Filtrer par année :</label>
            <select name="year" id="year" onchange="this.form.submit()">
                <?php for ($year = 2020; $year <= date('Y') + 5; $year++): ?>
                    <option value="<?= $year ?>" <?= $selectedYear == $year ? 'selected' : '' ?>><?= $year ?></option>
                <?php endfor; ?>
            </select>
            <label for="order">Ordre :</label>
            <select name="order" id="order" onchange="this.form.submit()">
                <option value="asc" <?= $order === 'asc' ? 'selected' : '' ?>>Croissant</option>
                <option value="desc" <?= $order === 'desc' ? 'selected' : '' ?>>Décroissant</option>
            </select>
        </form>

        <!-- Liste des tâches -->
        <h2>Liste des Corvées (Année : <?= htmlspecialchars($selectedYear) ?>)</h2>
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Utilisateur</th>
                    <th>Humeur</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tasks)): ?>
                    <tr>
                        <td colspan="5">Aucune tâche trouvée pour cette année.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tasks as $task): ?>
                        <tr>
                            <td><?= htmlspecialchars($task->date ?? 'Non spécifié') ?></td>
                            <td><?= htmlspecialchars($task->description ?? 'Non spécifié') ?></td>
                            <td><?= htmlspecialchars($task->userId ?? 'Non spécifié') ?></td>
                            <td><?= htmlspecialchars($task->humeur ?? 'Non spécifié') ?></td>
                            <td><?= isset($task->completed) && $task->completed ? 'Terminée' : 'Non Terminée' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Liste des statistiques -->
        <h2>Statistiques</h2>
        <h3>Nombre de corvées par utilisateur</h3>
        <ul>
            <?php foreach ($statsByUser as $stat): ?>
                <li><?= htmlspecialchars($stat->_id ?? 'Non spécifié') ?> : <?= $stat->taskCount ?> tâche(s)</li>
            <?php endforeach; ?>
        </ul>

        <h3>Nombre de corvées par humeur</h3>
        <ul>
            <?php foreach ($statsByHumeur as $stat): ?>
                <li><?= htmlspecialchars($stat->_id ?? 'Non spécifié') ?> : <?= $stat->count ?> tâche(s)</li>
            <?php endforeach; ?>
        </ul>

        <h3>Nombre de corvées terminées et non terminées</h3>
        <ul>
            <?php foreach ($statsByCompletion as $stat): ?>
                <li><?= $stat->_id ? 'Terminées' : 'Non Terminées' ?> : <?= $stat->count ?> tâche(s)</li>
            <?php endforeach; ?>
        </ul>
    </div>
    <script src="assets/script.js"></script>
</body>
</html>

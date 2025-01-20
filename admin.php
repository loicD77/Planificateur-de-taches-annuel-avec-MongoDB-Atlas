<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
require 'connect.php';

$date = '';
$userId = '';
$description = '';
$humeur = '';
$completed = false;
$isEdit = false;
$message = '';
$selectedYear = date('Y'); // Année par défaut : année actuelle
$order = 'asc'; // Ordre par défaut

try {
    $db = Database::getInstance();
    $manager = $db->getManager();

    // Gestion des actions POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';

        if ($action === 'create') {
            $bulk = new MongoDB\Driver\BulkWrite();
            $bulk->insert([
                '_id' => $_POST['date'],
                'date' => $_POST['date'],
                'userId' => $_POST['userId'],
                'description' => $_POST['description'],
                'completed' => isset($_POST['completed']) && $_POST['completed'] === 'on',
                'humeur' => $_POST['humeur']
            ]);
            $manager->executeBulkWrite('planning.tasks', $bulk);
            $message = "Nouvelle corvée ajoutée avec succès.";
        } elseif ($action === 'update') {
            $bulk = new MongoDB\Driver\BulkWrite();
            $bulk->update(
                ['_id' => $_POST['date']],
                ['$set' => [
                    'userId' => $_POST['userId'],
                    'description' => $_POST['description'],
                    'completed' => isset($_POST['completed']) && $_POST['completed'] === 'on',
                    'humeur' => $_POST['humeur']
                ]]
            );
            $manager->executeBulkWrite('planning.tasks', $bulk);
            $message = "Corvée mise à jour avec succès.";
        } elseif ($action === 'delete') {
            $bulk = new MongoDB\Driver\BulkWrite();
            $bulk->delete(['_id' => $_POST['date']]);
            $manager->executeBulkWrite('planning.tasks', $bulk);
            $message = "Corvée supprimée avec succès.";
        } elseif ($action === 'edit') {
            $date = $_POST['date'];
            $userId = $_POST['userId'];
            $description = $_POST['description'];
            $humeur = $_POST['humeur'];
            $completed = $_POST['completed'] === 'on';
            $isEdit = true;
        } elseif ($action === 'filterYear') {
            $selectedYear = $_POST['year'];
            $order = $_POST['order'] ?? 'asc'; // Récupérer l'ordre
        }
    }

    // Charger les tâches pour l'année sélectionnée
    $sortOrder = $order === 'asc' ? 1 : -1;
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
    <title>Admin - Gestion des corvées</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header>
        <div class="header-container">
            <h1>Gestion des Corvées - Admin</h1>
            <div class="user-info">
                <p>
                    Connecté en tant que : <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong>
                    (Statut : Admin)
                </p>
                <a href="logout.php" class="logout-button">Se déconnecter</a>
            </div>
            <div class="theme-toggle-container">
                <button id="theme-toggle" class="theme-toggle">Mode sombre</button>
            </div>
        </div>
    </header>
    <div class="container">
        <?php if (!empty($message)): ?>
            <p class="success" style="color: green;"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <!-- Formulaire pour filtrer par année et ordre -->
        <form method="POST" action="" class="year-filter">
            <input type="hidden" name="action" value="filterYear">
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

        <!-- Formulaire de création ou modification -->
        <h2><?= $isEdit ? 'Modifier une corvée' : 'Ajouter une nouvelle corvée' ?></h2>
        <form method="POST" action="">
            <input type="hidden" name="action" value="<?= $isEdit ? 'update' : 'create' ?>">
            <label for="date">Date :</label>
            <input type="date" id="date" name="date" value="<?= htmlspecialchars($date) ?>" <?= $isEdit ? 'readonly' : 'required' ?>>
            <label for="userId">Utilisateur :</label>
            <input type="text" id="userId" name="userId" value="<?= htmlspecialchars($userId) ?>" required>
            <label for="description">Description :</label>
            <input type="text" id="description" name="description" value="<?= htmlspecialchars($description) ?>" required>
            <label for="humeur">Humeur :</label>
            <input type="text" id="humeur" name="humeur" value="<?= htmlspecialchars($humeur) ?>" required>
            <label for="completed">Statut :</label>
            <input type="checkbox" id="completed" name="completed" <?= $completed ? 'checked' : '' ?>>
            <button type="submit"><?= $isEdit ? 'Modifier' : 'Ajouter' ?></button>
        </form>

        <!-- Liste des tâches -->
        <h2>Liste des corvées</h2>
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Utilisateur</th>
                    <th>Humeur</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task): ?>
                    <tr>
                        <td><?= htmlspecialchars($task->date ?? 'Non spécifié') ?></td>
                        <td><?= htmlspecialchars($task->description ?? 'Non spécifié') ?></td>
                        <td><?= htmlspecialchars($task->userId ?? 'Non spécifié') ?></td>
                        <td><?= htmlspecialchars($task->humeur ?? 'Non spécifié') ?></td>
                        <td><?= isset($task->completed) && $task->completed ? 'Terminée' : 'Non Terminée' ?></td>
                        <td>
                            <form method="POST" action="" style="display:inline;">
                                <input type="hidden" name="action" value="edit">
                                <input type="hidden" name="date" value="<?= htmlspecialchars($task->_id) ?>">
                                <input type="hidden" name="userId" value="<?= htmlspecialchars($task->userId ?? '') ?>">
                                <input type="hidden" name="description" value="<?= htmlspecialchars($task->description ?? '') ?>">
                                <input type="hidden" name="humeur" value="<?= htmlspecialchars($task->humeur ?? '') ?>">
                                <input type="hidden" name="completed" value="<?= isset($task->completed) && $task->completed ? 'on' : 'off' ?>">
                                <button type="submit" class="edit-button">Modifier</button>
                            </form>
                            <form method="POST" action="" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="date" value="<?= htmlspecialchars($task->_id) ?>">
                                <button type="submit" class="delete-button">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
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

        <h3>Humeurs les plus fréquentes</h3>
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

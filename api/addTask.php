<?php
require '../connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $db = Database::getInstance();
    $tasks = $db->getClient()->selectCollection('planning', 'tasks');

    $tasks->insertOne([
        'date' => $data['date'],
        'description' => $data['description'],
        'userId' => $data['userId'],
        'completed' => false
    ]);

    echo json_encode(['status' => 'success']);
}
?>

<?php
require '../connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $db = Database::getInstance();
    $tasks = $db->getClient()->selectCollection('planning', 'tasks');

    $tasks->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($data['_id'])],
        ['$set' => [
            'description' => $data['description'],
            'completed' => $data['completed']
        ]]
    );

    echo json_encode(['status' => 'success']);
}
?>

<?php
require '../connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $db = Database::getInstance();
    $tasks = $db->getClient()->selectCollection('planning', 'tasks');

    $tasks->deleteOne(['_id' => new MongoDB\BSON\ObjectId($data['_id'])]);

    echo json_encode(['status' => 'success']);
}
?>

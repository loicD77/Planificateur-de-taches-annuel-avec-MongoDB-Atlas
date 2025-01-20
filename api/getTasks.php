<?php
require '../connect.php';

$year = $_GET['year'];

try {
    $tasks = $collectionTasks->find(['date' => new MongoDB\BSON\Regex("^$year")]);
    echo json_encode(iterator_to_array($tasks));
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>

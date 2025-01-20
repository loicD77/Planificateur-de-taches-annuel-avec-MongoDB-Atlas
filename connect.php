<?php
class Database {
    private static $instance = null;
    private $client;

    private function __construct() {
        try {
            // URI avec les informations correctes
            $this->client = new MongoDB\Driver\Manager("mongodb+srv://admin:Lo200177190@planningcluster.iaa0u.mongodb.net/?retryWrites=true&w=majority&appName=PlanningCluster");
        } catch (MongoDB\Driver\Exception\Exception $e) {
            die("Erreur de connexion à MongoDB : " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getClient() {
        return $this->client;
    }

    // Ajout de la méthode getManager()
    public function getManager() {
        return $this->client;
    }
}
?>

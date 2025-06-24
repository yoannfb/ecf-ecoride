<?php
namespace App\Repository;

require_once __DIR__ . '/../../vendor/autoload.php'; // charge MongoDB via Composer
use MongoDB\Client;

class MongoLogger {
    public function logRecherche($depart, $arrivee, $ip) {
        try {
            // Connexion à MongoDB (localhost via Docker)
            $client = new Client("mongodb://mongo:27017");
            $collection = $client->ecoride->recherches;

            // Insertion d’un document dans la collection
            $collection->insertOne([
                'depart' => $depart,
                'arrivee' => $arrivee,
                'date' => date('Y-m-d H:i:s'),
                'ip' => $ip
            ]);
        } catch (\Exception $e) {
            file_put_contents("mongo_error.log", $e->getMessage(), FILE_APPEND);
        }
    }
}

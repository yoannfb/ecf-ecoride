<?php
require_once __DIR__ . '/vendor/autoload.php'; // Charge Composer

use MongoDB\Client;

try {
    // Connexion au service Mongo
    $mongo = new Client("mongodb://mongo:27017");

    // Sélectionne la collection
    $collection = $mongo->ecoride->recherches;

    // Prépare les données
    $data = [
        'depart' => $_GET['depart'] ?? null,
        'arrivee' => $_GET['arrivee'] ?? null,
        'date' => date('Y-m-d H:i:s'),
        'ip' => $_SERVER['REMOTE_ADDR']
    ];

    // Insertion dans Mongo
    $collection->insertOne($data);

    // Pour debug visuel
    echo "<script>console.log('✔ log_mongo.php exécuté');</script>";

} catch (Exception $e) {
    file_put_contents("mongo_error.log", $e->getMessage(), FILE_APPEND);
}


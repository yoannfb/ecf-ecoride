<?php
// Active le chargement automatique de Composer (MongoDB, classes PHP…)
require_once __DIR__ . '/../vendor/autoload.php';

// Charge manuellement les classes de ton projet (si pas encore en PSR-4 dans le composer.json)
// require_once __DIR__ . '/../app/Controller/RechercheController.php';
// require_once __DIR__ . '/../app/Service/RechercheService.php';
// require_once __DIR__ . '/../app/Repository/TrajetRepository.php';
// require_once __DIR__ . '/../app/Repository/MongoLogger.php';


use App\Controller\RechercheController;

// Exécution du contrôleur
$controller = new RechercheController();
$controller->handleSearch();

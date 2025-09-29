<?php
namespace App\Controller;

use App\Service\RechercheService;
use App\Repository\TrajetRepository;
use App\Repository\MongoLogger;



class RechercheController {
    private $rechercheService;

    public function __construct() {
        // On instancie le service qui gère la logique de recherche
        $pdo = new \PDO('mysql:host=db;dbname=EcoRide;charset=utf8', 'root', 'root');
        $repo = new TrajetRepository($pdo);
        $logger = new MongoLogger();
        $this->rechercheService = new RechercheService($repo, $logger);
    }

    public function handleSearch() {
        // On récupère les paramètres de la requête GET
        $depart = $_GET['depart'] ?? '';
        $arrivee = $_GET['arrivee'] ?? '';
        $date = $_GET['date'] ?? '';

        // On demande au service de chercher les trajets
        $results = $this->rechercheService->chercherTrajets($depart, $arrivee, $date);

        // On retourne une réponse JSON
        header('Content-Type: application/json');
        echo json_encode($results);
    }
}

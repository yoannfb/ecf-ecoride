<?php
namespace App\Controller;

use App\Service\RechercheService;

class RechercheController {
    private $service;

    public function __construct() {
        // On instancie le service qui gère la logique de recherche
        $this->service = new RechercheService();
    }

    public function handleSearch() {
        // On récupère les paramètres de la requête GET
        $depart = $_GET['depart'] ?? '';
        $arrivee = $_GET['arrivee'] ?? '';
        $date = $_GET['date'] ?? '';

        // On demande au service de chercher les trajets
        $results = $this->service->chercherTrajets($depart, $arrivee, $date);

        // On retourne une réponse JSON
        header('Content-Type: application/json');
        echo json_encode($results);
    }
}

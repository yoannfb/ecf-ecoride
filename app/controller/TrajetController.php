<?php
namespace App\Controller;

use App\Service\TrajetService;

class TrajetController {
    private $service;

    public function __construct() {
        $this->service = new TrajetService();
    }

    public function rechercher($depart, $arrivee) {
        return $this->service->rechercherTrajets($depart, $arrivee);
    }

    public function creer($donnees) {
        return $this->service->ajouterTrajet($donnees);
    }
}

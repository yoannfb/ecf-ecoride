<?php
namespace App\Service;

use App\Repository\TrajetRepository;

class TrajetService {
    private $repo;

    public function __construct() {
        $this->repo = new TrajetRepository();
    }

    public function rechercherTrajets($depart, $arrivee) {
        return $this->repo->trouverParLieu($depart, $arrivee);
    }

    public function ajouterTrajet($donnees) {
        return $this->repo->insererTrajet($donnees);
    }
}

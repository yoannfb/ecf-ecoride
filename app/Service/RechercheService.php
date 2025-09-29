<?php

namespace App\Service;

use App\Repository\TrajetRepository;
use App\Repository\MongoLogger;

class RechercheService {
    private $repo;
    private $logger;

    public function __construct(TrajetRepository $repo, MongoLogger $logger) {
        $this->repo = $repo;
        $this->logger = $logger;
    }

    public function chercherTrajets($depart, $arrivee, $date) {
        // On récupère les trajets depuis MySQL
        $results = $this->repo->trouverParLieu($depart, $arrivee, $date);

        // On loggue la recherche dans MongoDB
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $this->logger->logRecherche($depart, $arrivee, $ip);

        return $results;
    }
}


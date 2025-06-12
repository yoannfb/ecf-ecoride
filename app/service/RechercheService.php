<?php
namespace App\Service;

use App\Repository\TrajetRepository;
use App\Repository\MongoLogger;

class RechercheService {
    private $repository;
    private $logger;

    public function __construct() {
        // On crée une instance du repository pour accéder à MySQL
        $this->repository = new TrajetRepository();

        // On crée une instance du logger pour MongoDB
        $this->logger = new MongoLogger();
    }

    public function chercherTrajets($depart, $arrivee, $date) {
        // On récupère les trajets depuis MySQL
        $results = $this->repository->findTrajets($depart, $arrivee, $date);

        // On loggue la recherche dans MongoDB
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $this->logger->logRecherche($depart, $arrivee, $ip);

        return $results;
    }
}

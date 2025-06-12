<?php
namespace App\Repository;

use PDO;

class TrajetRepository {
    private $pdo;

    public function __construct() {
        // On inclut la config de la base de données (PDO)
        require __DIR__ . '/../../config/db.php';
        $this->pdo = $pdo;
    }

    public function findTrajets($depart, $arrivee, $date) {
        // Requête SQL avec filtres
        $sql = "SELECT * FROM trajets WHERE depart LIKE :depart AND arrivee LIKE :arrivee";
        $params = [
            ':depart' => "%$depart%",
            ':arrivee' => "%$arrivee%"
        ];

        // Si une date est fournie, on l'ajoute à la requête
        if (!empty($date)) {
            $sql .= " AND DATE(date_heure_depart) = :date";
            $params[':date'] = $date;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

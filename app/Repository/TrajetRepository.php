<?php
namespace App\Repository;

use PDO;

class TrajetRepository {
    private $pdo;

    public function __construct() {
        require __DIR__ . '/../../includes/db.php';
        $this->pdo = $pdo;
    }

    public function trouverParLieu($depart, $arrivee, $date = null) {
    $sql = "SELECT * FROM trajets WHERE adresse_depart LIKE :depart AND adresse_arrivee LIKE :arrivee";
    $params = [
        ':depart' => "%$depart%",
        ':arrivee' => "%$arrivee%"
    ];

    if (!empty($date)) {
        $sql .= " AND DATE(date_depart) = :date";
        $params[':date'] = $date;
    }

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    public function insererTrajet($donnees) {
        $sql = "INSERT INTO trajets (ville_depart, ville_arrivee, date_depart, heure_depart, places_disponibles, prix, conducteur_id)
                VALUES (:depart, :arrivee, :date, :heure, :places, :prix, :conducteur_id)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':depart' => $donnees['ville_depart'],
            ':arrivee' => $donnees['ville_arrivee'],
            ':date' => $donnees['date_depart'],
            ':heure' => $donnees['heure_depart'],
            ':places' => $donnees['places_disponibles'],
            ':prix' => $donnees['prix'],
            ':conducteur_id' => $donnees['conducteur_id']
        ]);
    }
}


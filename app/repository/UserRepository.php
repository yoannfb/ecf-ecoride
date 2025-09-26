<?php
namespace App\Repository;

use PDO;
use PDOException;

class UserRepository {
    private $db;

    public function __construct() {
<<<<<<< HEAD
        // Connexion PDO à adapter selon ta config
        $host = 'db';
        $dbname = 'db';
        $username = 'root';
        $password = '';
=======
    // Utilise la connexion centralisée (Docker)
    require_once __DIR__ . '/../../includes/db.php'; // définit $pdo (host=db, EcoRide, ecoride_user)
    $this->db = $pdo;
}
>>>>>>> 05eb1de (maj ensemble du projet)


    public function trouverParEmail($email) {
        $sql = "SELECT * FROM utilisateurs WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC); // false si non trouvé
    }

    public function ajouterUtilisateur($email, $motDePasseHash) {
        $sql = "INSERT INTO utilisateurs (email, mot_de_passe) VALUES (:email, :mot_de_passe)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'email' => $email,
            'mot_de_passe' => $motDePasseHash
        ]);
    }
}

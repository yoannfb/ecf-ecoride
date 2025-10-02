<?php
namespace App\Service;

use App\Repository\UserRepository;

class UtilisateurService {
    private $repo;

    public function __construct() {
        $this->repo = new UserRepository();
    }

    public function verifierConnexion(string $email, string $motDePasse): bool
{
    // charge la connexion PDO
    require __DIR__ . '/../../includes/db.php'; // adapte le chemin si besoin

    // récupère le hash + flag suspendu depuis la BDD
    $stmt = $pdo->prepare("SELECT id, mot_de_passe, suspendu FROM utilisateurs WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user) {
        return false; // email inconnu
    }

    // bloque immédiatement si le compte est suspendu
    if (isset($user['suspendu']) && (int)$user['suspendu'] === 1) {
        return false;
    }

    // vérifie le mot de passe
    return password_verify($motDePasse, $user['mot_de_passe']);
}


    public function inscrireUtilisateur($email, $motDePasse) {
        $existe = $this->repo->trouverParEmail($email);
        if ($existe) {
            return false;
        }

        $hash = password_hash($motDePasse, PASSWORD_DEFAULT);
        return $this->repo->ajouterUtilisateur($email, $hash);
    }
}

<?php
namespace App\Service;

use App\Repository\UserRepository;

class UtilisateurService {
    private $repo;

    public function __construct() {
        $this->repo = new UserRepository();
    }

    public function verifierConnexion($email, $motDePasse) {
        $utilisateur = $this->repo->trouverParEmail($email);
        if ($utilisateur && password_verify($motDePasse, $utilisateur['mot_de_passe'])) {
            return true;
        }
        return false;
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

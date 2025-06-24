<?php
namespace App\Controller;

use App\Service\UtilisateurService;

class UtilisateurController {
    private $service;

    public function __construct() {
        $this->service = new UtilisateurService();
    }

    public function login($email, $motDePasse) {
        if ($this->service->verifierConnexion($email, $motDePasse)) {
            $_SESSION['email'] = $email;
            header("Location: espace_utilisateur.php");
            exit;
        } else {
            echo "Identifiants invalides";
        }
    }

    public function register($email, $motDePasse) {
        if ($this->service->inscrireUtilisateur($email, $motDePasse)) {
            echo "Inscription réussie";
        } else {
            echo "Utilisateur déjà existant";
        }
    }

    public function logout() {
        session_destroy();
        header("Location: index.php");
        exit;
    }
}

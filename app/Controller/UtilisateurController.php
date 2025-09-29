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
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION['email'] = $email;
        header('Location: /espace_utilisateur.php');
        exit;
    }
    // Échec
    header('Location: /connexion.php?err=1');
    exit;
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

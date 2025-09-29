<?php
namespace App\Controller;

use App\Service\UtilisateurService;
use App\Repository\UserRepository;

class UtilisateurController
{
    private UtilisateurService $service;

    public function __construct()
    {
        $this->service = new UtilisateurService();
    }

    public function login(string $email, string $motDePasse): void
    {
        if ($this->service->verifierConnexion($email, $motDePasse)) {
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }

            // 🔽 Récupère l'utilisateur pour obtenir son id
            $repo = new UserRepository();
            $user = $repo->trouverParEmail($email);

            // 🔽 Alimente la session avec ce que l'espace utilisateur attend
            $_SESSION['user_id'] = $user['id'] ?? null;
            $_SESSION['email']   = $user['email'] ?? $email;

            // Sécurité: si pour une raison quelconque l'id manque, retourne au login
            if (empty($_SESSION['user_id'])) {
                header('Location: /connexion.php?err=1');
                exit;
            }

            header('Location: /espace_utilisateur.php');
            exit;
        }

        // Échec d’authentification
        header('Location: /connexion.php?err=1');
        exit;
    }
}

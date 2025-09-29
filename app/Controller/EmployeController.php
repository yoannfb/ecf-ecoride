<?php
namespace App\Controller;

use App\Service\EmployeService;

class EmployeController
{
    private EmployeService $service;
    public function __construct() { $this->service = new EmployeService(); }

    private function requireEmploye(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $role = $_SESSION['role'] ?? 'user';
        if (empty($_SESSION['user_id']) || !in_array($role, ['employe','admin'], true)) {
            header('Location: /connexion.php?err=auth'); exit;
        }
    }

    public function dashboard(): void
    {
        $this->requireEmploye();
        $avis = $this->service->listerAvisEnAttente();
        $incidents = $this->service->listerIncidents();     // ← incidents
        require __DIR__ . '/../../templates/employe/dashboard.php';
    }

    // ------- Avis -------
    public function approuverAvis(int $id): void { $this->requireEmploye(); $this->service->approuverAvis($id); header('Location: /espace_employe.php?ok=1'); }
    public function refuserAvis(int $id): void   { $this->requireEmploye(); $this->service->refuserAvis($id);   header('Location: /espace_employe.php?ok=1'); }

    // ------- Incidents -------
    public function majIncident(int $id, string $statut): void
    {
        $this->requireEmploye();
        $this->service->changerStatutIncident($id, $statut);
        header('Location: /espace_employe.php?ok=1');
    }
}


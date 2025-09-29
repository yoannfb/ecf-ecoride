<?php
namespace App\Service;

use App\Repository\EmployeRepository;

class EmployeService
{
    private EmployeRepository $repo;
    public function __construct() { $this->repo = new EmployeRepository(); }

    // Avis
    public function listerAvisEnAttente(): array { return $this->repo->getAvisEnAttente(); }
    public function approuverAvis(int $id): bool { return $this->repo->validerAvis($id); }
    public function refuserAvis(int $id): bool   { return $this->repo->refuserAvis($id); }

    // Incidents
    public function listerIncidents(): array { return $this->repo->getIncidents(); }
    public function changerStatutIncident(int $id, string $statut): bool { return $this->repo->majStatutIncident($id,$statut); }
}

<?php
namespace App\Repository;

class EmployeRepository
{
    private \PDO $db;
    public function __construct()
    {
        require __DIR__ . '/../../includes/db.php';
        $this->db = $pdo;
    }

    // ---------- AVIS ----------
    public function getAvisEnAttente(): array
    {
        $sql = "SELECT a.id, a.note, a.commentaire, a.statut,
                       t.id AS trajet_id, t.depart_ville, t.arrivee_ville, t.date_depart, t.date_arrivee,
                       u.pseudo AS auteur_pseudo, u.email AS auteur_email
                  FROM avis a
                  JOIN trajets t      ON t.id = a.trajet_id
                  JOIN utilisateurs u ON u.id = a.auteur_id
                 WHERE a.statut = 'en attente'
                 ORDER BY a.id DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function validerAvis(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE avis SET statut = 'approuve' WHERE id = :id");
        return $stmt->execute(['id'=>$id]);
    }
    public function refuserAvis(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE avis SET statut = 'refuse' WHERE id = :id");
        return $stmt->execute(['id'=>$id]);
    }

    // ---------- INCIDENTS ----------
    // NOTE: si tu n'as PAS encore de table 'incidents', ces méthodes renvoient vide / no-op proprement.
    public function incidentsTableExiste(): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'incidents'");
        $stmt->execute();
        return (bool)$stmt->fetchColumn();
    }

    public function getIncidents(): array
    {
        if (!$this->incidentsTableExiste()) return [];
        $sql = "SELECT i.id, i.description, i.statut, i.created_at,
                       t.id AS trajet_id, t.depart_ville, t.arrivee_ville, t.date_depart, t.date_arrivee,
                       uc.pseudo AS chauffeur_pseudo, uc.email AS chauffeur_email,
                       up.pseudo AS passager_pseudo, up.email AS passager_email
                  FROM incidents i
                  JOIN trajets t  ON t.id = i.covoiturage_id
                  JOIN utilisateurs uc ON uc.id = i.conducteur_id
                  JOIN utilisateurs up ON up.id = i.passager_id
                 ORDER BY i.created_at DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function majStatutIncident(int $id, string $statut): bool
    {
        if (!$this->incidentsTableExiste()) return false;
        $allowed = ['ouvert','en_cours','resolu'];
        if (!in_array($statut, $allowed, true)) return false;
        $stmt = $this->db->prepare("UPDATE incidents SET statut=:s WHERE id=:id");
        return $stmt->execute(['s'=>$statut,'id'=>$id]);
    }
}

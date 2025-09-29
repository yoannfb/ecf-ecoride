<?php
// public/espace_employe.php — tout-en-un

require __DIR__ . '/../vendor/autoload.php';

use App\Service\EmployeService;

// --- Session & garde employé ---
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$role = $_SESSION['role'] ?? 'user';
if (empty($_SESSION['user_id']) || !in_array($role, ['employe','admin'], true)) {
    header('Location: /connexion.php?err=auth'); exit;
}

$service = new EmployeService();

// --- Router minimal par query string ---
$action = $_GET['action'] ?? 'dashboard';

switch ($action) {
    case 'valider':
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) $service->approuverAvis($id);
        header('Location: /espace_employe.php?ok=1'); exit;

    case 'refuser':
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) $service->refuserAvis($id);
        header('Location: /espace_employe.php?ok=1'); exit;

    case 'incident_statut':
        $id = (int)($_GET['id'] ?? 0);
        $statut = $_GET['statut'] ?? 'ouvert';
        if ($id > 0) $service->changerStatutIncident($id, $statut);
        header('Location: /espace_employe.php?ok=1'); exit;

    default:
        // Charger les données pour le rendu
        $avis = $service->listerAvisEnAttente();
        $incidents = $service->listerIncidents();
        break;
}
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<div class="wrap">
  <h1>Espace employé</h1>
  <?php if (!empty($_GET['ok'])): ?><p class="ok">Action effectuée.</p><?php endif; ?>

  <h2>Avis en attente</h2>
  <?php if (empty($avis)): ?>
    <p>Aucun avis en attente.</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Trajet</th>
          <th>Auteur</th>
          <th>Note</th>
          <th>Commentaire</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($avis as $a): ?>
        <tr>
          <td><?= (int)$a['id'] ?></td>
          <td>
            #<?= (int)$a['trajet_id'] ?>
            <?php if (!empty($a['depart_ville']) || !empty($a['arrivee_ville'])): ?>
              — <?= htmlspecialchars($a['depart_ville'] ?? '') ?> ➜ <?= htmlspecialchars($a['arrivee_ville'] ?? '') ?><br>
              <small><?= htmlspecialchars($a['date_depart'] ?? '') ?> → <?= htmlspecialchars($a['date_arrivee'] ?? '') ?></small>
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($a['auteur_pseudo'] ?? '') ?><br><small><?= htmlspecialchars($a['auteur_email'] ?? '') ?></small></td>
          <td><?= (int)$a['note'] ?>/5</td>
          <td><?= nl2br(htmlspecialchars($a['commentaire'] ?? '')) ?></td>
          <td class="actions">
            <a href="/espace_employe.php?action=valider&id=<?= (int)$a['id'] ?>">Valider</a>
            <a href="/espace_employe.php?action=refuser&id=<?= (int)$a['id'] ?>">Refuser</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <hr>

  <h2>Trajets mal passés (incidents)</h2>
  <?php if (empty($incidents)): ?>
    <p>Aucun incident à afficher.</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Trajet</th>
          <th>Chauffeur</th>
          <th>Passager</th>
          <th>Description</th>
          <th>Statut</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($incidents as $i): ?>
        <tr>
          <td><?= (int)$i['id'] ?></td>
          <td>
            #<?= (int)$i['trajet_id'] ?> — <?= htmlspecialchars($i['depart_ville'] ?? '') ?> ➜ <?= htmlspecialchars($i['arrivee_ville'] ?? '') ?><br>
            <small><?= htmlspecialchars($i['date_depart'] ?? '') ?> → <?= htmlspecialchars($i['date_arrivee'] ?? '') ?></small>
          </td>
          <td><?= htmlspecialchars($i['chauffeur_pseudo'] ?? '') ?><br><small><?= htmlspecialchars($i['chauffeur_email'] ?? '') ?></small></td>
          <td><?= htmlspecialchars($i['passager_pseudo'] ?? '') ?><br><small><?= htmlspecialchars($i['passager_email'] ?? '') ?></small></td>
          <td><?= nl2br(htmlspecialchars($i['description'] ?? '')) ?></td>
          <td><?= htmlspecialchars($i['statut'] ?? '') ?></td>
          <td class="actions">
            <a href="/espace_employe.php?action=incident_statut&id=<?= (int)$i['id'] ?>&statut=ouvert">Ouvrir</a>
            <a href="/espace_employe.php?action=incident_statut&id=<?= (int)$i['id'] ?>&statut=en_cours">En cours</a>
            <a href="/espace_employe.php?action=incident_statut&id=<?= (int)$i['id'] ?>&statut=resolu">Résolu</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
</body>
</html>



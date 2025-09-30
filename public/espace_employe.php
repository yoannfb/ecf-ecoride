<?php
// public/espace_employe.php — tout-en-un (affichage + actions)
require __DIR__ . '/../vendor/autoload.php';

use App\Service\EmployeService;

if (session_status() !== PHP_SESSION_ACTIVE) session_start();
error_log('EMPLOYE id='.($_SESSION['user_id']??'').' role='.($_SESSION['role']??''));

$role = strtolower(trim($_SESSION['role'] ?? ''));
//$role = $_SESSION['role'] ?? 'user';
if (empty($_SESSION['user_id']) || !in_array($role, ['employe','admin'], true)) {
    header('Location: /connexion.php?err=auth'); exit;
}

$service = new EmployeService();

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
        $avis = $service->listerAvisEnAttente();
        $incidents = $service->listerIncidents();
        break;
}

require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<style>
    .user {
        background-color: #F7F6CF;
        font-family: EB Garamond;
    }
    .container {
        max-width: 1600px;
        margin: 0 auto ;
        padding: 0 20px 0;
    }
    h1 {
        text-transform: uppercase;
        color: black;
        font-weight: 900;
        color: transparent;
        font-size: 0px;
    }
    h1 span {
        display: inline-block;
        position: relative;
        overflow: hidden;
        font-size: clamp(20px, 8vw, 60px);
        border-radius: 30px;
    }
    h1 span::after {
        content:"";
        display: block;
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        transform: translateX(-100%);
        background: rgba(40, 167, 69, 1);
    }
    h1:nth-child(1) {
        font-weight: 300;
        animation: txt-appearance 0s 1s
        forwards;
    }
    h1:nth-child(1) span::after {
        background: rgba(40, 167, 69, 1);
        animation: slide-in 0.75s ease-out forwards,
        slide-out 0.75s 1s ease-out forwards;
    }
    @keyframes slide-in {
        100% {
            transform: translateX(0%);
        }
    }
    @keyframes slide-out {
        100% {
            transform: translateX(100%);
        }
    }
    @keyframes txt-appearance {
        100% {
            color: black;
        }
    }


</style>

<div class="user p-5">
    <div class="container">
        <h1>
            <span>
                Espace employé
            </span>
        </h1>
    </div>
<?php if (!empty($_GET['ok'])): ?><p class="ok">Action effectuée.</p><?php endif; ?>

    <div class="card mb-4">
        <div class="card-header info">
            Avis en attente
        </div>
    </div>
<?php if (empty($avis)): ?>
  <p>Aucun avis en attente.</p>
<?php else: ?>
  <table><thead><tr><th>#</th><th>Trajet</th><th>Auteur</th><th>Note</th><th>Commentaire</th><th>Actions</th></tr></thead><tbody>
  <?php foreach ($avis as $a): ?>
    <tr>
      <td><?= (int)$a['id'] ?></td>
      <td>#<?= (int)$a['trajet_id'] ?><?php if (!empty($a['depart_ville']) || !empty($a['arrivee_ville'])): ?> — <?= htmlspecialchars($a['depart_ville'] ?? '') ?> ➜ <?= htmlspecialchars($a['arrivee_ville'] ?? '') ?><br><small><?= htmlspecialchars($a['date_depart'] ?? '') ?> → <?= htmlspecialchars($a['date_arrivee'] ?? '') ?></small><?php endif; ?></td>
      <td><?= htmlspecialchars($a['auteur_pseudo'] ?? '') ?><br><small><?= htmlspecialchars($a['auteur_email'] ?? '') ?></small></td>
      <td><?= (int)$a['note'] ?>/5</td>
      <td><?= nl2br(htmlspecialchars($a['commentaire'] ?? '')) ?></td>
      <td class="actions">
        <a href="/espace_employe.php?action=valider&id=<?= (int)$a['id'] ?>">Valider</a>
        <a href="/espace_employe.php?action=refuser&id=<?= (int)$a['id'] ?>">Refuser</a>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody></table>
<?php endif; ?>

<hr>

    <div class="card mb-4">
        <div class="card-header info">
            Trajets mal passés (incidents)
        </div>
    </div>



<?php if (empty($incidents)): ?>
  <p>Aucun incident à afficher.</p>
<?php else: ?>
  <table><thead><tr><th>#</th><th>Trajet</th><th>Chauffeur</th><th>Passager</th><th>Description</th><th>Statut</th><th>Actions</th></tr></thead><tbody>
  <?php foreach ($incidents as $i): ?>
    <tr>
      <td><?= (int)$i['id'] ?></td>
      <td>#<?= (int)$i['trajet_id'] ?> — <?= htmlspecialchars($i['depart_ville'] ?? '') ?> ➜ <?= htmlspecialchars($i['arrivee_ville'] ?? '') ?><br><small><?= htmlspecialchars($i['date_depart'] ?? '') ?> → <?= htmlspecialchars($i['date_arrivee'] ?? '') ?></small></td>
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
  </tbody></table>
<?php endif; ?>

    <div class="mt-4 p-2">
        <a href="index.php" class="btn btn-outline-secondary">Retour à l'accueil</a>
        <a href="logout.php" class="btn btn-outline-danger">Se déconnecter</a>
    </div>
</div>

<?php require_once '../includes/footer.php'; // Inclusion du footer; ?>


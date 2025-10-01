<?php
// public/espace_admin.php — Espace Admin tout-en-un (actions + vues + JSON charts)

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../includes/db.php';

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// --- Garde d'accès : admin only ---
$role = strtolower(trim($_SESSION['role'] ?? ''));
if (empty($_SESSION['user_id']) || $role !== 'admin') {
    header('Location: /connexion.php?err=auth'); exit;
}

// -------------------------
// Helpers & Requêtes SQL
// -------------------------
function colonneExiste(PDO $db, string $table, string $col): bool {
    $stmt = $db->prepare("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :t AND column_name = :c");
    $stmt->execute(['t'=>$table, 'c'=>$col]);
    return (bool)$stmt->fetchColumn();
}

// Créer un employé
function admin_creer_employe(PDO $db, string $email, string $pseudo, string $plainPwd): bool {
    $hash = password_hash($plainPwd, PASSWORD_DEFAULT);
    $sql = "INSERT INTO utilisateurs (pseudo, email, mot_de_passe, role, credits, suspendu)
            VALUES (:p, :e, :h, 'employe', 100, 0)
            ON DUPLICATE KEY UPDATE mot_de_passe = VALUES(mot_de_passe), role = 'employe'";
    return $db->prepare($sql)->execute(['p'=>$pseudo,'e'=>$email,'h'=>$hash]);
}

// Lister utilisateurs/employés pour gestion
function admin_lister_comptes(PDO $db): array {
    $sql = "SELECT id, pseudo, email, role, credits, suspendu FROM utilisateurs ORDER BY role, id DESC";
    return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

// Suspendre / réactiver
function admin_set_suspendu(PDO $db, int $id, int $flag): bool {
    $stmt = $db->prepare("UPDATE utilisateurs SET suspendu=:s WHERE id=:i");
    return $stmt->execute(['s'=>$flag, 'i'=>$id]);
}

// Total crédits gagnés par la plateforme
// 1) Si table/colonne 'commission' existe, on la somme ; sinon on approx (2 crédits * trajets terminés)
function admin_total_credits_plateforme(PDO $db): int {
    // Essaie commission dans 'trajets'
    if (colonneExiste($db, 'trajets', 'commission')) {
        $v = $db->query("SELECT COALESCE(SUM(commission),0) FROM trajets")->fetchColumn();
        return (int)$v;
    }
    // Sinon approx via 2 crédits * nb trajets terminés (ou simplement 2 * total trajets si pas de statut)
    $hasStatut = colonneExiste($db, 'trajets', 'statut');
    $hasDate   = colonneExiste($db, 'trajets', 'date_depart') ? 'date_depart' : (colonneExiste($db,'trajets','created_at') ? 'created_at' : null);

    if ($hasStatut) {
        $sql = "SELECT COUNT(*) FROM trajets WHERE statut IN ('termine','terminé','fini','done')";
    } else {
        $sql = "SELECT COUNT(*) FROM trajets";
    }
    $nb = (int)$db->query($sql)->fetchColumn();
    return 2 * $nb; // US9 : la plateforme prend 2 crédits / covoiturage
}

// Stats JSON (graphes)
function admin_stats(PDO $db): array {
    // Déterminer colonne date
    $dateCol = null;
    foreach (['date_depart','created_at','date'] as $c) {
        if (colonneExiste($db,'trajets',$c)) { $dateCol = $c; break; }
    }
    if (!$dateCol) {
        return ['tripsPerDay'=>[], 'creditsPerDay'=>[]];
    }
    $hasStatut = colonneExiste($db,'trajets','statut');

    // 1) Covoiturages / jour (7 à 30 derniers jours selon dispo)
    $sqlTrips = "SELECT DATE($dateCol) d, COUNT(*) n
                 FROM trajets " . ($hasStatut ? "WHERE statut IN ('termine','terminé','fini','done')" : "") . "
                 GROUP BY DATE($dateCol)
                 ORDER BY d ASC";
    $rows = $db->query($sqlTrips)->fetchAll(PDO::FETCH_ASSOC);
    $tripsPerDay = [];
    foreach ($rows as $r) $tripsPerDay[] = ['date'=>$r['d'], 'count'=>(int)$r['n']];

    // 2) Crédits / jour : approx = 2 * nb trajets/jour (ou SUM(commission) si dispo)
    if (colonneExiste($db,'trajets','commission')) {
        $sqlCred = "SELECT DATE($dateCol) d, COALESCE(SUM(commission),0) c
                    FROM trajets " . ($hasStatut ? "WHERE statut IN ('termine','terminé','fini','done')" : "") . "
                    GROUP BY DATE($dateCol)
                    ORDER BY d ASC";
        $rows2 = $db->query($sqlCred)->fetchAll(PDO::FETCH_ASSOC);
        $creditsPerDay = [];
        foreach ($rows2 as $r) $creditsPerDay[] = ['date'=>$r['d'], 'credits'=>(int)$r['c']];
    } else {
        $creditsPerDay = [];
        foreach ($tripsPerDay as $r) $creditsPerDay[] = ['date'=>$r['date'], 'credits'=>2 * (int)$r['count']]; // US9
    }

    return ['tripsPerDay'=>$tripsPerDay, 'creditsPerDay'=>$creditsPerDay];
}

// -------------------------
// Router d'actions
// -------------------------
$action = $_GET['action'] ?? 'dashboard';

switch ($action) {
    case 'create_employe':
        $email  = trim($_POST['email']  ?? '');
        $pseudo = trim($_POST['pseudo'] ?? '');
        $pwd    = $_POST['password']    ?? '';
        if ($email && $pseudo && $pwd) {
            $ok = admin_creer_employe($pdo, $email, $pseudo, $pwd);
            header('Location: /espace_admin.php?ok='.($ok?'1':'0')); exit;
        }
        header('Location: /espace_admin.php?ok=0'); exit;

    case 'set_suspendu':
        $id   = (int)($_GET['id'] ?? 0);
        $flag = (int)($_GET['suspendu'] ?? 0);
        if ($id > 0) admin_set_suspendu($pdo, $id, $flag ? 1 : 0);
        header('Location: /espace_admin.php?ok=1'); exit;

    case 'stats.json': // endpoint pour les graphes
        header('Content-Type: application/json');
        echo json_encode(admin_stats($pdo));
        exit;

    default:
        // Données pour rendu
        $comptes = admin_lister_comptes($pdo);
        $totalCredits = admin_total_credits_plateforme($pdo);
        break;
}

require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<div class="wrap">
  <div class="toolbar">
    <h1>Espace administrateur</h1>
    <span class="badge">connecté : <?= htmlspecialchars($_SESSION['email'] ?? 'admin') ?></span>
  </div>
  <?php if (!empty($_GET['ok'])): ?><div class="flash ok">Action effectuée.</div><?php endif; ?>

  <!-- Comptes -->
  <h2>Comptes (utilisateurs & employés)</h2>
  <div class="card">
    <table class="table">
      <thead><tr><th>#</th><th>Pseudo</th><th>Email</th><th>Rôle</th><th>Crédits</th><th>Statut</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($comptes as $u): ?>
          <tr>
            <td class="mono">#<?= (int)$u['id'] ?></td>
            <td><?= htmlspecialchars($u['pseudo'] ?? '') ?></td>
            <td><?= htmlspecialchars($u['email'] ?? '') ?></td>
            <td><span class="badge"><?= htmlspecialchars($u['role'] ?? '') ?></span></td>
            <td class="mono"><?= (int)($u['credits'] ?? 0) ?></td>
            <td><?= ((int)$u['suspendu']===1) ? '<span class="badge">suspendu</span>' : '<span class="badge">actif</span>' ?></td>
            <td class="actions">
              <?php if ((int)$u['suspendu']===1): ?>
                <a class="btn ok" href="/espace_admin.php?action=set_suspendu&id=<?= (int)$u['id'] ?>&suspendu=0">Réactiver</a>
              <?php else: ?>
                <a class="btn danger" href="/espace_admin.php?action=set_suspendu&id=<?= (int)$u['id'] ?>&suspendu=1" onclick="return confirm('Suspendre ce compte ?');">Suspendre</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Création employé -->
  <h2>Créer un compte employé</h2>
  <div class="card">
    <form method="post" action="/espace_admin.php?action=create_employe" class="grid" onsubmit="return true;">
      <div>
        <label>Email</label>
        <input class="btn" style="width:100%" type="email" name="email" required>
      </div>
      <div>
        <label>Pseudo</label>
        <input class="btn" style="width:100%" type="text" name="pseudo" required>
      </div>
      <div>
        <label>Mot de passe</label>
        <input class="btn" style="width:100%" type="password" name="password" minlength="8" required>
      </div>
      <div style="align-self:end">
        <button class="btn primary" type="submit">Créer / Mettre à jour</button>
      </div>
    </form>
    <p class="mono" style="margin-top:8px">ℹ️ S’il existe déjà, son rôle passe à <strong>employe</strong> et son mot de passe est mis à jour.</p>
  </div>

  <!-- Indicateurs -->
  <h2>Indicateurs & Graphiques</h2>
  <div class="card">
    <p><strong>Total crédits gagnés (plateforme):</strong> <span class="mono"><?= (int)$totalCredits ?></span></p>
  </div>

  <div class="grid">
    <div class="card">
      <h3>Covoiturages par jour</h3>
      <canvas id="chartTrips"></canvas>
    </div>
    <div class="card">
      <h3>Crédits gagnés par jour</h3>
      <canvas id="chartCredits"></canvas>
    </div>
  </div>
</div>

<?php require_once '../includes/footer.php'; // Inclusion du footer; ?>

<script>
(async function(){
  const res = await fetch('/espace_admin.php?action=stats.json', {cache:'no-store'});
  const data = await res.json();
  const trips = data.tripsPerDay || [];
  const cred  = data.creditsPerDay || [];
  const lab1 = trips.map(r => r.date), val1 = trips.map(r => r.count);
  const lab2 = cred.map(r => r.date),  val2 = cred.map(r => r.credits);

  new Chart(document.getElementById('chartTrips'), {
    type: 'line',
    data: { labels: lab1, datasets: [{ label: 'Trajets / jour', data: val1 }] },
    options: { responsive:true, maintainAspectRatio:false }
  });
  new Chart(document.getElementById('chartCredits'), {
    type: 'line',
    data: { labels: lab2, datasets: [{ label: 'Crédits / jour', data: val2 }] },
    options: { responsive:true, maintainAspectRatio:false }
  });
})();
</script>


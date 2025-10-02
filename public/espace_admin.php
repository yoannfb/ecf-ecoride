<?php
// public/espace_admin.php — Espace Admin tout-en-un (actions + vues + JSON charts)

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../includes/db.php';

if (session_status() !== PHP_SESSION_ACTIVE) session_start();
error_log('ADMIN PAGE id=' . ($_SESSION['user_id'] ?? 'null') . ' role=' . ($_SESSION['role'] ?? 'null'));


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

function admin_stats(PDO $db){
  // Trouve la meilleure colonne date pour une table donnée
  $pickDateCol = function(string $table) use ($db){
    $cols = $db->prepare("
      SELECT COLUMN_NAME
      FROM information_schema.columns
      WHERE table_schema = DATABASE()
        AND table_name = :t
        AND DATA_TYPE IN ('date','datetime','timestamp')
    ");
    $cols->execute(['t'=>$table]);
    $list = $cols->fetchAll(PDO::FETCH_COLUMN);
    if (!$list) return [null,null];

    if (in_array('date_depart',$list, true)) return [$table,'date_depart'];
    if (in_array('date_arrivee',$list, true)) return [$table,'date_arrivee'];
    return [$table,$list[0]]; // fallback
  };

  $sources = [];
  foreach (['trajets','covoiturages'] as $t) {
    [$tbl,$col] = $pickDateCol($t);
    if ($tbl && $col) $sources[] = [$tbl,$col];
  }
  if (!$sources) return ['tripsPerDay'=>[], 'creditsPerDay'=>[]];

  $tripsPerDay = [];
  $creditsPerDay = [];
  $hasAnyRow = false;

  foreach ($sources as [$table,$dateCol]) {
    // Filtre statut seulement si ça renvoie >0 lignes, sinon on enlève le filtre
    $hasStatut = (bool)$db->query("
      SELECT COUNT(*) FROM information_schema.columns
      WHERE table_schema = DATABASE()
        AND table_name = '$table'
        AND column_name = 'statut'
    ")->fetchColumn();

    $where = $hasStatut ? "WHERE statut IN ('termine','terminé','fini','done')" : "";
    $cnt = (int)$db->query("SELECT COUNT(*) FROM $table $where")->fetchColumn();
    if ($cnt === 0) $where = ""; // relax le filtre si rien

    // 1) Covoiturages/jour
    $rows = $db->query("SELECT DATE($dateCol) d, COUNT(*) n FROM $table $where GROUP BY DATE($dateCol) ORDER BY d ASC")->fetchAll(PDO::FETCH_ASSOC);
    if ($rows) $hasAnyRow = true;
    foreach ($rows as $r) {
      $d = $r['d']; $n = (int)$r['n'];
      $tripsPerDay[$d] = ($tripsPerDay[$d] ?? 0) + $n;
    }

    // 2) Crédits/jour
    $hasCommission = (bool)$db->query("
      SELECT COUNT(*) FROM information_schema.columns
      WHERE table_schema = DATABASE()
        AND table_name = '$table'
        AND column_name = 'commission'
    ")->fetchColumn();

    if ($hasCommission) {
      $rows2 = $db->query("SELECT DATE($dateCol) d, COALESCE(SUM(commission),0) c FROM $table $where GROUP BY DATE($dateCol) ORDER BY d ASC")->fetchAll(PDO::FETCH_ASSOC);
      foreach ($rows2 as $r) {
        $d = $r['d']; $c = (int)$r['c'];
        $creditsPerDay[$d] = ($creditsPerDay[$d] ?? 0) + $c;
      }
    } else {
      foreach ($rows as $r) {
        $d = $r['d']; $n = (int)$r['n'];
        $creditsPerDay[$d] = ($creditsPerDay[$d] ?? 0) + (2 * $n);
      }
    }
  }

  if (!$hasAnyRow) return ['tripsPerDay'=>[], 'creditsPerDay'=>[]];

  // Normalise en listes triées par date
  ksort($tripsPerDay);  ksort($creditsPerDay);
  $tpd = []; foreach ($tripsPerDay as $d=>$n)   $tpd[] = ['date'=>$d, 'count'=>$n];
  $cpd = []; foreach ($creditsPerDay as $d=>$c) $cpd[] = ['date'=>$d, 'credits'=>$c];

  return ['tripsPerDay'=>$tpd, 'creditsPerDay'=>$cpd];
}




// -------------------------
// Router d'actions
// -------------------------
$action = $_GET['action'] ?? 'dashboard';

// DEBUG: voir ce que la page détecte (à retirer ensuite)
if (isset($_GET['debug']) && $_GET['debug'] === 'stats') {
  header('Content-Type: text/plain; charset=utf-8');
  $st = $pdo->query("SELECT table_name, column_name, data_type
                     FROM information_schema.columns
                     WHERE table_schema = DATABASE()
                       AND table_name IN ('trajets','covoiturages')
                       AND data_type IN ('date','datetime','timestamp')
                     ORDER BY table_name, column_name");
  print_r($st->fetchAll(PDO::FETCH_ASSOC));
  exit;
}

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
    .grid{display:grid;gap:16px;grid-template-columns:1fr 1fr}
    .card canvas{max-height:320px}
    /* ✅ Force une hauteur réelle */
    #chartTrips, #chartCredits { width: 100%; height: 320px !important; display:block; }

</style>

<div class="user p5">
  <div class="container">
    <h1>
        <span>
            Espace administrateur
        </span>
    </h1>
    <span class="badge text-success">connecté : <?= htmlspecialchars($_SESSION['email'] ?? 'admin') ?></span>
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
      <canvas id="chartTrips" height="320"></canvas>
    </div>
    <div class="card">
      <h3>Crédits gagnés par jour</h3>
      <canvas id="chartCredits" height="320"></canvas>
    </div>
  </div>
</div>

<?php require_once '../includes/footer.php'; // Inclusion du footer; ?>


<script>
document.addEventListener('DOMContentLoaded', async function () {
  // 1) Vérifier que Chart.js est chargé
  if (typeof Chart === 'undefined') {
    console.error('Chart.js non chargé');
    // Fallback: charge dynamiquement (au cas où le CDN aurait été lent/bloqué)
    await new Promise((ok, ko) => {
      const s = document.createElement('script');
      s.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js';
      s.onload = ok; s.onerror = ko; document.head.appendChild(s);
    });
  }

  // 2) Récupérer les données (éviter le cache)
  const res = await fetch('/espace_admin.php?action=stats.json&t=' + Date.now(), {cache:'no-store'});
  const data = await res.json();
  console.log('stats.json', data); // debug

  const trips = data.tripsPerDay || [];
  const cred  = data.creditsPerDay || [];

  // 3) Construire les jeux de données
  const lab1 = trips.map(r => r.date);
  const val1 = trips.map(r => r.count);
  const lab2 = cred.map(r => r.date);
  const val2 = cred.map(r => r.credits);

  // 4) Monter les graphiques (en utilisant le contexte 2D explicitement)
  const ctx1 = document.getElementById('chartTrips').getContext('2d');
  const ctx2 = document.getElementById('chartCredits').getContext('2d');

  new Chart(ctx1, {
    type: 'line',
    data: { labels: lab1, datasets: [{ label: 'Trajets / jour', data: val1, tension: 0.2 }] },
    options: { responsive:true, maintainAspectRatio:false }
  });

  new Chart(ctx2, {
    type: 'line',
    data: { labels: lab2, datasets: [{ label: 'Crédits / jour', data: val2, tension: 0.2 }] },
    options: { responsive:true, maintainAspectRatio:false }
  });

  // 5) Petit fallback si vraiment rien n'apparaît
  if (!lab1.length && !lab2.length) {
    document.getElementById('chartTrips').insertAdjacentHTML('afterend', '<p class="mono">Aucune donnée à afficher.</p>');
  }
});
</script>


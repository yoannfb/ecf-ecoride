<?php
session_start();
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'includes/db.php';

// Vérifie que l'utilisateur est connecté et que son rôle est "employe"
// Sinon, il est redirigé vers la page de connexion
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employe') {
    header('Location: connexion.php');
    exit();
}

// ✅ TRAITEMENT ACTION (valider/refuser)
if (isset($_GET['action'], $_GET['id'])) {
    $action = $_GET['action'];
    $id = (int) $_GET['id']; // Sécurise l'ID en entier

    // Si l'action demandée est autorisée, on met à jour le statut de l'avis
    if (in_array($action, ['valider', 'refuser'])) {
        $stmt = $pdo->prepare("UPDATE avis SET statut = ? WHERE id = ?");
        $stmt->execute([$action === 'valider' ? 'validé' : 'refusé', $id]);
        header("Location: espace_employe.php"); // Recharge la page pour mettre à jour l'affichage
        exit();
    }
}
?>

<h1>Espace Employé</h1>

<!-- 🔍 Avis en attente -->
<h2>Avis en attente</h2>
<?php
// Récupère tous les avis dont le statut est "en attente" pour modération
$avis = $pdo->query("
    SELECT a.id, a.note, a.commentaire, u.pseudo AS auteur, t.adresse_depart, t.adresse_arrivee
    FROM avis a
    JOIN utilisateurs u ON a.auteur_id = u.id
    JOIN trajets t ON a.trajet_id = t.id
    WHERE a.statut = 'en attente'
")->fetchAll();

// Affiche les avis ou un message s'il n'y en a aucun
if (empty($avis)) {
    echo "<p>Aucun avis en attente.</p>";
} else {
    foreach ($avis as $a) {
        echo "<div style='border:1px solid #ccc; padding:10px; margin:10px 0'>";
        echo "<strong>{$a['auteur']}</strong> a noté {$a['note']}/5<br>";
        echo "<em>{$a['commentaire']}</em><br>";
        echo "Trajet : {$a['adresse_depart']} → {$a['adresse_arrivee']}<br>";
        echo "<a href='?action=valider&id={$a['id']}'>✅ Valider</a> | ";
        echo "<a href='?action=refuser&id={$a['id']}'>❌ Refuser</a>";
        echo "</div>";
    }
}
?>

<!-- 🚨 Trajets signalés -->
<h2>Trajets signalés</h2>
<?php
// Récupère les trajets marqués comme problématiques (champ "probleme" = 1)
$problems = $pdo->query("
    SELECT t.*, 
            u1.pseudo AS conducteur, u1.email AS email_conducteur,
            u2.pseudo AS passager, u2.email AS email_passager
    FROM trajets t
    JOIN utilisateurs u1 ON t.conducteur_id = u1.id
    JOIN participations p ON p.covoiturage_id = t.id
    JOIN utilisateurs u2 ON p.utilisateur_id = u2.id
    WHERE t.probleme = 1
")->fetchAll();

// Affiche les trajets ou un message s'il n'y en a aucun
if (empty($problems)) {
    echo "<p>Aucun trajet signalé.</p>";
} else {
    foreach ($problems as $p) {
        echo "<div style='border:1px solid red; padding:10px; margin:10px 0'>";
        echo "<strong>Trajet #{$p['id']}</strong><br>";
        echo "Conducteur : {$p['conducteur']} ({$p['email_conducteur']})<br>";
        echo "Passager : {$p['passager']} ({$p['email_passager']})<br>";
        echo "Départ : {$p['adresse_depart']} ({$p['date_depart']})<br>";
        echo "Arrivée : {$p['adresse_arrivee']} ({$p['date_arrivee']})";
        echo "</div>";
    }
}


require_once 'includes/footer.php'; // Inclusion du footer; ?>


<?php
require_once 'includes/db.php';



$depart = $_GET['depart'] ?? '';
$arrivee = $_GET['arrivee'] ?? '';
$date = $_GET['date'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM trajets WHERE adresse_depart LIKE ? AND adresse_arrivee LIKE ? AND DATE(date_depart) = ?");
$stmt->execute(["%$depart%", "%$arrivee%", $date]);

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($results)) {
    echo "<div class='alert alert-warning'>❌ Aucun covoiturage disponible pour ces critères.</div>";
    exit;
}

foreach ($results as $trajet) {
    echo "<div class='card p-3 mb-2'>";
    echo "<h5>{$trajet['adresse_depart']} → {$trajet['adresse_arrivee']}</h5>";
    echo "<p>📅 " . date('d/m/Y H:i', strtotime($trajet['date_depart'])) . "</p>";
    echo "<p>🧍 Conducteur : {$trajet['conducteur_id']}</p>";
    echo "<a href='covoiturages.php?id={$trajet['id']}' class='btn btn-primary mt-2'>Voir le covoiturage</a>";
    echo "</div>";
}
include 'log_mongo.php';
?>
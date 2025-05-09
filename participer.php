<?php
session_start();
require_once 'includes/db.php'; // Connexion PDO

if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$covoiturage_id = $_POST['covoiturage_id'] ?? null;

if (!$covoiturage_id) {
    die('Covoiturage non spécifié.');
}

// 1. Récupérer le covoiturage
$sql = "SELECT * FROM trajets WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$covoiturage_id]);
$covoiturage = $stmt->fetch();

if (!$covoiturage) {
    die("Covoiturage introuvable.");
}

if ($covoiturage['places_disponibles'] <= 0) {
    die("Plus de place disponible pour ce covoiturage.");
}

// 2. Vérifier crédits utilisateur
$sql = "SELECT credits FROM utilisateurs WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($user['credits'] < $covoiturage['prix']) {
    die("Crédits insuffisants.");
}

// Enregistrer la participation
$insert = $pdo->prepare("INSERT INTO participations (utilisateur_id, covoiturage_id, date_participation)
                        VALUES (?, ?, NOW())");
$insert->execute([$user_id, $covoiturage_id]);

// Décrémenter le nombre de places disponibles
$update = $pdo->prepare("UPDATE trajets SET places_disponibles = places_disponibles - 1 WHERE id = ?");
$update->execute([$covoiturage_id]);

// Redirection
header("Location: espace_utilisateur.php?success=participation");
exit;

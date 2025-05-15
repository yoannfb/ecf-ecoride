<?php
// Démarre la session et connecte à la base de données
session_start();
require_once 'includes/db.php'; // Connexion PDO

// Vérifie que l'utilisateur est connecté, sinon redirection
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$covoiturage_id = $_POST['covoiturage_id'] ?? null;

// Vérifie qu'un ID de covoiturage a bien été fourni
if (!$covoiturage_id) {
    die('Covoiturage non spécifié.');
}

// 1. Récupérer les détails du covoiturage sélectionné
$sql = "SELECT * FROM trajets WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$covoiturage_id]);
$covoiturage = $stmt->fetch();

// Si le covoiturage n'existe pas, on arrête
if (!$covoiturage) {
    die("Covoiturage introuvable.");
}
// Vérifie s'il reste des places disponibles
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

// Redirige vers l'espace utilisateur avec un message de succès
header("Location: espace_utilisateur.php?success=participation");
exit;

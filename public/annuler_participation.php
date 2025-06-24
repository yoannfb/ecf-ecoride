<?php
session_start();
require_once '../includes/db.php';

$user_id = $_SESSION['user_id'] ?? null;
$covoiturage_id = $_GET['id'] ?? null;

if ($user_id && $covoiturage_id) {
    // Supprimer la participation
    $delete = $pdo->prepare("DELETE FROM participations WHERE utilisateur_id = ? AND covoiturage_id = ?");
    $delete->execute([$user_id, $covoiturage_id]);

    // Rendre une place
    $update = $pdo->prepare("UPDATE trajets SET places_disponibles = places_disponibles + 1 WHERE id = ?");
    $update->execute([$covoiturage_id]);

    header("Location: espace_utilisateur.php?success=annule");
    exit;
} else {
    echo "Erreur : impossible d'annuler.";
}

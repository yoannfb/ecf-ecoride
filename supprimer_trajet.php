<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$trajet_id = $_POST['trajet_id'] ?? null;

if ($trajet_id) {
    // Vérifie que le trajet appartient bien au conducteur connecté
    $check = $pdo->prepare("SELECT id FROM trajets WHERE id = ? AND conducteur_id = ?");
    $check->execute([$trajet_id, $user_id]);
    $trajet = $check->fetch();

    if ($trajet) {
        // Supprime le trajet
        $delete = $pdo->prepare("DELETE FROM trajets WHERE id = ?");
        $delete->execute([$trajet_id]);

        // (Optionnel) Tu peux aussi restituer 2 crédits au chauffeur ici
        $pdo->prepare("UPDATE utilisateurs SET credits = credits + 2 WHERE id = ?")->execute([$user_id]);

        // Redirige avec message de succès
        header("Location: espace_utilisateur.php?success=trajet_supprime");
        exit();
    }
}

// Si le trajet n'existe pas ou ne lui appartient pas
header("Location: espace_utilisateur.php?erreur=trajet_introuvable");
exit();

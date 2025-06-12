<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$trajet_id = $_POST['id'] ?? null;
$action = $_POST['action'] ?? null;

if (!$trajet_id || !$action) {
    header("Location: espace_utilisateur.php?erreur=changement_invalide");
    exit();
}

// Vérifie que le trajet appartient au conducteur connecté
$stmt = $pdo->prepare("SELECT * FROM trajets WHERE id = ? AND conducteur_id = ?");
$stmt->execute([$trajet_id, $user_id]);
$trajet = $stmt->fetch();

if (!$trajet) {
    header("Location: espace_utilisateur.php?erreur=trajet_non_autorise");
    exit();
}

// Détermine le nouveau statut
$newStatut = null;
if ($action === 'start' && $trajet['statut'] === 'à venir') {
    $newStatut = 'en cours';
} elseif ($action === 'finish' && $trajet['statut'] === 'en cours') {
    $newStatut = 'terminé';
}

if ($newStatut) {
    $update = $pdo->prepare("UPDATE trajets SET statut = ? WHERE id = ?");
    $update->execute([$newStatut, $trajet_id]);
}

header("Location: espace_utilisateur.php?success=statut_change");
exit();

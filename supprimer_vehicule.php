<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$vehicule_id = $_POST['vehicule_id'] ?? null;

if ($vehicule_id) {
    // Vérifie que le véhicule appartient bien à l'utilisateur
    $check = $pdo->prepare("SELECT id FROM vehicules WHERE id = ? AND utilisateur_id = ?");
    $check->execute([$vehicule_id, $user_id]);
    $vehicule = $check->fetch();

    // Vérifie que le véhicule n’est pas utilisé dans un trajet
    $checkTrajet = $pdo->prepare("SELECT COUNT(*) FROM trajets WHERE vehicule_id = ?");
    $checkTrajet->execute([$vehicule_id]);
    $nbTrajets = $checkTrajet->fetchColumn();

    if ($nbTrajets > 0) {
    // Message d’erreur (ou redirection avec une erreur)
    header("Location: espace_utilisateur.php?erreur=suppression_vehicule");    
    exit();
    }

    if ($vehicule) {
        // Supprime le véhicule
        $delete = $pdo->prepare("DELETE FROM vehicules WHERE id = ?");
        $delete->execute([$vehicule_id]);
    }

    

}

// Redirige vers l'espace utilisateur
header("Location: espace_utilisateur.php");
exit();

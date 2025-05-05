<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$trajet_id = $_POST['id'] ?? null;

// Récupération des données du formulaire
$vehicule_id = $_POST['vehicule_id'] ?? null;
$adresse_depart = $_POST['adresse_depart'] ?? '';
$adresse_arrivee = $_POST['adresse_arrivee'] ?? '';
$date_depart = $_POST['date_depart'] ?? '';
$date_arrivee = $_POST['date_arrivee'] ?? '';
$prix = $_POST['prix'] ?? 0;

// Validation rapide
if (!$trajet_id || !$vehicule_id || !$adresse_depart || !$adresse_arrivee || !$date_depart || !$date_arrivee || $prix <= 0) {
    header("Location: espace_utilisateur.php?erreur=modif_invalide");
    exit();
}

// Vérifie que le trajet appartient bien à l'utilisateur connecté
$check = $pdo->prepare("SELECT id FROM trajets WHERE id = ? AND conducteur_id = ?");
$check->execute([$trajet_id, $user_id]);
$trajet = $check->fetch();

if (!$trajet) {
    header("Location: espace_utilisateur.php?erreur=modif_non_autorisee");
    exit();
}

// Mise à jour en base
$stmt = $pdo->prepare("UPDATE trajets SET 
    vehicule_id = ?, 
    adresse_depart = ?, 
    adresse_arrivee = ?, 
    date_depart = ?, 
    date_arrivee = ?, 
    prix = ? 
    WHERE id = ?");

$stmt->execute([
    $vehicule_id,
    $adresse_depart,
    $adresse_arrivee,
    $date_depart,
    $date_arrivee,
    $prix,
    $trajet_id
]);

header("Location: espace_utilisateur.php?success=trajet_modifie");
exit();

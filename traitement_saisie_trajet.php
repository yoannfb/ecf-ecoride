<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupération et validation des données du formulaire
$vehicule_id = $_POST['vehicule_id'] ?? null;
$adresse_depart = $_POST['adresse_depart'] ?? '';
$adresse_arrivee = $_POST['adresse_arrivee'] ?? '';
$date_depart = $_POST['date_depart'] ?? '';
$date_arrivee = $_POST['date_arrivee'] ?? '';
$prix = $_POST['prix'] ?? 0;

// Vérification minimale
if (!$vehicule_id || !$adresse_depart || !$adresse_arrivee || !$date_depart || !$date_arrivee || $prix <= 0) {
    echo "Tous les champs sont obligatoires et le prix doit être supérieur à 0.";
    exit();
}

// Insertion dans la table trajets
$stmt = $pdo->prepare("INSERT INTO trajets 
    (conducteur_id, vehicule_id, adresse_depart, adresse_arrivee, date_depart, date_arrivee, prix)
    VALUES (?, ?, ?, ?, ?, ?, ?)");

$stmt->execute([
    $user_id,
    $vehicule_id,
    $adresse_depart,
    $adresse_arrivee,
    $date_depart,
    $date_arrivee,
    $prix
]);

// (Optionnel) Mettre à jour les crédits du chauffeur, en déduisant 2 crédits
$updateCredits = $pdo->prepare("UPDATE utilisateurs SET credits = credits - 2 WHERE id = ?");
$updateCredits->execute([$user_id]);

// Redirection vers l’espace utilisateur
header("Location: espace_utilisateur.php");
exit();

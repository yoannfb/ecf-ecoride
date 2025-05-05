<?php
session_start();
require 'includes/db.php'; // adapte le chemin si nécessaire

// Vérifie que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_POST['role'] ?? '';

// Mise à jour du rôle dans la table utilisateurs
$update = $pdo->prepare("UPDATE utilisateurs SET role = ? WHERE id = ?");
$update->execute([$role, $user_id]);

// Si l'utilisateur est chauffeur ou les deux, enregistrer son véhicule
if ($role === 'chauffeur' || $role === 'les-deux') {
    $plaque = $_POST['plaque'] ?? '';
    $date_immat = $_POST['date_immat'] ?? '';
    $modele = $_POST['modele'] ?? '';
    $marque = $_POST['marque'] ?? '';
    $couleur = $_POST['couleur'] ?? '';
    $places = $_POST['places'] ?? 0;
    $fumeur = isset($_POST['fumeur']) ? 1 : 0;
    $animaux = isset($_POST['animaux']) ? 1 : 0;
    $eco = isset($_POST['eco']) ? 1 : 0;
    $preferences_perso = $_POST['preferences_perso'] ?? '';

    // Insère les données véhicule dans la base
    $insert = $pdo->prepare("INSERT INTO vehicules 
        (utilisateur_id, plaque, date_immat, modele, marque, couleur, places, fumeur, animaux, preferences_perso, eco)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $insert->execute([
        $user_id,
        $plaque,
        $date_immat,
        $modele,
        $marque,
        $couleur,
        $places,
        $fumeur,
        $animaux,
        $preferences_perso,
        $eco,
    ]);
}

// Redirige vers l'espace utilisateur
header("Location: utilisateur.php");
exit();


<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Vérifie si un fichier est envoyé
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $fileTmp = $_FILES['photo']['tmp_name'];
    $fileName = basename($_FILES['photo']['name']);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Vérifie l'extension
    $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($fileExt, $allowedExts)) {
        die("Format d'image non autorisé.");
    }

    // Renomme le fichier de manière unique
    $newName = uniqid('profil_') . '.' . $fileExt;
    $destination = 'uploads/' . $newName;

    // Déplace le fichier
    if (move_uploaded_file($fileTmp, $destination)) {
        // Met à jour la BDD
        $stmt = $pdo->prepare("UPDATE utilisateurs SET photo = ? WHERE id = ?");
        $stmt->execute([$newName, $user_id]);
    }
}

// Retour à l'espace utilisateur
header("Location: espace_utilisateur.php");
exit();

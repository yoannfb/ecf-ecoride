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
$sql = "SELECT * FROM covoiturages WHERE id = ?";
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

// 3. Confirmer participation + MAJ
try {
    $pdo->beginTransaction();

    // Déduire crédits utilisateur
    $sql = "UPDATE utilisateurs SET credits = credits - ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$covoiturage['prix'], $user_id]);

    // Mettre à jour les places restantes
    $sql = "UPDATE covoiturages SET places_disponibles = places_disponibles - 1 WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$covoiturage_id]);

    // Enregistrer la participation
    $sql = "INSERT INTO participations (utilisateur_id, covoiturage_id, date_participation)
            VALUES (?, ?, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $covoiturage_id]);

    $pdo->commit();
    header("Location: utilisateur.php?success=1");
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    die("Erreur lors de la participation : " . $e->getMessage());
}

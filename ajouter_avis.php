<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auteur_id = $_SESSION['user_id'];
    $trajet_id = $_POST['trajet_id'] ?? null;
    $note = $_POST['note'] ?? null;
    $commentaire = trim($_POST['commentaire']);

    if ($trajet_id && $note && $commentaire) {
        $stmt = $pdo->prepare("INSERT INTO avis (trajet_id, auteur_id, note, commentaire, statut) VALUES (?, ?, ?, ?, 'en attente')");
        $stmt->execute([$trajet_id, $auteur_id, $note, $commentaire]);

        header("Location: espace_utilisateur.php?avis=ok");
        exit();
    } else {
        echo "Formulaire incomplet.";
    }
}
?>

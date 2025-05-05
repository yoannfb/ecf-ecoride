<?php
session_start();
require_once 'includes/db.php';



if (!isset($_POST['email'], $_POST['password'])) {
    header("Location: connexion.php?error=Champs manquants");
    exit;
}

$email = $_POST['email'];
$password = $_POST['password'];

try {
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['mot_de_passe'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['pseudo'] = $user['pseudo'];
        $_SESSION['email'] = $user['email'];

        header("Location: utilisateur.php"); // à adapter selon ta page utilisateur
        exit;
    } else {
        header("Location: connexion.php?error=Email ou mot de passe incorrect");
        exit;
    }
} catch (PDOException $e) {
    header("Location: connexion.php?error=Erreur interne");
    exit;
}

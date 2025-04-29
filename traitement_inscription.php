<?php
session_start();
require_once 'includes/db.php';

if (
    empty($_POST['pseudo']) ||
    empty($_POST['email']) ||
    empty($_POST['password']) ||
    empty($_POST['confirm_password'])
) {
    header("Location: inscription.php?error=Veuillez remplir tous les champs.");
    exit;
}

$pseudo = $_POST['pseudo'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// Vérifie si les mots de passe correspondent
if ($password !== $confirm_password) {
    header("Location: inscription.php?error=Les mots de passe ne correspondent pas.");
    exit;
}

// Hachage du mot de passe
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        header("Location: inscription.php?error=Un compte existe déjà avec cette adresse e-mail.");
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO utilisateurs (pseudo, email, mot_de_passe) VALUES (?, ?, ?)");
    $stmt->execute([$pseudo, $email, $hashedPassword]);

    header("Location: inscription.php?success=Compte créé avec succès. Vous pouvez maintenant vous connecter.");
    exit;
} catch (PDOException $e) {
    header("Location: inscription.php?error=Erreur lors de la création du compte.");
    exit;
}

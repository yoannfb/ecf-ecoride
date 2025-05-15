<style> 


</style>

<?php
// Démarre la session et inclut les fichiers nécessaires (header, navbar, base de données)
session_start();
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'includes/db.php';

// Si l'utilisateur est déjà connecté, on le redirige vers son espace
if (isset($_SESSION['user_id'])) {
    header("Location: espace_utilisateur.php");
    exit();
}

// Si le formulaire est soumis par méthode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupère les champs du formulaire
    $pseudo = $_POST['pseudo'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Vérifie que tous les champs sont remplis
    if (empty($pseudo) || empty($email) || empty($password) || empty($confirm_password)) {
        $message = "Veuillez remplir tous les champs.";
    } elseif ($password !== $confirm_password) {
        $message = "Les mots de passe ne correspondent pas.";
    } elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\\d)(?=.*[^a-zA-Z\\d]).{8,}$/', $password)) {
        $message = "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
    } else {
        // Hachage avec sécurisé du mot de passe
        // Le hachage de mot de passe transforme votre mot de passe (ou toute autre donnée) en une courte chaîne de lettres et/ou de chiffres grâce à un algorithme de chiffrement. En cas de piratage d'un site web, le hachage de mot de passe empêche les cybercriminels d'accéder à vos mots de passe.
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Vérifie si l'email est déjà utilisé
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $message = "Un compte existe déjà avec cette adresse e-mail.";
        } else {
            // Insère un nouvel utilisateur en base
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (pseudo, email, mot_de_passe) VALUES (?, ?, ?)");
            $stmt->execute([$pseudo, $email, $hashedPassword]);

            // Redirige vers la page de connexion après inscription réussie
            header("Location: connexion.php?success=compte");
            exit;
        }
    }
}

?>

<div class="colornav d-flex flex-column align-items-center text-center pt-5">
    <h2 class="text-center mb-4">Créer un compte</h2>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger text-center">
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success text-center">
            <?= htmlspecialchars($_GET['success']) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($message)): ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>


    <form method="POST">
        <div class="mb-3">
            <label for="pseudo" class="form-label">Pseudo :</label>
            <input type="text" name="pseudo" id="pseudo" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Adresse e-mail :</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe :</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirmer le mot de passe :</label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success w-100">S'inscrire</button>
    </form>

    <p class="text-center mt-3">
        Déjà un compte ? <a href="connexion.php">Se connecter</a>
    </p>
</div>

<?php require_once 'includes/footer.php'; ?>


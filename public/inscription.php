<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\UtilisateurController;

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $motDePasse = $_POST['mot_de_passe'] ?? '';

    $controller = new UtilisateurController();
    $controller->register($email, $motDePasse);
}


require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
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

<?php require_once __DIR__ . '/../includes/footer.php'; ?>


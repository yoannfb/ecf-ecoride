<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\UtilisateurController;

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $motDePasse = $_POST['password'] ?? '';

    $controller = new UtilisateurController();
    $controller->login($email, $motDePasse);
}
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>


<div class="d-flex justify-content-around">
    <div class="col-lg-6 col-md-6 col-sm-12 car pt-5">
        <img src="assets/voiture connexion.jpg" alt="voiture roulant">
    </div>

    <div class="col-lg-6 col-md-6 col-sm-12 d-flex flex-column covoit pt-5">
        <h2 class="text-center mb-4">Connexion à EcoRide</h2>
        <?php if (isset($_GET['error'])): // Démarrage de la session et inclusion des éléments nécessaires if (isset($_GET['error'])): ?>
            <div class="alert alert-danger text-center">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; // Démarrage de la session et inclusion des éléments nécessaires endif; ?>
        <form method="POST" class="mx-auto" style="max-width: 400px;">
            <div class="mb-3">
                <label for="email" class="form-label">Adresse e-mail</label>
                <input type="email" class="form-control" id="email" name="email" required placeholder="ex : utilisateur@ecoride.fr">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Se connecter</button>
        </form>
        <p class="text-center mt-3">
            Pas encore de compte ?
            <a href="inscription.php">Créer un compte</a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

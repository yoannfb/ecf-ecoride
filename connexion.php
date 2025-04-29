<style>
.container {
    background-color: #F7F6CF !important;
    font-family: EB Garamond !important;
}
</style>

<?php
// connexion.php
session_start();
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'includes/db.php';

?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Connexion à EcoRide</h2>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger text-center">
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <form action="traitement_connexion.php" method="POST" class="mx-auto" style="max-width: 400px;">
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

<?php require_once 'includes/footer.php'; ?>

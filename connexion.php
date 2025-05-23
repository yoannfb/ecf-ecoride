

<?php
// Démarrage de la session et inclusion des éléments nécessaires
session_start();
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'includes/db.php';

// Si l'utilisateur est déjà connecté, on le redirige vers son espace
if (isset($_SESSION['user_id'])) {
    header("Location: espace_utilisateur.php");
    exit();
}

// Initialisation du message d'erreur (vide par défaut)
$message = '';

// Vérifie si le formulaire a été soumis via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupère l'email et le mot de passe soumis
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Vérifie que les deux champs sont remplis
    if (empty($email) || empty($password)) {
        $message = "Veuillez remplir tous les champs.";
    } else {
        // Prépare et exécute une requête pour trouver l'utilisateur par email
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Après $stmt->execute([$email]);
$user = $stmt->fetch();

        // Si l'employé existe et que le mot de passe est correct
        if ($user && password_verify($password, $user['mot_de_passe'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'employe') {
                header("Location: employe/espace_employe.php");
            } else {
                header("Location: espace_utilisateur.php");
            }
            exit();
        }


        // Si l'utilisateur existe et que le mot de passe est correct
        if ($user && password_verify($password, $user['mot_de_passe'])) {
            // Stocke les informations de l'utilisateur en session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['pseudo'] = $user['pseudo'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            header("Location: espace_utilisateur.php");
            exit();
        } else {
            // Sinon, affiche un message d'erreur
            $message = "Email ou mot de passe incorrect.";
        }
    }
}
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

<?php require_once 'includes/footer.php'; // Inclusion du footer; ?>

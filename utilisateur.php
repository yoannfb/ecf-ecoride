<style>
    .back {
        width: 100%;
        height: 100%;
        background-image: url("assets/espace utilisateur.jpg");
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 50%;
        background-color: #F7F6CF;
    }
    
</style>


<?php
session_start();
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'includes/db.php';

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php?error=Veuillez vous connecter pour accéder à cette page.");
    exit;
}

// Récupération des infos utilisateur depuis la base
try {
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    die("Erreur lors du chargement du profil.");
}
?>

<div class="back d-flex flex-column text-center align-items-center pt-5">
    <h2 class="mb-4">Bienvenue, <?= htmlspecialchars($user['pseudo']) ?> 👋</h2>

    <ul class="list-group">
        <li class="list-group-item"><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></li>
        <li class="list-group-item"><strong>Crédits disponibles :</strong> <?= $user['credits'] ?></li>
    </ul>

    <div class="py-5">
        <a href="logout.php" class="btn btn-success">Se déconnecter</a>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

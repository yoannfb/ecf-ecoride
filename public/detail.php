<?php
// Démarrage de la session et inclusion des fichiers nécessaires
session_start();
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'includes/db.php';

// Récupère l'ID du trajet depuis l'URL
// Récupère l'ID du trajet depuis l'URL (paramètre GET)
$id = $_GET['id'] ?? null;
$covoiturage_id = $id;

// Si aucun ID n'est fourni, affiche un message d'erreur et arrête l'exécution
if (!$id) {
    echo "<div class='alert alert-danger'>Aucun trajet sélectionné.</div>";
    exit;
}

// Requête pour récupérer un seul trajet par son id
// Prépare une requête SQL pour récupérer les détails du trajet correspondant à l'ID
$stmt = $pdo->prepare("
    SELECT 
        t.adresse_depart AS depart,
        t.adresse_arrivee AS arrivee,
        DATE(t.date_depart) AS date,
        TIME(t.date_depart) AS heure_depart,
        TIME(t.date_arrivee) AS heure_arrivee,
        t.prix,
        t.statut,
        u.pseudo AS chauffeur,
        u.photo,
        v.places,
        v.eco,
        CONCAT(v.marque, ' ', v.modele) AS vehicule
    FROM trajets t
    JOIN utilisateurs u ON t.conducteur_id = u.id
    JOIN vehicules v ON t.vehicule_id = v.id
    WHERE t.id = ?
");
$stmt->execute([$id]);
$trajet = $stmt->fetch(); // Récupère une seule ligne (le trajet concerné)

// Si aucun trajet n'est trouvé, affiche une erreur et stoppe le script
if (!$trajet) {
    echo "<div class='alert alert-danger'>Trajet introuvable.</div>";
    exit;
}
// petite modif pour forcer le rebuild de Heroku
?>

<style>
    main {
        background-color: #F7F6CF !important;
        font-family: EB Garamond !important;
        font-size:1.2rem;
    }

</style>

<main class="container py-5">
    <h2>Détail du trajet avec <?= htmlspecialchars($trajet['chauffeur']) ?> 🚗</h2>
    <ul class="list-group">
        <li class="list-group-item"><strong>Départ :</strong> <?= $trajet['depart'] ?> à <?= $trajet['heure_depart'] ?></li>
        <li class="list-group-item"><strong>Arrivée :</strong> <?= $trajet['arrivee'] ?> à <?= $trajet['heure_arrivee'] ?></li>
        <li class="list-group-item"><strong>Date :</strong> <?= $trajet['date'] ?></li>
        <li class="list-group-item"><strong>Prix :</strong> <?= $trajet['prix'] ?> €</li>
        <li class="list-group-item"><strong>Véhicule :</strong> <?= $trajet['vehicule'] ?></li>
        <li class="list-group-item"><strong>Places :</strong> <?= $trajet['places'] ?></li>
        <li class="list-group-item"><strong>Éco :</strong> <?= $trajet['eco'] ? '✅ Oui' : '❌ Non' ?></li>
    </ul>
    <?php if (!isset($_SESSION['user_id'])): // Démarrage de la session et inclusion des fichiers nécessaires if (!isset($_SESSION['user_id'])): ?>
        <div class="text-center mt-4">
            <p>Vous devez être connecté pour participer à ce covoiturage.</p>
            <a href="connexion.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn btn-success">Se connecter</a>
        </div>
    <?php else: // Démarrage de la session et inclusion des fichiers nécessaires else: ?>
        <form action="participer.php" method="POST" class="text-center mt-4">
            <input type="hidden" name="covoiturage_id" value="<?= $covoiturage_id ?>">
            <button type="submit" class="btn btn-success">Participer à ce covoiturage</button>
        </form>
    <?php endif; // Démarrage de la session et inclusion des fichiers nécessaires endif;?>


    <a href="covoiturages.php" class="btn btn-secondary mt-4">← Retour</a>
</main>

<?php include("includes/footer.php"); // Inclusion du footer; ?>


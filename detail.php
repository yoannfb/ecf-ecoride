<style>
  main {
    background-color: #F7F6CF !important;
    font-family: EB Garamond !important;
    font-size:1.2rem;
  }

</style>
<?php
include("includes/header.php");
include("includes/navbar.php");
include("includes/mock.php");
require_once 'includes/db.php';


// Vérifier que l'ID est bien passé et existe dans le mock
$id = $_GET['id'] ?? null;
$trajet = null;

foreach ($covoiturages as $item) {
  if ($item['id'] == $id) {
    $trajet = $item;
    break;
  }
}
?>

<main class="py-5">
  <?php if (!$trajet): ?>
    <div class="alert alert-danger">
      Le covoiturage demandé est introuvable.
    </div>
  <?php else: ?>
    <h2>Détail du trajet avec <?= htmlspecialchars($trajet['chauffeur']) ?> 🚗</h2>

    <div class="d-flex mt-4">
      <div class="col-md-4">
        <img src="<?= htmlspecialchars($trajet['photo']) ?>" class="img-fluid rounded shadow" alt="Photo du chauffeur">
        <p class="mt-3"><strong>Note :</strong> <?= htmlspecialchars($trajet['note']) ?> ⭐</p>
      </div>
      <div class="d-flex flex-column ms-5">
        <ul class="list-group mb-3">
          <li class="list-group-item"><strong>Départ :</strong> <?= htmlspecialchars($trajet['depart']) ?> à <?= htmlspecialchars($trajet['heure_depart']) ?></li>
          <li class="list-group-item"><strong>Arrivée :</strong> <?= htmlspecialchars($trajet['arrivee']) ?> à <?= htmlspecialchars($trajet['heure_arrivee']) ?></li>
          <li class="list-group-item"><strong>Date :</strong> <?= htmlspecialchars($trajet['date']) ?></li>
          <li class="list-group-item"><strong>Prix :</strong> <?= htmlspecialchars($trajet['prix']) ?> €</li>
          <li class="list-group-item"><strong>Places restantes :</strong> <?= htmlspecialchars($trajet['places']) ?></li>
          <li class="list-group-item"><strong>Véhicule :</strong> <?= htmlspecialchars($trajet['vehicule']) ?></li>
          <li class="list-group-item"><strong>Écologique :</strong> <?= $trajet['eco'] ? '✅ Oui' : '❌ Non' ?></li>
        </ul>

        <h4>Préférences du conducteur</h4>
        <p><em>(Cette partie pourra être remplie dynamiquement selon les préférences stockées)</em></p>

        <h4>Avis du conducteur</h4>
        <p><em>À implémenter avec gestion back-end (US12)</em></p>

        <div class="container mt-5">
    <!-- Affichage des détails du covoiturage ici -->

    <?php if (!isset($_SESSION['user'])): ?>
        <div class="text-center mt-4">
            <p>Vous devez être connecté pour participer à ce covoiturage.</p>
            <a href="connexion.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn btn-success">Se connecter</a>
        </div>
    <?php else: ?>
        <form action="participer.php" method="POST" class="text-center mt-4">
            <input type="hidden" name="covoiturage_id" value="<?= $covoiturage_id ?>">
            <button type="submit" class="btn btn-success">Participer à ce covoiturage</button>
        </form>
    <?php endif; ?>
</div>
      </div>
    </div>
  <?php endif; ?>
</main>

<?php include("includes/footer.php"); ?>

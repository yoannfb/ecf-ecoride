<style>
  main {
    background-color:  #F7F6CF;
  }
</style>

<?php
// Inclusion des éléments d'en-tête, barre de navigation et base de données
require_once '../includes/header.php';
require_once '../includes/navbar.php';
require_once '../includes/db.php';

// Récupération des champs du formulaire de recherche avec valeurs par défaut
$depart = $_POST['depart'] ?? '';
$arrivee = $_POST['arrivee'] ?? '';
$date = $_POST['date'] ?? '';

// Requête SQL simple
// Construction de la requête SQL pour récupérer les trajets disponibles
$sql = "SELECT
          t.id,
          t.adresse_depart AS depart,
          t.adresse_arrivee AS arrivee,
          t.date_depart,
          t.prix,
          t.statut,
          u.pseudo AS chauffeur,
          u.photo,
          t.places_disponibles AS places,
          v.eco,
          CONCAT(v.marque, ' ', v.modele) AS vehicule
        FROM trajets t
        JOIN utilisateurs u ON t.conducteur_id = u.id
        JOIN vehicules v ON t.vehicule_id = v.id
        WHERE t.statut = 'à venir'";

// Tableau pour stocker dynamiquement les paramètres de la requête
$params = [];

// Ajout du filtre de ville de départ si précisé
if (!empty($depart)) {
    $sql .= " AND t.adresse_depart LIKE ?";
    $params[] = "%$depart%";
}

// Ajout du filtre de ville d'arrivée si précisé
if (!empty($arrivee)) {
    $sql .= " AND t.adresse_arrivee LIKE ?";
    $params[] = "%$arrivee%";
}

// Ajout du filtre de date si précisé
if (!empty($date)) {
    $sql .= " AND DATE(t.date_depart) = ?";
    $params[] = $date;
}

$sql .= " ORDER BY t.date_depart ASC";

// Préparation et exécution de la requête SQL avec les filtres
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$covoiturages = $stmt->fetchAll(); // Récupération de tous les trajets correspondants
?>

<main class="container py-4">
  <h2>Rechercher un covoiturage</h2>
  <form method="POST" class="row g-3 mb-4">
    <div class="col-md-3">
      <input type="text" name="depart" class="form-control" placeholder="Départ" value="<?= htmlspecialchars($depart) ?>">
    </div>
    <div class="col-md-3">
      <input type="text" name="arrivee" class="form-control" placeholder="Arrivée" value="<?= htmlspecialchars($arrivee) ?>">
    </div>
    <div class="col-md-3">
      <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($date) ?>">
    </div>
    <div class="col-md-3">
      <button type="submit" class="btn btn-success w-100">Rechercher</button>
    </div>
  </form>

  <?php if (empty($covoiturages)):  // Inclusion des éléments d'en-tête, barre de navigation et base de données if (empty($covoiturages)): ?>
    <p class="text-muted">Aucun trajet trouvé pour votre recherche.</p>
  <?php else: // Inclusion des éléments d'en-tête, barre de navigation et base de données else: ?>
    <div class="row">
      <?php foreach ($covoiturages as $trajet): // Inclusion des éléments d'en-tête, barre de navigation et base de données foreach ($covoiturages as $trajet): ?>
        <div class="col-md-6 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
              <img src="uploads/<?= htmlspecialchars($trajet['photo']) ?>" alt="photo" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                <strong><?= htmlspecialchars($trajet['chauffeur']) ?></strong>
              </div>
              <p><?= htmlspecialchars($trajet['depart']) ?> → <?= htmlspecialchars($trajet['arrivee']) ?></p>
              <p>Départ : <?= htmlspecialchars($trajet['date_depart']) ?></p>
              <p>Prix : <?= htmlspecialchars($trajet['prix']) ?> €</p>
              <p>Véhicule : <?= htmlspecialchars($trajet['vehicule']) ?></p>
              <p><strong>Places disponibles :</strong> <?= $trajet['places'] ?></p>
              <?php if (!empty($trajet['eco'])): // Inclusion des éléments d'en-tête, barre de navigation et base de données if (!empty($trajet['eco'])): ?>
                <span class="badge bg-success">🌿 Éco</span>
              <?php endif; // Inclusion des éléments d'en-tête, barre de navigation et base de données endif; ?>
              <a href="detail.php?id=<?= $trajet['id'] ?>" class="btn btn-outline-success btn-sm">Voir le détail</a>
            </div>
          </div>
        </div>
      <?php endforeach; // Inclusion des éléments d'en-tête, barre de navigation et base de données endforeach; ?>
    </div>
  <?php endif; // Inclusion des éléments d'en-tête, barre de navigation et base de données endif; ?>
</main>

<?php include("../includes/footer.php"); // Inclusion du footer; ?>


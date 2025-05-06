<style>
  main {
    background-color:  #F7F6CF;
  }
</style>

<?php
include("includes/header.php");
include("includes/navbar.php");
//include("includes/mock.php"); // Données simulées//
require_once 'includes/db.php';

// Requête pour récupérer les trajets à venir depuis la BDD
$filtreStatut = $_GET['statut'] ?? null;

$sql = "
    SELECT 
        t.id,
        t.adresse_depart AS depart,
        t.adresse_arrivee AS arrivee,
        DATE(t.date_depart) AS date,
        TIME(t.date_depart) AS heure_depart,
        TIME(t.date_arrivee) AS heure_arrivee,
        t.prix,
        t.statut,
        u.pseudo AS chauffeur,
        u.photo,
        CONCAT(v.marque, ' ', v.modele) AS vehicule,
        v.places,
        v.preferences_perso,
        v.eco,
        5 AS note
    FROM trajets t
    JOIN utilisateurs u ON t.conducteur_id = u.id
    JOIN vehicules v ON t.vehicule_id = v.id
";

if ($filtreStatut) {
    $sql .= " WHERE t.statut = ? ORDER BY t.date_depart ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$filtreStatut]);
} else {
    $sql .= " ORDER BY t.date_depart ASC";
    $stmt = $pdo->query($sql);
}



$covoiturages = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* Récupération des filtres depuis l'URL*/
$depart = $_POST["depart"] ?? '';
$arrivee = $_POST['arrivee'] ?? '';
$date = $_POST['date'] ?? '';
$filtre_eco = isset($_POST['eco']) ? true : false;
$filtre_note = $_POST['note'] ?? '';
$filtre_prix = $_POST['prix'] ?? '';
?>

<main class="py-5 px-4">
  <h2>Résultats de recherche</h2>

  <!-- Formulaire de filtres -->
  <form method="POST" class="row g-3 mb-4">
    <div class="col-md-3">
      <input type="text" name="depart" class="form-control" placeholder="Départ" value="<?= htmlspecialchars($depart) ?>">
    </div>
    <div class="col-md-3">
      <input type="text" name="arrivee" class="form-control" placeholder="Arrivée" value="<?= htmlspecialchars($arrivee) ?>">
    </div>
    <div class="col-md-2">
      <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($date) ?>">
    </div>
    <div class="col-md-2">
      <select name="note" class="form-select">
        <option value="">Note min.</option>
        <option value="4" <?= $filtre_note == '4' ? 'selected' : '' ?>>4+</option>
        <option value="4.5" <?= $filtre_note == '4.5' ? 'selected' : '' ?>>4.5+</option>
      </select>
    </div>
    <div class="col-md-2">
      <select name="prix" class="form-select">
        <option value="">Prix max.</option>
        <option value="10" <?= $filtre_prix == '10' ? 'selected' : '' ?>>10 €</option>
        <option value="15" <?= $filtre_prix == '15' ? 'selected' : '' ?>>15 €</option>
        <option value="20" <?= $filtre_prix == '20' ? 'selected' : '' ?>>20 €</option>
      </select>
    </div>
    <div class="col-md-2 form-check mt-2">
      <input class="form-check-input" type="checkbox" name="eco" id="eco" <?= $filtre_eco ? 'checked' : '' ?>>
      <label class="form-check-label" for="eco">Éco-responsable</label>
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-success w-100">Filtrer</button>
    </div>
  </form>

  <!-- Affichage des résultats -->
  <div class="row">
    <form method="GET" class="mb-4 d-flex align-items-center">
      <label class="me-2 fw-bold" for="statut">Filtrer par statut :</label>
      <select name="statut" id="statut" class="form-select w-auto me-2" onchange="this.form.submit()">
          <option value="">Tous</option>
          <option value="à venir" <?= ($_GET['statut'] ?? '') === 'à venir' ? 'selected' : '' ?>>À venir</option>
          <option value="en cours" <?= ($_GET['statut'] ?? '') === 'en cours' ? 'selected' : '' ?>>En cours</option>
          <option value="terminé" <?= ($_GET['statut'] ?? '') === 'terminé' ? 'selected' : '' ?>>Terminé</option>
      </select>
    </form>

    <?php
    $resultats = array_filter($covoiturages, function($trajet) use ($depart, $arrivee, $date, $filtre_eco, $filtre_note, $filtre_prix) {
      if ($depart && stripos($trajet['depart'], $depart) === false) return false;
      if ($arrivee && stripos($trajet['arrivee'], $arrivee) === false) return false;
      if ($date && $trajet['date'] != $date) return false;
      if ($filtre_eco && !$trajet['eco']) return false;
      if ($filtre_note && $trajet['note'] < floatval($filtre_note)) return false;
      if ($filtre_prix && $trajet['prix'] > floatval($filtre_prix)) return false;
      return true;
    });

    if (empty($resultats)) {
      echo "<p>Aucun covoiturage trouvé avec ces critères.</p>";
    } else {
      foreach ($resultats as $trajet) {
        ?>
          <div class="col-md-6 mb-4 px-4">
            <div class="card shadow-sm">
              <div class="row g-0">
                <div class="col-md-4">
                  <img src="uploads/<?= htmlspecialchars($trajet['photo']) ?>" class="img-fluid rounded-start" alt="<?= htmlspecialchars($trajet['chauffeur']) ?>">
                </div>
                <div class="col-md-8">
                  <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($trajet['chauffeur']) ?> (<?= htmlspecialchars($trajet['note']) ?> ⭐)</h5>
                    <?php
                    $badge = '';
                    switch ($trajet['statut']) {
                        case 'à venir':
                            $badge = '<span class="badge bg-secondary">À venir</span>';
                            break;
                        case 'en cours':
                            $badge = '<span class="badge bg-warning text-dark">🚗 En cours</span>';
                            break;
                        case 'terminé':
                            $badge = '<span class="badge bg-success">🏁 Terminé</span>';
                            break;
                        case 'annulé':
                            $badge = '<span class="badge bg-danger">❌ Annulé</span>';
                            break;
                    }
                    echo $badge;
                    ?>
                    <p class="card-text">
                      <strong><?= htmlspecialchars($trajet['depart']) ?> → <?= htmlspecialchars($trajet['arrivee']) ?></strong><br>
                      Départ : <?= htmlspecialchars($trajet['heure_depart']) ?> | Arrivée : <?= htmlspecialchars($trajet['heure_arrivee']) ?><br>
                      Véhicule : <?= htmlspecialchars($trajet['vehicule']) ?><br>
                      <?php if (!empty($trajet['eco'])): ?>
                        <span class="badge bg-success">Éco</span>
                      <?php endif; ?>
                    </p>
                    <p class="card-text">
                      <span class="text-success fw-bold"><?= htmlspecialchars($trajet['prix']) ?> €</span> – <?= htmlspecialchars($trajet['places']) ?> place(s) dispo
                    </p>
                    <a href="detail.php?id=<?= urlencode($trajet['id']) ?>" class="btn btn-outline-success btn-sm">Voir le détail</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php
        }
        
    }
    ?>
  </div>
</main>

<?php include("includes/footer.php"); ?>

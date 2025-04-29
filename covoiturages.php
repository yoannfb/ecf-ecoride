<?php
include("includes/header.php");
include("includes/navbar.php");
include("includes/mock.php"); // Données simulées
require_once 'includes/db.php';


// Récupération des filtres depuis l'URL
$depart = $_POST["depart"] ?? '';
$arrivee = $_POST['arrivee'] ?? '';
$date = $_POST['date'] ?? '';
$filtre_eco = isset($_POST['eco']) ? true : false;
$filtre_note = $_POST['note'] ?? '';
$filtre_prix = $_POST['prix'] ?? '';
?>

<main class="pt-5">
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
        <div class="col-md-6 mb-4">
          <div class="card shadow-sm">
            <div class="row g-0">
              <div class="col-md-4">
                <img src="<?= htmlspecialchars($trajet['photo']) ?>" class="img-fluid rounded-start" alt="<?= htmlspecialchars($trajet['chauffeur']) ?>">
              </div>
              <div class="col-md-8">
                <div class="card-body">
                  <h5 class="card-title"><?= htmlspecialchars($trajet['chauffeur']) ?> (<?= htmlspecialchars($trajet['note']) ?> ⭐)</h5>
                  <p class="card-text">
                    <strong><?= htmlspecialchars($trajet['depart']) ?> → <?= htmlspecialchars($trajet['arrivee']) ?></strong><br>
                    Départ : <?= htmlspecialchars($trajet['heure_depart']) ?> | Arrivée : <?= htmlspecialchars($trajet['heure_arrivee']) ?><br>
                    Véhicule : <?= htmlspecialchars($trajet['vehicule']) ?><br>
                    <?= $trajet['eco'] ? '<span class="badge bg-success">Éco</span>' : '' ?>
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

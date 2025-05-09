<style>
    .vehicule {
        background: #F7F6CF;
        font-family: EB Garamond;
    }
</style>


<?php
session_start();
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $plaque = $_POST['plaque'];
    $modele = $_POST['modele'];
    $marque = $_POST['marque'];
    $couleur = $_POST['couleur'];
    $date_immat = $_POST['date_immat'];
    $places = $_POST['places'];
    $fumeur = isset($_POST['fumeur']) ? 1 : 0;
    $animaux = isset($_POST['animaux']) ? 1 : 0;
    $eco = isset($_POST['eco']) ? 1 : 0;
    $preferences = $_POST['preferences_perso'] ?? '';

    $stmt = $pdo->prepare("UPDATE vehicules SET plaque = ?, modele = ?, marque = ?, couleur = ?, date_immat = ?, places = ?, fumeur = ?, animaux = ?, eco = ?, preferences_perso = ? WHERE id = ?");
    $stmt->execute([$plaque, $modele, $marque, $couleur, $date_immat, $places, $fumeur, $animaux, $eco, $preferences, $id]);

    header("Location: espace_utilisateur.php?success=vehicule_modifie");
    exit();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$vehicule_id = $_GET['id'] ?? null;

$stmt = $pdo->prepare("SELECT * FROM vehicules WHERE id = ? AND utilisateur_id = ?");
$stmt->execute([$vehicule_id, $user_id]);
$vehicule = $stmt->fetch();

if (!$vehicule) {
    echo "Véhicule introuvable ou accès refusé.";
    exit();
}

// TRAITEMENT DE SUPPRESSION SI DEMANDE POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $vehicule_id = $_POST['id'];

    // Vérifie si le véhicule est utilisé dans un trajet
    $check = $pdo->prepare("SELECT COUNT(*) FROM trajets WHERE vehicule_id = ?");
    $check->execute([$vehicule_id]);
    $nbTrajets = $check->fetchColumn();

    if ($nbTrajets > 0) {
        header("Location: espace_utilisateur.php?erreur=suppression_vehicule");
        exit();
    }

    // Supprimer le véhicule
    $delete = $pdo->prepare("DELETE FROM vehicules WHERE id = ?");
    $delete->execute([$vehicule_id]);

    header("Location: espace_utilisateur.php?success=vehicule_supprime");
    exit();
}

?>

<div class="vehicule py-5 px-3">
    <h2>Modifier mon véhicule</h2>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $vehicule['id'] ?>">

        <div class="mb-3">
            <label>Plaque</label>
            <input type="text" name="plaque" class="form-control" value="<?= htmlspecialchars($vehicule['plaque']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Modèle</label>
            <input type="text" name="modele" class="form-control" value="<?= htmlspecialchars($vehicule['modele']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Marque</label>
            <input type="text" name="marque" class="form-control" value="<?= htmlspecialchars($vehicule['marque']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Couleur</label>
            <input type="text" name="couleur" class="form-control" value="<?= htmlspecialchars($vehicule['couleur']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Date d'immatriculation</label>
            <input type="date" name="date_immat" class="form-control" value="<?= $vehicule['date_immat'] ?>" required>
        </div>
        <div class="mb-3">
            <label>Places</label>
            <input type="number" name="places" class="form-control" value="<?= $vehicule['places'] ?>" required>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="fumeur" value="1" <?= $vehicule['fumeur'] ? 'checked' : '' ?>>
            <label class="form-check-label">Accepte fumeurs</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="animaux" value="1" <?= $vehicule['animaux'] ? 'checked' : '' ?>>
            <label class="form-check-label">Accepte animaux</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="eco" value="1" <?= $vehicule['eco'] ? 'checked' : '' ?>>
            <label class="form-check-label">Véhicule électrique</label>
        </div>
        <div class="mb-3">
            <label>Préférences personnelles</label>
            <textarea name="preferences_perso" class="form-control"><?= htmlspecialchars($vehicule['preferences_perso']) ?></textarea>
        </div>

        <button type="submit" class="btn btn-success">Enregistrer les modifications</button>
    </form>
    <hr>
    <form method="POST" onsubmit="return confirm('Supprimer définitivement ce véhicule ?');">
        <input type="hidden" name="id" value="<?= $vehicule['id'] ?>">
        <input type="hidden" name="delete" value="1">
        <button type="submit" class="btn btn-outline-danger">🗑 Supprimer ce véhicule</button>
    </form>


</div>

<?php require_once 'includes/footer.php'; ?>

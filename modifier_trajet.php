<?php
session_start();
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$trajet_id = $_GET['id'] ?? null;

if (!$trajet_id) {
    header("Location: espace_utilisateur.php");
    exit();
}

// Vérifie que le trajet appartient à l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM trajets WHERE id = ? AND conducteur_id = ?");
$stmt->execute([$trajet_id, $user_id]);
$trajet = $stmt->fetch();

if (!$trajet) {
    echo "Trajet introuvable ou non autorisé.";
    exit();
}

// Récupère les véhicules de l'utilisateur pour modifier si besoin
$stmt2 = $pdo->prepare("SELECT id, marque, modele FROM vehicules WHERE utilisateur_id = ?");
$stmt2->execute([$user_id]);
$vehicules = $stmt2->fetchAll();
?>


<div class="container mt-5">
    <h2>Modifier un trajet</h2>

    <form method="POST" action="traitement_modifier_trajet.php">
        <input type="hidden" name="id" value="<?= $trajet['id'] ?>">

        <div class="mb-3">
            <label>Véhicule utilisé</label>
            <select name="vehicule_id" class="form-select" required>
                <?php foreach ($vehicules as $v): ?>
                    <option value="<?= $v['id'] ?>" <?= $v['id'] == $trajet['vehicule_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($v['marque']) . ' ' . htmlspecialchars($v['modele']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Adresse de départ</label>
            <input type="text" name="adresse_depart" class="form-control" value="<?= htmlspecialchars($trajet['adresse_depart']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Adresse d’arrivée</label>
            <input type="text" name="adresse_arrivee" class="form-control" value="<?= htmlspecialchars($trajet['adresse_arrivee']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Date et heure de départ</label>
            <input type="datetime-local" name="date_depart" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($trajet['date_depart'])) ?>" required>
        </div>

        <div class="mb-3">
            <label>Date et heure d’arrivée</label>
            <input type="datetime-local" name="date_arrivee" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($trajet['date_arrivee'])) ?>" required>
        </div>

        <div class="mb-3">
            <label>Prix (€)</label>
            <input type="number" step="0.01" name="prix" class="form-control" value="<?= $trajet['prix'] ?>" required>
        </div>

        <button type="submit" class="btn btn-success">Enregistrer les modifications</button>
        <a href="espace_utilisateur.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
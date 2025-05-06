<style>
    .trajet {
        background: #F7F6CF;
        font-family: EB Garamond;
    }
</style>

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

// Vérifie que l'utilisateur est bien chauffeur
$stmt = $pdo->prepare("SELECT role FROM utilisateurs WHERE id = ?");
$stmt->execute([$user_id]);
$role = $stmt->fetchColumn();
if (!in_array($role, ['chauffeur', 'les-deux'])) {
    echo "Accès réservé aux chauffeurs.";
    exit();
}

// Récupère les véhicules du chauffeur
$stmt = $pdo->prepare("SELECT id, marque, modele FROM vehicules WHERE utilisateur_id = ?");
$stmt->execute([$user_id]);
$vehicules = $stmt->fetchAll();
?>

<div class="trajet py-5 px-3">
    <h1 class="mb-4">Saisir un trajet</h1>

    <form action="traitement_saisie_trajet.php" method="POST" class="border p-4 rounded bg-light">
        <div class="mb-3">
            <label for="vehicule">Véhicule</label>
            <select name="vehicule_id" id="vehicule" class="form-select" required>
                <option value="">-- Sélectionnez un véhicule --</option>
                <?php foreach ($vehicules as $v): ?>
                    <option value="<?= $v['id'] ?>">
                        <?= htmlspecialchars($v['marque']) . ' ' . htmlspecialchars($v['modele']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Adresse de départ</label>
            <input type="text" name="adresse_depart" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Adresse d'arrivée</label>
            <input type="text" name="adresse_arrivee" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Date et heure de départ</label>
            <input type="datetime-local" name="date_depart" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Date et heure d'arrivée</label>
            <input type="datetime-local" name="date_arrivee" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Prix (€)</label>
            <input type="number" name="prix" step="0.01" min="0" class="form-control" required>
            <small class="text-muted">2 crédits seront prélevés par la plateforme.</small>
        </div>

        <button type="submit" class="btn btn-success">Créer le trajet</button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>

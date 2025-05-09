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


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vehicule_id = $_POST['vehicule_id'];
    $stmt = $pdo->prepare("SELECT places FROM vehicules WHERE id = ?");
    $stmt->execute([$vehicule_id]);
    $places = $stmt->fetchColumn();

    $stmt = $pdo->prepare("INSERT INTO trajets 
        (conducteur_id, vehicule_id, adresse_depart, adresse_arrivee, date_depart, date_arrivee, prix, places_disponibles)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $user_id,
        $vehicule_id,
        $_POST['adresse_depart'],
        $_POST['adresse_arrivee'],
        $_POST['date_depart'],
        $_POST['date_arrivee'],
        $_POST['prix'],
        $places
    ]);

    header("Location: espace_utilisateur.php?success=participation");
exit;

}
?>

<div class="trajet py-5 px-3">
    <h1 class="mb-4">Saisir un trajet</h1>

    <form method="POST" class="border p-4 rounded bg-light">
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

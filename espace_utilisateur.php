<?php
session_start();
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupère les infos de l'utilisateur
$stmt = $pdo->prepare("SELECT pseudo, email, credits, role FROM utilisateurs WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Récupère les véhicules de l'utilisateur
$stmt2 = $pdo->prepare("SELECT * FROM vehicules WHERE utilisateur_id = ?");
$stmt2->execute([$user_id]);
$vehicules = $stmt2->fetchAll();
?>

<?php if (isset($_GET['success']) && $_GET['success'] === 'trajet_supprime'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        ✅ Le trajet a bien été supprimé.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['erreur']) && $_GET['erreur'] === 'trajet_introuvable'): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        🚫 Impossible de supprimer ce trajet (non trouvé ou non autorisé).
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['success']) && $_GET['success'] === 'trajet_modifie'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        ✅ Le trajet a été modifié avec succès.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
<?php endif; ?>


<?php if (isset($_GET['erreur']) && $_GET['erreur'] === 'suppression_vehicule'): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        🚫 Ce véhicule ne peut pas être supprimé car il est utilisé dans un ou plusieurs trajets.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
<?php endif; ?>
<div class="container mt-5">
    <h1 class="mb-4">Espace utilisateur</h1>

    <!-- Infos utilisateur -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            Mes informations
        </div>
        <div class="card-body">
            <p><strong>Pseudo :</strong> <?= htmlspecialchars($user['pseudo']) ?></p>
            <p><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p><strong>Rôle :</strong> <?= htmlspecialchars($user['role']) ?></p>
            <p><strong>Crédits :</strong> <?= $user['credits'] ?> crédits</p>
        </div>
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                Modifier ma photo de profil
            </div>
            <div class="card-body">
                <?php if (!empty($user['photo'])): ?>
                    <img src="uploads/<?= htmlspecialchars($user['photo']) ?>" alt="Photo de profil" class="mb-3 rounded-circle" width="100">
                <?php endif; ?>

                <form action="traitement_photo.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <input type="file" name="photo" class="form-control" accept="image/*" required>
                </div>
                <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Véhicules enregistrés -->
    <div class="card">
        <div class="card-header bg-info text-white">
            Mes véhicules enregistrés
        </div>
        <div class="card-body">
            <?php if ($vehicules): ?>
                <div class="row">
                    <?php foreach ($vehicules as $v): ?>
                        <div class="col-md-6 mb-4">
                            <div class="border rounded p-3 bg-light">
                                <h5><?= htmlspecialchars($v['marque']) ?> <?= htmlspecialchars($v['modele']) ?></h5>
                                <ul class="list-unstyled mb-0">
                                    <li><strong>Couleur :</strong> <?= htmlspecialchars($v['couleur']) ?></li>
                                    <li><strong>Plaque :</strong> <?= htmlspecialchars($v['plaque']) ?></li>
                                    <li><strong>Date immatriculation :</strong> <?= htmlspecialchars($v['date_immat']) ?></li>
                                    <li><strong>Places :</strong> <?= $v['places'] ?></li>
                                    <li><strong>Fumeur :</strong> <?= $v['fumeur'] ? 'Oui' : 'Non' ?></li>
                                    <li><strong>Animaux :</strong> <?= $v['animaux'] ? 'Oui' : 'Non' ?></li>
                                    <li><strong>Préférences :</strong> <?= nl2br(htmlspecialchars($v['preferences_perso'])) ?></li>
                                </ul>
                                <a href="modifier_vehicule.php?id=<?= $v['id'] ?>" class="btn btn-sm btn-outline-primary mt-2">Modifier</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted">Aucun véhicule enregistré pour le moment.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Mes Trajets -->
    <h4 class="mt-5">Mes trajets créés</h4>
    <?php
    $stmt = $pdo->prepare("SELECT * FROM trajets WHERE conducteur_id = ? ORDER BY date_depart DESC");
    $stmt->execute([$user_id]);
    $mes_trajets = $stmt->fetchAll();
    ?>

    <?php if ($mes_trajets): ?>
        <ul class="list-group">
            <?php foreach ($mes_trajets as $trajet): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong><?= htmlspecialchars($trajet['adresse_depart']) ?> → <?= htmlspecialchars($trajet['adresse_arrivee']) ?></strong><br>
                        <?= $trajet['date_depart'] ?> — <?= $trajet['prix'] ?> €
                    </div>
                    <div>
                        <a href="modifier_trajet.php?id=<?= $trajet['id'] ?>" class="btn btn-sm btn-outline-primary">Modifier</a>
                        <form action="supprimer_trajet.php" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce trajet ?');">
                            <input type="hidden" name="trajet_id" value="<?= $trajet['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="text-muted">Aucun trajet créé pour le moment.</p>
    <?php endif; ?>


    <!-- Boutons utiles -->
    <div class="mt-4">
        <?php if (in_array($user['role'], ['chauffeur', 'les-deux'])): ?>
        <a href="saisir_trajet.php" class="btn btn-success">Créer un trajet</a>
        <?php endif; ?>
        <a href="utilisateur.php" class="btn btn-outline-secondary">Modifier mon rôle / véhicule</a>
        <a href="index.php" class="btn btn-outline-primary">Retour à l'accueil</a>
        <a href="logout.php" class="btn btn-danger">Se déconnecter</a>
    </div>
</div>



<?php require_once 'includes/footer.php'; ?>
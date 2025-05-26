<style>
    .user {
        background-color: #F7F6CF;
        font-family: EB Garamond;
    }
    .container {
        max-width: 1600px;
        margin: 0 auto ;
        padding: 0 20px 0;
    }
    h1 {
        text-transform: uppercase;
        color: black;
        font-weight: 900;
        color: transparent;
        font-size: 0px;
    }
    h1 span {
        display: inline-block;
        position: relative;
        overflow: hidden;
        font-size: clamp(20px, 8vw, 60px);
        border-radius: 30px;
    }
    h1 span::after {
        content:"";
        display: block;
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        transform: translateX(-100%);
        background: rgba(40, 167, 69, 1);
    }
    h1:nth-child(1) {
        font-weight: 300;
        animation: txt-appearance 0s 1s
        forwards;
    }
    h1:nth-child(1) span::after {
        background: rgba(40, 167, 69, 1);
        animation: slide-in 0.75s ease-out forwards,
        slide-out 0.75s 1s ease-out forwards;
    }
    @keyframes slide-in {
        100% {
            transform: translateX(0%);
        }
    }
    @keyframes slide-out {
        100% {
            transform: translateX(100%);
        }
    }
    @keyframes txt-appearance {
        100% {
            color: black;
        }
    }

    .info {
        background: rgba(40, 167, 69, 1)!important;
        color: white!important;
    }
</style>

<?php
// Démarre la session et inclut les fichiers nécessaires (en-tête, navigation, base de données)
session_start();
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require 'includes/db.php';

// Redirige l'utilisateur vers la page de connexion s'il n'est pas authentifié
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

// Récupère l'ID de l'utilisateur connecté depuis la session
$user_id = $_SESSION['user_id'];

// Récupère les infos de l'utilisateur
// Requête pour obtenir les informations de l'utilisateur connecté
$stmt = $pdo->prepare("SELECT pseudo, email, credits, role FROM utilisateurs WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Récupère les véhicules de l'utilisateur
// Requête pour récupérer les véhicules liés à cet utilisateur
$stmt2 = $pdo->prepare("SELECT * FROM vehicules WHERE utilisateur_id = ?");
$stmt2->execute([$user_id]);
$vehicules = $stmt2->fetchAll();
?>

<?php
// Trajets auxquels l'utilisateur participe
$stmt = $pdo->prepare("
    SELECT t.*, u.pseudo AS conducteur
    FROM participations p
    JOIN trajets t ON p.covoiturage_id = t.id
    JOIN utilisateurs u ON t.conducteur_id = u.id
    WHERE p.utilisateur_id = ?
");
$stmt->execute([$user_id]);
$trajets_participes = $stmt->fetchAll();
?>


<?php 
// Démarre la partie de suppression d'un trajet et inclut les fichiers nécessaires (en-tête, navigation, base de données) if (isset($_GET['success']) && $_GET['success'] === 'trajet_supprime'):
if (isset($_GET['success']) && $_GET['success'] === 'trajet_supprime'): ?>
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

<?php
// Démarre la partie de modification d'un trajet et inclut les fichiers nécessaires (en-tête, navigation, base de données) if (isset($_GET['success']) && $_GET['success'] === 'trajet_modifie'):
if (isset($_GET['success']) && $_GET['success'] === 'trajet_modifie'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        ✅ Le trajet a été modifié avec succès.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
<?php endif; ?>


<?php
// Démarre la partie de suppression d'un véhicule et inclut les fichiers nécessaires (en-tête, navigation, base de données) if (isset($_GET['erreur']) && $_GET['erreur'] === 'suppression_vehicule'):
if (isset($_GET['erreur']) && $_GET['erreur'] === 'suppression_vehicule'): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        🚫 Ce véhicule ne peut pas être supprimé car il est utilisé dans un ou plusieurs trajets.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
<?php endif; ?>
<div class="user p-5">
    <div class="container">
        <h1>
            <span>
                Espace utilisateur
            </span>
        </h1>
    </div>
    

    <!-- Infos utilisateur -->
    <div class="card mb-4">
        <div class="card-header info">
            Mes informations
        </div>
        <div class="card-body">
            <p><strong>Pseudo :</strong> <?= htmlspecialchars($user['pseudo']) ?></p>
            <p><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p><strong>Rôle :</strong> <?= htmlspecialchars($user['role']) ?></p>
            <p><strong>Crédits :</strong> <?= $user['credits'] ?> crédits</p>
        </div>
        <div class="card">
            <div class="card-header bg-secondary text-white">
                Modifier ma photo de profil
            </div>
            <div class="card-body">
                <?php 
                // Démarre la partie de traitement de la photo de profil et inclut les fichiers nécessaires (en-tête, navigation, base de données) if (!empty($user['photo'])):
                if (!empty($user['photo'])): ?>
                    <img src="uploads/<?= htmlspecialchars($user['photo']) ?>" alt="Photo de profil" class="mb-3 rounded-circle" width="100">
                <?php endif; ?>

                <form action="traitement_photo.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <input type="file" name="photo" class="form-control" accept="image/*" required>
                </div>
                <button type="submit" class="btn btn-secondary">Mettre à jour</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Véhicules enregistrés -->
    <div class="card">
        <div class="card-header bg-warning text-dark">
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
                                <a href="modifier_vehicule.php?id=<?= $v['id'] ?>" class="btn btn-sm btn-warning mt-2">Modifier</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted">Aucun véhicule enregistré pour le moment.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <h2 class="mt-5">🧍 Mes participations</h2>
    <?php 
    // Démarre la partie sur la participation aux trajets et inclut les fichiers nécessaires (en-tête, navigation, base de données) if (empty($trajets_participes)):
    if (empty($trajets_participes)): ?>
        <p>Vous n'avez rejoint aucun covoiturage.</p>
    <?php else: ?>
        <ul class="list-group">
            <?php foreach ($trajets_participes as $trajet): ?>
                <li class="list-group-item">
                    <strong><?= htmlspecialchars($trajet['adresse_depart']) ?></strong> → 
                    <strong><?= htmlspecialchars($trajet['adresse_arrivee']) ?></strong><br>
                    Date : <?= date('d/m/Y', strtotime($trajet['date_depart'])) ?><br>
                    Conducteur : <?= htmlspecialchars($trajet['conducteur']) ?><br>

                    <a href="annuler_participation.php?id=<?= $trajet['id'] ?>" 
                        class="btn btn-outline-danger btn-sm mt-2"
                        onclick="return confirm('Annuler votre participation ?');">
                        ❌ Annuler
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>


    <!-- Mes Trajets -->
    <h4 class="mt-5">Mes trajets créés</h4>
    <?php
    // Démarre la création d'un trajet et inclut les fichiers nécessaires (en-tête, navigation, base de données)
    $stmt = $pdo->prepare("SELECT * FROM trajets WHERE conducteur_id = ? ORDER BY date_depart DESC");
    $stmt->execute([$user_id]);
    $mes_trajets = $stmt->fetchAll();
    ?>

    <?php
    // Démarre la partie gestion des trajets et inclut les fichiers nécessaires (en-tête, navigation, base de données)
    if ($mes_trajets): ?>
        <ul class="list-group">
            <?php foreach ($mes_trajets as $trajet): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong><?= htmlspecialchars($trajet['adresse_depart']) ?> → <?= htmlspecialchars($trajet['adresse_arrivee']) ?></strong><br>
                        <?= $trajet['date_depart'] ?> — <?= $trajet['prix'] ?> €
                    </div>
                    <div>
                        <a href="modifier_trajet.php?id=<?= $trajet['id'] ?>" class="btn btn-sm btn-warning">Modifier</a>
                    </div>
                    <?php if ($trajet['statut'] === 'à venir'): ?>
                        <form action="changer_statut_trajet.php" method="POST" class="d-inline">
                            <input type="hidden" name="id" value="<?= $trajet['id'] ?>">
                            <input type="hidden" name="action" value="start">
                            <button type="submit" class="btn btn-sm btn-outline-success">🚗 Démarrer</button>
                        </form>
                    <?php elseif ($trajet['statut'] === 'en cours'): ?>
                        <form action="changer_statut_trajet.php" method="POST" class="d-inline">
                            <input type="hidden" name="id" value="<?= $trajet['id'] ?>">
                            <input type="hidden" name="action" value="finish">
                            <button type="submit" class="btn btn-sm btn-outline-primary">🏁 Arrivée</button>
                        </form>
                    <?php elseif ($trajet['statut'] === 'terminé'): ?>
                        <span class="badge bg-success">✅ Trajet terminé</span>
                    <?php endif; ?>
                    <form action="ajouter_avis.php" method="post" style="margin-top:10px;">
                        <input type="hidden" name="trajet_id" value="<?= $trajet['id'] ?>">
                        <label for="note_<?= $trajet['id'] ?>">Note :</label>
                        <select name="note" id="note_<?= $trajet['id'] ?>" required>
                            <option value="5">5 - Excellent</option>
                            <option value="4">4 - Très bien</option>
                            <option value="3">3 - Bien</option>
                            <option value="2">2 - Moyen</option>
                            <option value="1">1 - Mauvais</option>
                        </select>
                        <br>
                        <label for="commentaire_<?= $trajet['id'] ?>">Commentaire :</label><br>
                        <textarea name="commentaire" id="commentaire_<?= $trajet['id'] ?>" rows="3" cols="40" required></textarea><br>
                        <button type="submit">Envoyer l'avis</button>
                    </form>

                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="text-muted">Aucun trajet créé pour le moment.</p>
    <?php endif; ?>


    <!-- Boutons utiles -->
    <div class="mt-4 p-2">
        <?php if (in_array($user['role'], ['chauffeur', 'les-deux'])): ?>
        <a href="saisir_trajet.php" class="btn btn-success">Créer un trajet</a>
        <?php endif; ?>
        <a href="utilisateur.php" class="btn btn-warning">Modifier mon rôle / véhicule</a>
        <a href="index.php" class="btn btn-outline-secondary">Retour à l'accueil</a>
        <a href="logout.php" class="btn btn-outline-danger">Se déconnecter</a>
    </div>
</div>



<?php require_once 'includes/footer.php'; // Inclusion du footer; ?>
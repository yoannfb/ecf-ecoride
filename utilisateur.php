<style>
    .back {
        width: 100%;
        height: 100%;
        background-image: url("assets/espace utilisateur.jpg");
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 50%;
        background-color: #F7F6CF;
    }
    .container-formulaire {
        max-width: 600px;
        margin: 40px auto;
        padding: 20px;
        background-color: #F7F6CF;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .container-formulaire form input,
    .container-formulaire form select,
    .container-formulaire form textarea {
        width: 100%;
        margin-bottom: 12px;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
    }

    .container-formulaire button {
        width: 100%;
        padding: 12px;
        background-color: #218838;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: bold;
        transition: background-color 0.3s;
    }   

    .container-formulaire button:hover {
  background-color: #1e7e34;
    }

</style>


<?php
session_start();
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'includes/db.php';

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php?error=Veuillez vous connecter pour accéder à cette page.");
    exit;
}

// Récupération des infos utilisateur depuis la base
try {
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    die("Erreur lors du chargement du profil.");
}
?>

<div class="back d-flex flex-column text-center align-items-center pt-5">
    <h2 class="mb-4">Bienvenue, <?= htmlspecialchars($user['pseudo']) ?> 👋</h2>
    <div class="d-flex flex-row justify-content-around">
        <ul class="list-group d-flex flex-row justify-content-around">
            <li class="list-group-item mx-1 border border-warning-subtle rounded"><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></li>
            <li class="list-group-item mx-1 border border-warning-subtle rounded"><strong>Crédits disponibles :</strong> <?= $user['credits'] ?></li>
            <div class="mx-1">
                <a href="logout.php" class="btn btn-warning">Se déconnecter</a>
            </div>
        </ul>
    </div>
    
    <div class="container container-formulaire">
        <h3 class="mt-5">Mon rôle :</h3>
        <form action="traitement_role.php" method="POST" class="w-75 mx-auto">
            <select name="role" id="role" class="form-select mb-3" onchange="toggleForm()" required>
                <option value="">-- Sélectionnez --</option>
                <option value="passager">Passager</option>
                <option value="chauffeur">Chauffeur</option>
                <option value="les-deux">Passager + Chauffeur</option>
            </select>

            <div class="row" id="chauffeur-fields" style="display: none;">
                <div class="d-flex">
                    <div class="d-flex flex-column">
                        <div class="mb-3 mx-3">
                            <label>Plaque d'immatriculation</label>
                            <input type="text" name="plaque" class="form-control">
                        </div>
                        <div class="mb-3 mx-3">
                            <label>Modèle</label>
                            <input type="text" name="modele" class="form-control">
                        </div>
                    </div>
                    <div class="d-flex flex-column">
                        <div class="mb-3 mx-3">
                            <label>Couleur</label>
                            <input type="text" name="couleur" class="form-control">
                        </div>
                        <div class="mb-3 mx-3">
                            <label>Places disponibles</label>
                            <input type="number" name="places" class="form-control" min="1">
                        </div>
                    </div>

                    <div class="d-flex flex-column">
                        <div class="mb-3 mx-3">
                            <label>Date de 1ère immatriculation</label>
                            <input type="date" name="date_immat" class="form-control">
                        </div>
                        <div class="mb-3 mx-3">
                            <label>Marque</label>
                            <input type="text" name="marque" class="form-control">
                        </div>
                    </div>
                    <div class="d-flex flex-column"> 
                        <div class="mb-3 mx-3">
                            <!--<label>Préférences</label><br>-->
                            <div class="form-check col-lg-1 col-md-1 col-sm-1">
                                <input class="form-check-input" type="checkbox" name="fumeur" value="1" id="fumeur">
                                <label class="form-check-label" for="fumeur">Accepte fumeurs</label>
                            </div>
                            <div class="form-check col-lg-1 col-md-1 col-sm-1">
                                <input class="form-check-input" type="checkbox" name="animaux" value="1" id="animaux">
                                <label class="form-check-label" for="animaux">Accepte animaux</label>
                            </div>
                            <textarea name="preferences_perso" class="form-control mt-2" placeholder="Autres préférences"></textarea>
                        </div>
                    </div>
                </div>
                
            </div>

            <button type="submit" class="btn btn-success mt-3">Enregistrer</button>
        </form>
    </div>
    

    
</div>

<script>
function toggleForm() {
    const role = document.getElementById('role').value;
    const chauffeurFields = document.getElementById('chauffeur-fields');
    chauffeurFields.style.display = (role === 'chauffeur' || role === 'les-deux') ? 'block' : 'none';
}
</script>


<?php require_once 'includes/footer.php'; ?>

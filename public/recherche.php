<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\RechercheController;

$isAjax = !empty($_GET['ajax']); // si ?ajax=1 dans l'URL → on veut du JSON

if ($isAjax) {
    header('Content-Type: application/json');
    $controller = new RechercheController();
    $controller->handleSearch();
    exit;
}

// Sinon, page HTML classique avec formulaire

?>
<!-- 
<main class="container">
    <h1>Résultats de votre recherche</h1>
    <form id="form-recherche" method="GET" action="recherche.php" style="margin-top: 1rem;">
        <input type="text" name="depart" id="depart" placeholder="Ville de départ" required>
        <input type="text" name="arrivee" id="arrivee" placeholder="Ville d’arrivée" required>
        <input type="date" name="date" id="date">
        <button type="submit">Rechercher</button>
    </form>
    <div id="recherche-resultats"></div>
</main>-->

<script src="js/index.js" defer></script>
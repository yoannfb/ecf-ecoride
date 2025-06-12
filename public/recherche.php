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

<main class="container">
    <h1>Résultats de votre recherche</h1>
    <div id="resultats"></div>
</main>

<script src="js/index.js" defer></script>
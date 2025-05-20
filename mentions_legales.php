<style>
    .principal {
        background-color: #873e23;
        width: 100%;
        margin:0;
        padding:0;
    }
    .container {
        background-color: #F7F6CF;
    }

</style>



<?php
session_start();
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<div class="principal">
    <div class="container mt-5">
        <h1>Mentions légales</h1>

        <h2>Éditeur du site</h2>
        <p>EcoRide – Plateforme de covoiturage écologique<br>
        Projet fictif réalisé dans le cadre du Titre Professionnel Développeur Web et Web Mobile (DWWM)<br>
        Responsable de publication : José, directeur technique</p>

        <h2>Adresse</h2>
        <p>[Adresse fictive ou campus Studi]<br>
        France</p>

        <h2>Email de contact</h2>
        <p><a href="mailto:contact@ecoride.fr">contact@ecoride.fr</a></p>

        <h2>Hébergement</h2>
        <p>Le site est hébergé par :<br>
        <strong>Heroku</strong> (Salesforce)<br>
        650 7th Street, San Francisco, CA<br>
        <a href="https://www.heroku.com">www.heroku.com</a></p>

        <h2>Propriété intellectuelle</h2>
        <p>Les contenus de ce site (textes, images, code…) sont la propriété exclusive d’EcoRide ou de ses partenaires. Toute reproduction sans autorisation est interdite.</p>

        <h2>Données personnelles</h2>
        <p>Les données collectées sont utilisées uniquement pour gérer les trajets sur EcoRide. Elles ne sont jamais transmises à des tiers.<br>
        Conformément au RGPD, vous disposez d’un droit d’accès, de modification et de suppression de vos données.<br>
        Pour exercer ce droit : <a href="mailto:contact@ecoride.fr">contact@ecoride.fr</a></p>

        <h2>Cookies</h2>
        <p>Le site peut utiliser des cookies à des fins de navigation ou d’analyse. En continuant votre navigation, vous acceptez leur utilisation.</p>

        <h2>Responsabilité</h2>
        <p>EcoRide ne peut être tenue responsable des dommages directs ou indirects résultant de l’utilisation de ce site ou des trajets proposés par ses membres.</p>
    </div>
</div>


<?php require_once 'includes/footer.php'; ?>
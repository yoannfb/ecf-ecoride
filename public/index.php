<?php require_once __DIR__ . '/../includes/header.php'; // ✅ sûr
?>

<!-- Bibliothèque AOS (Animate On Scroll) -->
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">



  <?php require_once __DIR__ . '/../includes/navbar.php';
; 
  require_once __DIR__ . '/../includes/db.php';
  ;?>

  


  <header class="header text-white text-center py-5">
    <h1>Bienvenue sur EcoRide 🍁</h1>
    <p>La plateforme de covoiturage écologique</p>
  </header>

  <main class="col-lg-12 col-md-12 col-sm-12 animation text-center pt-5">
    <h2>Présentation de l’entreprise</h2>
    <p class="text"></p>
    <div class="animation slider-container">
      <img src="assets/voiture4.png" class="slider-img-loop delay-0" alt="voiture1" id="voiture">
      <img src="assets/voiture5.png" class="slider-img-loop delay-1" alt="voiture2" id="voiture">
      <img src="assets/voiture6.png" class="slider-img-loop delay-2" alt="voiture3" id="voiture">
    </div>
  </main>

  <!--Section parallax 1-->
  <section id="parallax1" class="parallax-section">
        <div class="overlay"></div>
        <div class="container h-100">
            <div class="d-flex h-100 align-items-center">
            
            </div>
        </div>
  </section>
  <section class="py-4 px-2 section-content">
    <h3>Rechercher un itinéraire</h3>
    <form id="form-recherche" method="GET" action="recherche.php" class="row g-3">
      <div class="col-md-4">
        <input type="text" name="depart" id="depart" placeholder="Ville de départ" class="form-control" required>
      </div>
      <div class="col-md-4">
        <input type="text" name="arrivee" id="arrivee" placeholder="Ville d’arrivée" class="form-control" required>
      </div>
      <div class="col-md-4">
        <input type="date" name="date" id="date" class="form-control">
      </div>
      <div class="text-end">
        <button type="submit" class="btn btn-success">Rechercher</button>
      </div>
    </form>
    <div id="recherche-resultats"></div>

  </section>

  <?php require_once __DIR__ . '/../includes/footer.php';?>
  
  <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
  // attendre que AOS soit chargé
  if (typeof AOS !== "undefined") {
    AOS.init();
    console.log("✅ AOS initialisé !");
  } else {
    console.warn("⚠️ AOS n’est pas encore défini.");
  }
</script>
<script src="js/index.js"></script>
</body>
</html>

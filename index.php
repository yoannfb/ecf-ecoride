<?php include("includes/header.php"); ?>
<body>
  <?php include("includes/navbar.php"); 
  require_once 'includes/db.php';?>

  


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
    <form id="searchForm" class="row g-3">
      <div class="col-md-4">
        <input type="text" name="depart" placeholder="Ville de départ" class="form-control">
      </div>
      <div class="col-md-4">
        <input type="text" name="arrivee" placeholder="Ville d’arrivée" class="form-control">
      </div>
      <div class="col-md-4">
        <input type="date" name="date" class="form-control">
      </div>
      <div class="text-end">
        <button type="submit" class="btn btn-success">Rechercher</button>
      </div>
    </form>

  </section>

  <?php include("includes/footer.php"); ?>
  
  <script src="js/index.js"></script>
</body>
</html>

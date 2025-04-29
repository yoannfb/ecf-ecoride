
// annimation sur la "recherche d'itinéraire" 
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('searchForm');
  
    if (form) {
      form.addEventListener('submit', (e) => {
        e.preventDefault(); // Toujours empêcher la soumission par défaut
  
        // Récupération des champs via querySelector
        const depart = form.querySelector('input[name="depart"]').value.trim();
        const arrivee = form.querySelector('input[name="arrivee"]').value.trim();
        const date = form.querySelector('input[name="date"]').value;
  
        if (!depart || !arrivee || !date) {
          alert("🚨 Merci de remplir tous les champs avant de rechercher un itinéraire.");
          return;
        }
  
        // Si tout est bon, on redirige vers covoiturages.php avec les paramètres
        const params = new URLSearchParams({ depart, arrivee, date });
        window.location.href = `covoiturages.php?${params.toString()}`;
      });
    }
  });
  

// animation voiture qui défilent 
document.addEventListener('DOMContentLoaded', () => {
    const voiture = document.getElementById('voiture');

    // Vérifie que l'image est bien trouvée
    if (voiture) {
      // Applique le déplacement après un petit délai
        setTimeout(() => {
        voiture.style.left = '100%';
        }, 1000);
    } else {
    console.warn("⚠️ L'image avec l'ID 'voiture' n'a pas été trouvée.");
    }
});

// section présentation entreprise, défilement du texte
const title = document.querySelector(".text");
const txt ="EcoRide a pour mission de réduire l’impact environnemental des trajets grâce au covoiturage."



function typewriter(word, index) {
    if(index < word.length) {
        setTimeout(() => {
            title.innerHTML += `<span>${word[index]}</span>`
            typewriter(txt, index + 1)
        }, 50);
    }
} 

setTimeout(() =>{
    typewriter(txt, 0)
}, 500);


  AOS.init();

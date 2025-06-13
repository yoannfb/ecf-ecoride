console.log("✅ index.js chargé !");
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("form-recherche");
  const resultatsDiv = document.getElementById("recherche-resultats");

  if (!form || !resultatsDiv) {
    console.error("Formulaire ou zone de résultats introuvable.");
    return;
  }

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const depart = document.getElementById("depart").value;
    const arrivee = document.getElementById("arrivee").value;
    const date = document.getElementById("date").value;

    const url = `recherche.php?ajax=1&depart=${encodeURIComponent(depart)}&arrivee=${encodeURIComponent(arrivee)}&date=${encodeURIComponent(date)}`;

    try {
      const response = await fetch(url);

      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }

      const data = await response.json();
      console.log("✅ Données reçues :", data);

      resultatsDiv.innerHTML = "";

      if (data.length > 0) {
        data.forEach((trajet) => {
          resultatsDiv.innerHTML += `
            <div class="trajet">
              <h4>${trajet.adresse_depart} → ${trajet.adresse_arrivee}</h4>
              <p>Départ : ${trajet.date_depart}</p>
              <p>Prix : ${trajet.prix} €</p>
              <a href="covoiturages.php?id=${trajet.id}" class="btn">Voir le covoiturage</a>
            </div>
          `;
        });
      } else {
        resultatsDiv.innerHTML = "<p>Aucun covoiturage trouvé.</p>";
      }

    } catch (error) {
      console.error("❌ Erreur AJAX :", error);
      resultatsDiv.innerHTML = "<p>Une erreur est survenue.</p>";
    }
  });
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


if (typeof AOS !== 'undefined') {
AOS.init();
console.log("✅ AOS initialisé !");
} else {
console.warn("⚠️ AOS n'est pas encore chargé.");
}
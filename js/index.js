console.log("✅ index.js chargé !");

document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("searchForm");
  const resultatsDiv = document.getElementById("resultats");

  if (!form || !resultatsDiv) {
    console.error("❌ Formulaire ou zone de résultats introuvable.");
    return;
  }

  form.addEventListener("submit", (e) => {
    e.preventDefault();
    console.log("🚀 Formulaire soumis !");

    const depart = form.elements["depart"].value.trim();
    const arrivee = form.elements["arrivee"].value.trim();
    const date = form.elements["date"].value.trim();

    const params = new URLSearchParams({ depart, arrivee, date });

    fetch("recherche.php?" + params.toString())
      .then((res) => res.text())
      .then((html) => {
        console.log("📦 Réponse reçue !");
        if (html.trim() === "") {
          resultatsDiv.innerHTML = `<div class="alert alert-warning">Aucun covoiturage trouvé.</div>`;
        } else {
          resultatsDiv.innerHTML = html;
        }
      })
      .catch((err) => {
        console.error("❌ Erreur FETCH :", err);
        resultatsDiv.innerHTML = `<div class="alert alert-danger">Erreur lors de la recherche.</div>`;
      });
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
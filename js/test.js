console.log("✅ test.js chargé !");

document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("searchForm");
  console.log("🧪 Formulaire :", form);

  if (!form) {
    console.error("❌ Formulaire introuvable !");
    return;
  }

  form.addEventListener("submit", (e) => {
    e.preventDefault();
    console.log("🚀 Formulaire soumis !");
  });
});

// Validation client-side pour formulaire de connexion
document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector(".login-form");

  if (form) {
    form.addEventListener("submit", function (e) {
      const email = document.getElementById("email").value.trim();
      const password = document.getElementById("password").value;

      // Validation champs obligatoires
      if (!email || !password) {
        e.preventDefault();
        alert("Veuillez remplir tous les champs obligatoires.");
        return false;
      }

      // Validation email
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        e.preventDefault();
        alert("Veuillez entrer une adresse email valide.");
        return false;
      }

      return true;
    });
  }
});

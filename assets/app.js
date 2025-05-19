document.addEventListener("DOMContentLoaded", () => {
  /**
   * Fonction pour ajouter un nouvel élément (étape ou ingrédient)
   * @param {string} containerId - ID du conteneur
   * @param {string} addButtonId - ID du bouton d'ajout
   */
  function handleDynamicFields(containerId, addButtonId) {
    const container = document.getElementById(containerId);
    const addButton = document.getElementById(addButtonId);

    addButton.addEventListener("click", () => {
      const prototype = container.dataset.prototype;
      const index = container.children.length;
      const newForm = prototype.replace(/__name__/g, index);

      const div = document.createElement("div");
      div.classList.add("dynamic-item"); // Classe générique pour styling
      div.innerHTML =
        newForm +
        `
                <button type="button" class="btn btn-danger btn-sm remove-item float-right">
                    <i class="fa-solid fa-trash"></i>
                </button>
            `;

      container.appendChild(div);

      // Ajouter un event listener pour le bouton de suppression
      div.querySelector(".remove-item").addEventListener("click", () => {
        div.remove();
      });
    });

    // Event listeners pour les boutons "Supprimer" existants
    container.querySelectorAll(".remove-item").forEach((button) => {
      button.addEventListener("click", (event) => {
        event.target.closest(".dynamic-item").remove();
      });
    });
  }
  // Appel pour les ingrédients
  handleDynamicFields("ingredient-container", "add-ingredient");

  // Appel pour les étapes
  handleDynamicFields("etape-container", "add-etape");

  const recetteId = 1; // ID de la recette, à dynamiser
  // const recetteElement = document.getElementById("recette-data");
  // const recetteId = recetteElement?.dataset.recetteId;

  // Charger le nombre de commentaires
  fetch(`/recette/${recetteId}/comments-count`)
    .then((response) => response.json())
    .then((data) => {
      document.querySelector(".fa-comments + p").textContent =
        data.comments_count;
    });

  const showMoreButton = document.getElementById("show-more-comments");
  const showLessButton = document.getElementById("show-less-comments");
  const commentRows = document.querySelectorAll(".comment-row");

  if (showMoreButton && showLessButton && commentRows.length > 0) {
    showMoreButton.addEventListener("click", function () {
      console.log("Voir plus cliqué");
      commentRows.forEach((row) => row.classList.remove("d-none")); // Affiche tous les commentaires
      showMoreButton.classList.add("d-none"); // Cache le bouton "Voir plus"
      showLessButton.classList.remove("d-none"); // Affiche le bouton "Voir moins"
    });

    showLessButton.addEventListener("click", function () {
      console.log("Voir moins cliqué");
      commentRows.forEach((row, index) => {
        if (index >= 3) {
          row.classList.add("d-none"); // Masque les commentaires après le 3e
        }
      });
      showLessButton.classList.add("d-none"); // Cache le bouton "Voir moins"
      showMoreButton.classList.remove("d-none"); // Affiche le bouton "Voir plus"
    });
  }
});

import "./styles/app.css";
// import * as bootstrap from "bootstrap";
// window.bootstrap = bootstrap;

console.log("This log comes from assets/app.js - welcome to AssetMapper! 🎉");

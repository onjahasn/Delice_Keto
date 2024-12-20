// import 'bootstrap/dist/css/bootstrap.min.css';
// import './bootstrap.js';
// document.getElementById('add-etape').addEventListener('click', function () {
//     const container = document.getElementById('etape-container');
//     const prototype = container.dataset.prototype;
//     const index = container.children.length;

//     // Remplace __name__ par l'index actuel
//     const newForm = prototype.replace(/__name__/g, index);

//     // Ajouter le nouveau formulaire au conteneur
//     const div = document.createElement('div');
//     div.classList.add('etape-item');
//     div.innerHTML = newForm + '<button type="button" class="btn btn-danger btn-sm remove-etape">Supprimer</button>';
//     container.appendChild(div);

//     // Ajout d'un événement pour le bouton "Supprimer"
//     div.querySelector('.remove-etape').addEventListener('click', function () {
//         div.remove();
//     });
// });

// // Gestion de la suppression d'étapes existantes
// document.querySelectorAll('.remove-etape').forEach(button => {
//     button.addEventListener('click', function () {
//         button.closest('.etape-item').remove();
//     });
// });

	document.addEventListener('DOMContentLoaded', () => { /**
     * Fonction pour ajouter un nouvel élément (étape ou ingrédient)
     * @param {string} containerId - ID du conteneur
     * @param {string} addButtonId - ID du bouton d'ajout
     */
function handleDynamicFields(containerId, addButtonId) {
const container = document.getElementById(containerId);
const addButton = document.getElementById(addButtonId);

addButton.addEventListener('click', () => {
const prototype = container.dataset.prototype;
const index = container.children.length;
const newForm = prototype.replace(/__name__/g, index);

const div = document.createElement('div');
div.classList.add('dynamic-item'); // Classe générique pour styling
div.innerHTML = newForm + `
<button type="button" class="btn btn-danger btn-sm remove-item float-right">
	<i class="fa-solid fa-trash"></i>
</button>
            `;

container.appendChild(div);

// Ajouter un event listener pour le bouton de suppression
div.querySelector('.remove-item').addEventListener('click', () => {
div.remove();
});
});

// Event listeners pour les boutons "Supprimer" existants
container.querySelectorAll('.remove-item').forEach((button) => {
button.addEventListener('click', (event) => {
event.target.closest('.dynamic-item').remove();
});
});
}

// Appel pour les ingrédients
handleDynamicFields('ingredient-container', 'add-ingredient');

// Appel pour les étapes
handleDynamicFields('etape-container', 'add-etape');
});


import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

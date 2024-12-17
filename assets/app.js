import 'bootstrap/dist/css/bootstrap.min.css';
import './bootstrap.js';
document.getElementById('add-etape').addEventListener('click', function () {
    const container = document.getElementById('etape-container');
    const prototype = container.dataset.prototype;
    const index = container.children.length;

    // Remplace __name__ par l'index actuel
    const newForm = prototype.replace(/__name__/g, index);

    // Ajouter le nouveau formulaire au conteneur
    const div = document.createElement('div');
    div.classList.add('etape-item');
    div.innerHTML = newForm + '<button type="button" class="btn btn-danger btn-sm remove-etape">Supprimer</button>';
    container.appendChild(div);

    // Ajout d'un événement pour le bouton "Supprimer"
    div.querySelector('.remove-etape').addEventListener('click', function () {
        div.remove();
    });
});

// Gestion de la suppression d'étapes existantes
document.querySelectorAll('.remove-etape').forEach(button => {
    button.addEventListener('click', function () {
        button.closest('.etape-item').remove();
    });
});


import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

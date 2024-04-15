//////////////////////////////////////////////MODAL VOIR DETAIL///////////////////////////////////////////////////
// Fonction pour afficher la modale avec les détails de l'événement spécifique
function showModal(eventId) {
    // Récupérer les détails de l'événement à partir de l'ID de l'événement
    fetchEventData(eventId).then(function(evenement) {
        //console.log()

        // Mettre à jour le contenu de la modale avec les détails de l'événement
        document.getElementById("modal-image").src = '/images/evenement/'+ evenement.image;
        document.getElementById("modal-title").textContent = evenement.titre;
        document.getElementById("modal-date").textContent = evenement.dateDebut + " / " + evenement.dateFin;
        document.getElementById("modal-lieu").textContent = evenement.lieu;
        document.getElementById("modal-description").textContent = evenement.description;
        // Afficher la modale
        var modal = document.getElementById("eventModal");
        modal.style.display = "block";
    });
}

// Fonction pour récupérer les détails de l'événement depuis le serveur
function fetchEventData(eventId) {
    return fetch('/evenements/' + eventId)
        .then(response => response.json())
        .catch(error => console.error('Erreur lors de la récupération des données de l\'événement:', error));
}

// Attacher un gestionnaire d'événements à tous les liens avec la classe "voir-detail"
document.querySelectorAll('.voir-detail').forEach(function(link) {
    link.addEventListener('click', function(event) {
        event.preventDefault(); // Empêcher le comportement par défaut du lien
        var eventId = this.getAttribute('data-evenement'); // Récupérer l'ID de l'événement à partir de l'attribut de données
        showModal(eventId); // Afficher la modale avec les détails de l'événement spécifique
    });
});

// Fonction pour fermer la modale lorsque l'utilisateur clique sur le bouton de fermeture
document.querySelector('.close').addEventListener('click', function() {
    var modal = document.getElementById("eventModal");
    modal.style.display = "none";
});

// Fermer la modale lorsque l'utilisateur clique en dehors de celle-ci
window.addEventListener('click', function(event) {
    var modal = document.getElementById("eventModal");
    if (event.target === modal) {
        modal.style.display = "none";
    }
});

////////////////////////////////////////////////BOUTON PARTICIPER////////////////////////////////////////////////////
document.addEventListener('DOMContentLoaded', function() {
    const participerButtons = document.querySelectorAll('.participer-btn');

    participerButtons.forEach(button => {
        // Récupérer l'ID de l'événement associé à ce bouton
        const eventId = button.getAttribute('data-evenement');
        
        // Vérifier s'il existe une valeur enregistrée pour cet événement dans le stockage local
        const participationStatus = localStorage.getItem(`participation_${eventId}`);
        if (participationStatus === 'participated') {
            // Si l'utilisateur a déjà participé à cet événement, mettre à jour le bouton
            button.innerHTML = '<i class="fas fa-star"></i> Participé(e)';
        }

        // Récupérer le compteur de participants pour cet événement
        const participantsCountElement = button.parentElement.querySelector('.participants-count');
        let participantsCount = parseInt(participantsCountElement.textContent.trim());

        button.addEventListener('click', () => {
            console.log("Button clicked");

            const icon = button.querySelector('i');
            console.log("Icon:", icon);

            // Accéder au texte du bouton via la propriété textContent
            const buttonText = button.textContent.trim(); // Obtenir le texte sans les espaces blancs avant et après
            console.log("ButtonText:", buttonText);

            // Mettre à jour le statut de participation dans le stockage local
            if (buttonText === 'Participer') {
                fetch(`/evenement/participate/${eventId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ action: 'add' }),
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur lors de la gestion de la participation');
                    }
                    // Mettre à jour le compteur de participants et le bouton après une réponse réussie
                    localStorage.setItem(`participation_${eventId}`, 'participated');
                    participantsCount++;
                    participantsCountElement.textContent = participantsCount + " participants";
                    button.innerHTML = '<i class="fas fa-star"></i> Participé(e)';
                })
                .catch(error => {
                    console.error('Erreur lors de la gestion de la participation:', error);
                });
            } else {
                fetch(`/evenement/participate/${eventId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ action: 'remove' }),
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur lors de la gestion de la participation');
                    }
                    // Mettre à jour le compteur de participants et le bouton après une réponse réussie
                    localStorage.removeItem(`participation_${eventId}`);
                    participantsCount--;
                    participantsCountElement.textContent = participantsCount + " participant(s)";
                    button.innerHTML = '<i class="far fa-star"></i> Participer';
                })
                .catch(error => {
                    console.error('Erreur lors de la gestion de la participation:', error);
                });
            }
        });
    });
});


////////////////////////////////////////////////AJOUT EVENT//////////////////////////////////////////////////////////////////////////
// JavaScript pour afficher la boîte modale de création d'événement
document.addEventListener('DOMContentLoaded', function () {
    const openModalBtn = document.querySelector('.openModalBtn');
    const createEventModal = document.getElementById('createEventModal');
    const closeModalBtn = createEventModal.querySelector('.close');
    const imagePreview = document.getElementById('imagePreview'); // Get the image preview element

    openModalBtn.addEventListener('click', function (event) {
        event.preventDefault(); // Empêcher le comportement par défaut de navigation
        createEventModal.style.display = 'block';
        document.body.classList.add('blur'); // Ajouter la classe pour flouter la page
    });

    closeModalBtn.addEventListener('click', function () {
        createEventModal.style.display = 'none';
        document.body.classList.remove('blur'); // Retirer la classe de flou de la page
    });

    window.addEventListener('click', function (event) {
        if (event.target === createEventModal) {
            createEventModal.style.display = 'none';
            document.body.classList.remove('blur'); // Retirer la classe de flou de la page si l'utilisateur clique en dehors de la modale
        }
    });
    const formErrors = document.querySelectorAll('.form-control.is-invalid');
    if (formErrors.length > 0) {
        createEventModal.style.display = 'flex';
        document.body.classList.add('blur');
    }

    const addEventButton = document.getElementById('add'); // Get the submit button
    addEventButton.addEventListener('click', function () {
        const formErrors = document.querySelectorAll('.form-control.is-invalid');
        if (formErrors.length === 0) { // Only hide the image preview if there are no errors
            imagePreview.style.display = 'none';
        }
    });

    // Function to preview image
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
});

 ///////////////////////////////////////////////////////////////////////////////////////////
 function formatDateForDateTimeInput(dateString) {
    var parts = dateString.split(' ');
    var dateParts = parts[0].split('-');
    var timeParts = parts[1].split(':');
    return dateParts[2] + '-' + dateParts[1] + '-' + dateParts[0] + 'T' + timeParts[0] + ':' + timeParts[1];
}

// Appel de la fonction pour formater la date de début de l'événement
var dDebutEvenementInput = document.getElementById('form_dDebutEvenement');
dDebutEvenementInput.value = formatDateForDateTimeInput(dDebutEvenementInput.value);

var dFinEvenementInput = document.getElementById('form_dFinEvenement');
dFinEvenementInput.value = formatDateForDateTimeInput(dFinEvenementInput.value);




// Assurez-vous que l'ID de l'élément d'aperçu d'image est correct
document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('form').addEventListener('change', function(event) {
        if (event.target && event.target.matches('input[type="file"]')) {
            const file = event.target.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                document.getElementById("imagePreview").src = '/images/evenement/' + evenement.image;
            };
            

            reader.readAsDataURL(file);
        }
    });
});




// Fonction pour valider la date de fin côté client
function validateEndDate() {
    const startDate = new Date(document.getElementById('form_dDebutEvenement').value);
    const endDate = new Date(document.getElementById('form_dFinEvenement').value);

    if (endDate < startDate) {
        alert("La date de fin ne peut pas être antérieure à la date de début.");
        return false;
    }
    return true;
}

// Écouter l'événement de soumission du formulaire
document.getElementById('createEventForm').addEventListener('submit', function(event) {
    // Valider la date de fin avant de soumettre le formulaire
    if (!validateEndDate()) {
        event.preventDefault(); // Empêcher la soumission du formulaire si la validation échoue
    }
});


/////////////////////////////////////////////DROPDOWN EDIT & DELETE/////////////////////////////////////////
// Supprimer les écouteurs d'événements existants
function toggleDropdown(toggle) {
    const dropdown = toggle.parentElement;
    dropdown.classList.toggle('active');
}

document.querySelectorAll('.dropdown-toggle').forEach(item => {
    item.removeEventListener('click', toggleDropdown);
});

// Ajouter des écouteurs d'événements individuels pour chaque élément de menu déroulant
document.querySelectorAll('.dropdown-toggle').forEach(item => {
    item.addEventListener('click', function(event) {
        const dropdown = this.nextElementSibling;
        dropdown.classList.toggle('show');
        event.stopPropagation();
    });
});

// Fermer le menu déroulant lors du clic en dehors de celui-ci
window.addEventListener('click', function(event) {
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        if (!menu.contains(event.target)) {
            menu.classList.remove('show');
        }
    });
});
////////////////////////////////////////////////SUPPRIMER EVENT//////////////////////////////////////
document.querySelectorAll('.delete-event').forEach(link => {
    link.addEventListener('click', function(event) {
        event.preventDefault(); // Empêche le comportement par défaut du lien
        const confirmMessage = "Êtes-vous sûr de vouloir supprimer cet événement ?";
        if (confirm(confirmMessage)) {
            const eventId = this.getAttribute('href'); // Récupère l'URL de suppression de l'événement
            fetch(eventId, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest', // Ajoutez cette en-tête si nécessaire
                    // Ajoutez ici votre jeton CSRF si nécessaire
                },
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur lors de la suppression de l\'événement');
                }
                return response.json();
            })
            .then(data => {
                alert(data.message); // Affiche un message de confirmation
                // Rechargez la page ou mettez à jour la liste des événements si nécessaire
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur s\'est produite lors de la suppression de l\'événement');
            });
        }
    });
});

///////////////////////////////////////////////////////// Recherche /////////////////////////////////////////////////
 $(document).ready(function() {
    $('#search-bar').on('submit', function(event) {
        event.preventDefault(); // Empêche le formulaire de se soumettre normalement

        var searchTerm = $('#search-bar input[type="search"]').val(); // Récupère la valeur de la recherche

        $.ajax({
            url: '/evenement/search?q=' + searchTerm,
            method: 'GET', // Utiliser la méthode GET pour passer le terme de recherche en tant que paramètre
            success: function(response) {
                $('.produit-container').html(response); // Met à jour les résultats de la recherche dans la div .produit-container
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    });
});


















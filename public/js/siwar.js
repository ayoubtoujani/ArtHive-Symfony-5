document.addEventListener('DOMContentLoaded', function() {
    const participerButtons = document.querySelectorAll('.participer-btn');

    participerButtons.forEach(button => {
        button.addEventListener('click', () => {
            console.log("Button clicked");

            const icon = button.querySelector('i');
            console.log("Icon:", icon);

            // Accéder au texte du bouton via la propriété textContent
            const buttonText = button.textContent.trim(); // Obtenir le texte sans les espaces blancs avant et après
            console.log("ButtonText:", buttonText);

            icon.classList.toggle('far');
            icon.classList.toggle('fas');

            // Vérifier le texte du bouton et modifier en conséquence
            if (buttonText === 'Participer') {
                button.textContent = 'Participé';
            } else {
                button.textContent = 'Participer';
            }
        });
    });
});
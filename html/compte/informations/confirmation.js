document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("donnee");

    form.addEventListener("submit", function(event) {
        // Empêche l'envoi immédiat
        event.preventDefault();

        // Fenêtre de confirmation
        const confirmation = confirm("Êtes-vous sûr de vouloir modifier vos données ?");

        if (confirmation) {
            // L'utilisateur confirme → on envoie le formulaire
            form.submit();
        }
    });
});


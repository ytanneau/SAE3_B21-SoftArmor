document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("supprimer");

    form.addEventListener("submit", function(event) {
        // Empêche l'envoi immédiat
        event.preventDefault();

        // Fenêtre de confirmation
        const confirmation = confirm("Êtes-vous sûr de vouloir supprimer ce produit ?");

        if (confirmation) {
            // L'utilisateur confirme, on envoie le formulaire
            form.submit();
        }
    });
});


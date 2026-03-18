<form id="form_avis" enctype="multipart/form-data" method="post">
    <input type="hidden" name="produit" value="<?=$produit['id_produit'] ?? ''?>" id="id_produit_avis">

    <div id="etoiles">
        <img src="<?=HOME_SITE?>image/etoile.svg" alt="e">
        <img src="<?=HOME_SITE?>image/etoile.svg" alt="e">
        <img src="<?=HOME_SITE?>image/etoile.svg" alt="e">
        <img src="<?=HOME_SITE?>image/etoile.svg" alt="e">
        <img src="<?=HOME_SITE?>image/etoile.svg" alt="e">
    </div>

    <input type="hidden" name="note" id="nb_etoiles">

    <div id="champs">
        <div>
            <input type="text" placeholder="Titre de l'avis" id="titre_avis" name="titre" class="champ">

            <div style="display: flex;">
                <p id="error_titre" class="error" style="visibility: hidden">Veuillez remplir ce champ</p>
                <span id="nb_car_titre">0/<?= TAILLE_TITRE ?></span>
            </div>

            <textarea placeholder="Description de l'avis" id="description_avis" name="description" class="text champ"></textarea>
            
            <div style="display: flex;">
                <p id="error_description" class="error" style="visibility: hidden">Ce champ dépasse la limite autorisée</p>
                <span id="nb_car_description">0/<?= TAILLE_DESCRIPTION ?></span>
            </div>

            <input type="submit" value="Publier l'avis">
        </div>

        <div id="image_avis">
            <label class="image_uploader" for="input_image_avis" tabIndex="0">
                <div id="image_preview_avis" class="image_preview">
                    <span>+</span>
                    <span>Ajouter une image</span>
                </div>
            </label>
            
            <button id="clear_image_avis" class="clear_image">
                <img src="<?= HOME_SITE . "image/supprimer_blanc.svg" ?>">
            </button>
            
            <input type="file" id="input_image_avis" class="input_image" name="image" accept="image/png"></input>
        </div>
    </div>
</form>

<script defer>
    const formAvis = document.getElementById("form_avis");
    const divAvis = document.getElementById("creation_avis");

    const inputTitre = document.getElementById("titre_avis");
    const inputCommentaire = document.getElementById("description_avis");

    const inputImageAvis = document.getElementById("input_image_avis");
    const imagePreviewAvis = document.getElementById("image_preview_avis");
    const buttonClearImageAvis = document.getElementById("clear_image_avis");

    const pErrorTitre = document.getElementById("error_titre");
    const pErrorDescription = document.getElementById("error_description");

    const nbCarTitre = document.getElementById("nb_car_titre");
    const nbCarDescription = document.getElementById("nb_car_description");

    const TAILLE_TITRE = 100;
    const TAILLE_DESCRIPTION = 1000;

    // Création ou modification d'un avis
    formAvis.addEventListener("submit", async (e) => {
        e.preventDefault();

        // Récupérer les données du formulaire
        const data = new FormData(formAvis);

        let avisSansTitre = (data.get("description").trim() != "") && (data.get("titre") == "");
        let titreLong = (data.get("titre").trim().length > TAILLE_TITRE);
        let descriptionLongue = (data.get("description").trim().length > TAILLE_DESCRIPTION);

        // Affichage des erreurs
        
        pErrorTitre.style.visibility = (avisSansTitre | titreLong) ? "visible" : "hidden";
        pErrorDescription.style.visibility = descriptionLongue ? "visible" : "hidden";
        
        if (avisSansTitre) {
            pErrorTitre.textContent = "Veuillez remplir ce champ";
            return;

        } else if (titreLong) {
            pErrorTitre.textContent = "Ce champ dépasse la limite de caractères";
            return;
        } 
        
        else if (descriptionLongue) {
            pErrorDescription.textContent = "Ce champ dépasse la limite de caractères";
            return;
        }

        // Envoyer les données du formulaire en JSON à une autre page

        const res = await fetch(HOME_SITE + "avis/creation.php", {
            method: "POST",
            body: data
        });

        const json = await res.json();

        // Afficher la snackbar
        showSnackbar(json.message, json.success ? "success" : "error");

        if (json.success) {
            // Cacher le formulaire de création d'un avis
            divAvis.style.display = "none";

            setTimeout(() => {
                window.location.reload();
            }, 5000);
        }
    });

    // Empêcher les titres d'avis trop longs
    inputTitre.addEventListener("input", (e) => {
        pErrorTitre.style.visibility = "hidden";
        
        nbCarTitre.textContent = `${inputTitre.value.length}/${TAILLE_TITRE}`;
        
        if (inputTitre.value.length > TAILLE_TITRE) {
            nbCarTitre.style.color = "red";
        } else {
            nbCarTitre.style.color = "black";
        }
    });

    // Empêcher les descriptions d'avis trop longues
    inputCommentaire.addEventListener("input", (e) => {
        nbCarDescription.textContent = `${inputCommentaire.value.length}/${TAILLE_DESCRIPTION}`;
        
        if (inputCommentaire.value.length > TAILLE_DESCRIPTION) {
            nbCarDescription.style.color = "red";
        } else {
            nbCarDescription.style.color = "black";
        }
    });

    // Uploader une image
    inputImageAvis.addEventListener("change", (e) => {
        const file = e.target.files[0];

        if (file) {
            const reader = new FileReader();

            reader.addEventListener("load", (e) => {
                imagePreviewAvis.style.backgroundImage = `url(${e.target.result})`;
                imagePreviewAvis.innerHTML = "";

                // Afficher le bouton de suppression
                buttonClearImageAvis.style.display = "block";
            });

            reader.readAsDataURL(file);
        }
    });

    // Supprimer l'image uploadée
    buttonClearImageAvis.addEventListener("click", (e) => {
        e.preventDefault();

        inputImageAvis.value = "";
        imagePreviewAvis.style.backgroundImage = "";
        imagePreviewAvis.innerHTML = "";

        // Cacher le bouton de suppression d'image
        buttonClearImageAvis.style.display = "none";

        const spanPlus = document.createElement("span");
        const spanTexte = document.createElement("span");
        
        spanPlus.textContent = "+";
        spanTexte.textContent = "Ajouter une image";

        imagePreviewAvis.appendChild(spanPlus);
        imagePreviewAvis.appendChild(spanTexte);
    });


    // Fonctions pour la sélection de la note

    let dir_images = '<?=HOME_SITE?>image/';

    function activerEtoileR(img) { // active toutes les étoiles à gauche (récursivement) de l'étoile selectionnée
        img.src = dir_images + 'etoile_pleine.svg';
        
        if (img.previousElementSibling && img.previousElementSibling.nodeType == 1) {
            return 1 + activerEtoileR(img.previousElementSibling);
        }

        return 1;
    }
    
    function desactiverEtoileR(img) { // désactive toutes les étoiles à droite (récursivement) de l'étoile selectionnée
        img.src = dir_images + 'etoile.svg';

        if (img.nextElementSibling && img.nextElementSibling.nodeType == 1) {
            desactiverEtoileR(img.nextElementSibling);
        }
    }

    let children = Array.from(document.getElementById("etoiles").children);

    children.forEach(element => {
        element.addEventListener('click', (e) => {
            let src = e.target.getAttribute('src');

            let image = dir_images + 'etoile_pleine.svg';
            desactiverEtoileR(e.target);
            let nb_etoiles = activerEtoileR(e.target);

            e.target.setAttribute('src', image);

            document.getElementById('nb_etoiles').value = nb_etoiles;
            console.log("etoiles");
        });
    });

    children[2].click(); // met 3 étoiles par défaut
</script>
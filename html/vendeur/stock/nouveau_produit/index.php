<?php                 
    define('HOME_GIT', '../../../../');
    define('HOME_SITE', '../../../');
 
    if (!isset($_SESSION)) {
        session_start();
    }

    // Si je suis connecté mais pas en tant que vendeur, retour à l'accueil client
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && !isset($_SESSION['raison_sociale'])) {
        header('location: ' . HOME_SITE);
        exit;

    // Sinon si je ne suis pas connecté, retour à la page connexion vendeur
    } else if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] === false) {
        header('location: ../');
        exit;
    }

    // appel des fichiers de configuration et fonctions
    require_once HOME_GIT . ".config.php";
    include HOME_GIT . "fonction_categorie.php";
    include HOME_GIT . "fonction_produit.php";

    $id_compte = $_SESSION['id_compte'];

    if($_SERVER["REQUEST_METHOD"] == "POST"){

        // recupération des données du formulaire
        $nomPrv = $_POST["nomPrv"];
        $nomPblc = $_POST["nomPblc"];
        
        $qtStock = $_POST["qtStock"];
        $tva = $_POST["tva"];
        $prixProd = $_POST["prixProd"];
        $descSimple = $_POST["descSimple"];
        $descDetaille = $_POST["descDetaille"];
        $codeBarre = $_POST["codeBarre"];
        $poidColis = $_POST["poidColis"];
        $volumeColis = $_POST["volumeColis"];
        $quantite = $_POST["qtAchete"];
        $unite = $_POST["unite"];

        $qtachete = $quantite . ";" . $unite;

        if(isset($_POST['sous_categorie']) && $_POST['sous_categorie'] != null){ $categorie = $_POST["sous_categorie"]; } 
        else { $categorie = $_POST["categorie"]; }

        // redefinition des variables de type checkbox pour l'insertion MySql
        if($_POST["qtStock"] === ""){ $qtStock = 0; } 
        else { $qtStock = $_POST["qtStock"]; }
        
        if($_POST["seuilAlerte"] === ""){ $seuilAlerte = 0; } 
        else { $seuilAlerte = $_POST["seuilAlerte"]; }

        $checkMajeur = isset($_POST["checkMajeur"]) ? 1 : 0;

        // insertion du produit dans la base de données
        $idProduit = add_produit($id_compte, $nomPrv,$nomPblc,
                                $prixProd, $tva, $codeBarre, $checkMajeur,
                                $qtachete, $qtStock,$seuilAlerte,
                                $descSimple,$descDetaille, $poidColis,
                                    $volumeColis, $categorie);

        /**********************
        *   Image du produit  *
        ***********************/

        // vérification de la presence d'une images 
        if (isset($_FILES['photo'])){
            $nomImageTemp = $_FILES['photo']['name'];
            $cheminTemp = $_FILES['photo']['tmp_name'];
            
            $nomImage = $idProduit . "_1.png";
            
            $cheminFinal = HOME_SITE . "ressources/produit/" . $nomImage;

            $url = "ressources/produit/" . $nomImage;
            $altDefault = "Image du produit : " . $nomPblc;
            
            // deplace le fichier, stocké dans un espace temporaire, dans un dossier definis plus tot
            if(move_uploaded_file($cheminTemp,$cheminFinal)){

                // insertion des images dans la bdd
                $idImage = add_image($url, $nomPblc, $altDefault);
                add_image_produit($idProduit,$idImage);
                
                // redirection vers la page de stock
                header("Location: ../");
                exit();
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>Alizon - Créer un produit</title>
        <?php include HOME_SITE . 'link_head.php'; ?>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="<?=HOME_SITE?>style.css">
    </head>
    <body>
        <?php include "../../header.php" ?>
        <main>
            <a href="../index.php"><img src="../../../../image/retour.svg" alt="bouton retour en arrière"></a>
            <div class="bodyAjoutProduit">
                <div class="entete">
                    <!-- Bouton de retour sur la page de gestion des stocks -->
                    <h1>Ajouter un produit au stock</h1>
                </div>

                <!-- Formulaire de saisie des informations du produit -->
                <form action="" name="formulaire" method="post" enctype="multipart/form-data">
                    <h3>Informations produit</h3>
                    <div>
                        <p>
                            <label for="nomPrv">Référence*</label>
                            <input required type="text" name="nomPrv" id="idNomPrv" placeholder="Oignon rosé / 500g">
                        </p>
                        <p>
                            <label for="nomPblc">Libellé public*</label>
                            <input required type="text" name="nomPblc" id="idNomPblc" placeholder="Oignon rosé de Roscoff - 500g">
                        </p>
                    </div>
                    <div class="divEnLigne">
                        <p>
                            <label for="prixProd">Prix* hors taxe (€)</label>
                            <input required type="text" name="prixProd" id="idPrixProd" placeholder="4.50">
                        </p>
                        <p>
                            <label for="idtva">TVA*</label>
                            <select name="tva" id="idtva" required>
                                <option value="">-- Taux de TVA --</option>
                                <option value="5">5%</option>
                                <option value="10">10%</option>
                                <option value="20">20%</option>
                            </select>
                        </p>
                        <p>
                            <label for="idCodeBarre">Code barre*</label>
                            <input required type="text" name="codeBarre" id="idCodeBarre" maxlength="13" placeholder="1234512345123">
                            <span id="idMessageErrCodeBarre" style="display:none; color:red">Le code barre doit comporter 13 chiffres</span>
                        </p>
                    </div>
                    <div>
                        <label for="idcheckMajeur">Réservé aux majeurs</label>
                        <input type="checkbox" name="checkMajeur" id="idCheckMajeur">
                    </div>
                    <div class="divEnLigne">
                        <p>
                            <label for="categorie">Catégories*</label>
                            <select name="categorie" id="idCategorie" style="width: 175px;" required>
                                <option value="">-- Choisir une catégorie --</option>
                                <?php
                                    $tabCategorie = get_categorie_parent();
                                    foreach($tabCategorie as $nomCat){
                                ?>
                                <option value="<?= htmlspecialchars($nomCat['nom_categorie']) ?>">
                                    <?= htmlspecialchars($nomCat['nom_categorie']) ?>
                                </option>
                                <?php } ?>
                            </select>
                        </p>
                        <p id="pSousCategorieAlimentaire" style="display:none;">
                            <label for="sous_categorie">Sous-catégories alimentaire*</label>
                            <select name="sous_categorie" id="sous_cate">
                                <option value="">-- Choisir une catégorie --</option>
                                <?php 
                                    $tabSousCategorie = get_sous_categorie("Alimentaire");
                                    foreach($tabSousCategorie as $sousCat){                  
                                ?>
                                <option value="<?= htmlspecialchars($sousCat['nom_categorie']) ?>">
                                        <?= htmlspecialchars($sousCat['nom_categorie'])?>
                                </option>
                                <?php } ?>
                            </select>
                        </p>
                    </div>
                    <div class="divEnLigne">
                        <p>
                            <label for="">Quantité acheté</label>
                            <input type="text" name="qtAchete" placeholder="500">
                        </p>
                        <p id="blockUniteVetement" style="display:none;">
                            <label for="uniteVetement">Unités de Masse</label>
                            <select name="unite" id="uniteVetement">
                                <option value="">-- Choisir une unitée --</option>
                                <option value="xs">XS</option>
                                <option value="s">S</option>
                                <option value="m">M</option>
                                <option value="l">L</option>
                                <option value="xl">XL</option>
                            </select>
                        </p>
                        <p id="blockUniteMasse" style="display:none;">
                            <label for="uniteMasse">Unités de masse</label>
                            <select name="unite" id="uniteMasse">
                                <option value="">-- Choisir une unitée --</option>
                                <option value="g">g</option>
                                <option value="kg">kg</option>
                            </select>
                        </p>
                        <p id="blockUniteLiquide" style="display:none;">
                            <label for="uniteLiquide">Unités de liquide</label>
                            <select name="unite" id="uniteLiquide">
                                <option value="">-- Choisir une unitée --</option>
                                <option value="ml">ml</option>
                                <option value="cl">cl</option>
                                <option value="l">l</option>
                            </select>
                        </p>
                    </div>
                    <h3>Image principale</h3>
                    <div class="divEnLigne">
                        <label class="hide_input_file" for="photo">Importer une image du produit*</label>
                        <input style="display:none;" type="file" name="photo" id="photo" accept=".png" required>
                    </div>
                    <h3>Gestion de stock</h3>
                    <div class="divEnLigne">
                        <p>
                            <label for="qtStock">Quantité en stock</label>
                            <input type="number" name="qtStock" id="qtStock" placeholder="100">
                        </p>
                        <p>
                            <label for="seuilAlerte">Seuil d'alerte</label>
                            <input type="number" name="seuilAlerte" id="seuilAlerte" placeholder="10">
                        </p>
                    </div>
                    <h3>Description</h3>
                    <div>
                        <p class="blockDescription">
                            <label for="descSimple">Description simple (200 caractéres max)</label>
                            <textarea name="descSimple" id="idDescSimple" maxlength="200"></textarea>
                            <label for="descDetaille">Description détaillé (2000 caractéres max)</label>
                            <textarea name="descDetaille" id="idDescDetaille" maxlength="2000"></textarea>
                        </p>
                    </div>
                    <h3>Livraison</h3>
                    <div class="divEnLigne">
                        <p>
                            <label for="poidColis">Poids* (Kg)</label>
                            <input type="text" name="poidColis" id="poidColis" required>
                        </p>
                        <p>
                            <label for="volumeColis">Volume du colis* (L)</label>
                            <input type="text" name="volumeColis" id="volumeColis" required>
                        </p>   
                    </div>
                    <input type="submit" value="Créer le produit" id="creerProduit">
                </form>
            </div>
            
            <script>
                /*
                    Script pour assuré l'intégrité des données en temps réel (dynamique)
                */
                const nomPrv = document.getElementById("idNomPrv"); 
                const nomPblc = document.getElementById("idNomPblc");

                const categorie = document.getElementById("idCategorie");
                const pSousCategorie = document.getElementById("pSousCategorieAlimentaire");
                const selectSousCategorieAlimentaire = document.getElementById("sous_cate");

                const uniteLiquide = document.getElementById("blockUniteLiquide");
                const uniteMasse = document.getElementById("blockUniteMasse");
                const uniteVetement = document.getElementById("blockUniteVetement");

                const tva = document.getElementById("idtva");
                const prix = document.getElementById("idPrixProd");

                const descSimple = document.getElementById("idDescSimple");
                const descDetaille = document.getElementById("idDescDetaille");
                
                const codeBarre = document.getElementById("idCodeBarre");
                const messageErrCodeBarre = document.getElementById("idMessageErrCodeBarre");

                const poidColis = document.getElementById("poidColis");
                const volumeColis = document.getElementById("volumeColis");
                const checkMajeur = document.getElementById("checkMajeur");

                const photo = document.getElementById("photo");

                const creerProduit = document.getElementById("creerProduit");

                poidColis.addEventListener('input', () => {
                    poidColis.value = poidColis.value.replace(",",".");
                    poidColis.value = poidColis.value.replace(/[^\d.,]/g,"");
                })
                volumeColis.addEventListener('input', () => {
                    volumeColis.value = volumeColis.value.replace(",",".");
                    volumeColis.value = volumeColis.value.replace(/[^\d.,]/g,"");
                })
                prix.addEventListener('input', () => {
                    prix.value = prix.value.replace(",",".");
                    prix.value = prix.value.replace(/[^\d.,]/g,"");
                })
                
                selectSousCategorieAlimentaire.addEventListener('change', () => {
                    console.log(selectSousCategorieAlimentaire.value)
                    if(selectSousCategorieAlimentaire.value === "Sucré" || selectSousCategorieAlimentaire.value === "Salé"){
                        uniteLiquide.style.display = "none";
                        uniteVetement.style.display = "none";
                        
                        uniteMasse.style.display = "flex";
                    } else if (selectSousCategorieAlimentaire.value === "Boisson"){
                        uniteMasse.style.display = "none";
                        uniteVetement.style.display = "none";

                        uniteLiquide.style.display = "flex";
                    } else {
                        uniteMasse.style.display = "none";
                        uniteVetement.style.display = "none";
                        uniteLiquide.style.display = "none";
                    }
                })

                categorie.addEventListener('change', () => {
                    console.log(categorie.value)
                    if(categorie.value === "Alimentaire"){
                        pSousCategorie.style.display = "flex";
                    } 
                    else if(categorie.value === "Electroménager" || categorie.value === "Electronique" ||
                    categorie.value === "Soin & Hygiène"){
                        uniteMasse.style.display = "flex";

                        uniteVetement.style.display = "none";
                        uniteLiquide.style.display = "none";
                        
                        selectSousCategorieAlimentaire.value = null;
                        pSousCategorie.style.display = "none";
                    } else {
                        uniteMasse.style.display = "none";
                    }
                })

                descSimple.addEventListener('input', () => {
                    if(descSimple.value.length === 200){
                        alert("Maximum de caractère atteint");
                    }
                })
                descDetaille.addEventListener('input', ()=> {
                    if(descDetaille.value.length === 2000){
                        alert("Maximum de caratère atteint");
                    }
                })

                
                function checkCodeBarre(chaineCodeBarre){
                    if(chaineCodeBarre.length < 13) return true;
                    else return false;
                }
                codeBarre.addEventListener('input', () =>{
                    codeBarre.value = codeBarre.value.replace(/\D/g,"");
                    if(codeBarre.value.length < 13){
                        messageErrCodeBarre.style.display = "block";
                    } else {
                        messageErrCodeBarre.style.display = "none";
                    }
                })

                checkMajeur.addEventListener('change', () => {
                    if(checkMajeur.checked){
                        if(!confirm("Confirmer ?")){
                            checkMajeur.checked = 0;
                        }
                    }
                })
                
                creerProduit.addEventListener('click' , () =>  {
                    if(!checkMajeur.checked){
                        checkMajeur.checked = 0;
                    }
                    
                    if ((nomPrv.value === "") || (nomPblc.value === "") || 
                        (categorie.value === "") || (tva.value === "") || 
                        (prix.value === "") || (codeBarre.value === "") || 
                        (poid.value === "") || (volume.value === "") || 
                        (photo.files.length === 0) || (checkCodeBarre(codeBarre.value))){
                        alert("Les champs obligatoires ne sont pas tous remplis")
                        event.preventDefault();
                    } else if(confirm("Confirmer la création du produit ?")) {
                        alert("Produit créer");
                    } else {
                        event.preventDefault();
                    }
                })
            </script>
            
            
        </main>
        <?php include HOME_SITE . "footer.php" ?>
    </body>
</html>
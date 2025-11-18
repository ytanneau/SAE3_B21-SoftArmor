<?php                 
    // appel du fichier de configuration bdd
    

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

        if(isset($_POST['sous_categorie'])){ $categorie = $_POST["sous_categorie"]; } 
        else { $categorie = $_POST["categorie"]; }

        if($_POST["qtStock"] === ""){ $qtStock = 0; } 
        else { $qtStock = $_POST["qtStock"]; }
        
        if($_POST["seuilAlerte"] === ""){ $seuilAlerte = 0; } 
        else { $seuilAlerte = $_POST["seuilAlerte"]; }

        // redéfinition du critéres sur la majorité suivant l'état du bouton
        $checkMajeur = isset($_POST["checkMajeur"]) ? 1 : 0;

        // insertion du produit dans la base de données
        
        $qtachete = $quantite . ";" . $unite;
        $idProduit = add_produit($id_compte, $nomPrv,$nomPblc,
                                $prixProd, $tva, $codeBarre, $checkMajeur,
                                $qtachete, $qtStock,$seuilAlerte,
                                $descSimple,$descDetaille, $poidColis,
                                    $volumeColis);

        // mise en relation entre le produit et sa catégorie dans la bdd 

        set_produit_categorie($idProduit,$categorie);

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

            $titre_img = explode('.',$nomImageTemp)[0];
            $altDefault = "Image du produit : " . $titre_img;
            
            if(move_uploaded_file($cheminTemp,$cheminFinal)){
                // insertion des images dans la bdd

                $idImage = add_image($url, $titre_img, $altDefault);

                add_image_produit($idProduit,$idImage);
                
                header("Location: ../");
                exit();
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>Ajout produit</title>
        <?php include HOME_SITE . 'link_head.php'; ?>
        <meta charset="UTF-8">
    </head>
    <body>
        <?php include "../../header.php"
          ?>
        <main>
            <!-- Bouton de retour sur la page de gestion des stocks -->
            <a href="../index.php"><img src="../../../../image/retour.svg" alt="bouton retour en arrière"></a>
            <h1>Ajouter un produit au stock</h1>

            <!-- Formulaire de saisie des infos du produit -->
            <form action="" name="formulaire" method="post" enctype="multipart/form-data">
                <fieldset>
                    <h3>Informations produit</h3>
                    <div>
                        <p>
                            <label for="nomPrv">Libellé privé*</label>
                            <input required type="text" name="nomPrv" id="idNomPrv">
                        </p>
                        <p>
                            <label for="nomPblc">Libellé public*</label>
                            <input required type="text" name="nomPblc" id="idNomPblc">
                        </p>
                    </div>
                    <div>
                        <p>
                            <label for="prixProd">Prix* hors taxe (€)</label>
                            <input required type="text" name="prixProd" id="idPrixProd">
                        </p>
                        <p>
                            <label for="tva">TVA* (%)</label>
                            <input required type="number" name="tva" id="idTVA">
                        </p>
                        
                        <p>
                            <label for="codeBarre">Code barre*</label>
                            <input required type="text" name="codeBarre" id="idCodeBarre" maxlength="13" style="width:162.4px">
                            <span id="idMessageErrCodeBarre" style="display:none; color:red">Le code barre doit comporter 13 chiffres</span>
                        </p>
                    </div>
                    <div>
                        <label for="checkMajeur">Réservé aux majeurs</label>
                        <input type="checkbox" name="checkMajeur" id="idCheckMajeur">
                    </div>
                    <div>
                        <!--
                        <p>
                            <label for="venteUnitaire">Vente unitaire</label>
                            <input type="checkbox" name="venteUnitaire" id="venteUnitaire">
                        </p>
                        -->
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
                                    $tabSousCategorie = get_sousCategorie("Alimentaire");
                                    foreach($tabSousCategorie as $sousCat){                  
                                ?>
                                <option value="<?= htmlspecialchars($sousCat['nom_categorie']) ?>">
                                        <?= htmlspecialchars($sousCat['nom_categorie'])?>
                                </option>
                                <?php } ?>
                            </select>
                        </p>
                    </div>
                    <div>
                        <p>
                            <label for="">Quantité acheté</label>
                            <input type="text" name="qtAchete">
                        </p>
                        <p id="blockUniteVetement" style="display:none;">
                            <label for="uniteVetement">Unités de Masse</label>
                            <br>
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
                            <br>
                            <select name="unite" id="uniteMasse">
                                <option value="">-- Choisir une unitée --</option>
                                <option value="g">g</option>
                                <option value="kg">kg</option>
                            </select>
                        </p>
                        <p id="blockUniteLiquide" style="display:none;">
                            <label for="uniteLiquide">Unités de liquide</label>
                            <br>
                            <select name="unite" id="uniteLiquide">
                                <option value="">-- Choisir une unitée --</option>
                                <option value="ml">ml</option>
                                <option value="cl">cl</option>
                                <option value="l">l</option>
                            </select>
                        </p>
                    </div>
                    <hr>
                    <h3>Photo principale</h3>
                    <div>
                        <label for="photo">Importer des images du produit*</label>
                        <input type="file" name="photo" id="photo" accept=".png" required>
                    </div>
                    <hr>
                    <h3>Gestion de stock</h3>
                    <div>
                        <p>
                            <label for="qtStock">Quantité en stock</label>
                            <input type="number" name="qtStock" id="qtStock">
                        </p>
                        <p>
                            <label for="seuilAlerte">Seuil d'alerte</label>
                            <input type="number" name="seuilAlerte" id="seuilAlerte">
                        </p>
                    </div>
                    <hr>
                    <h3>Description</h3>
                    <div>
                        <p>
                            <label for="descSimple">Description simple (200 caractéres maximum)</label>
                            <textarea name="descSimple" id="idDescSimple" maxlength="200"></textarea>
                            <label for="descDetaille">Description détaillé (2000 caractéres maximum)</label>
                            <textarea name="descDetaille" id="idDescDetaille" maxlength="2000"></textarea>
                        </p>
                    </div>
                    <hr>
                    <h3>Livraison</h3>
                    <div>
                        <p>
                            <label for="poidColis">Poids* (Kg)</label>
                            <input type="text" name="poidColis" id="poidColis" required>
                        </p>
                        <p>
                            <label for="volumeColis">Volume du colis* (L)</label>
                            <input type="text" name="volumeColis" id="volumeColis" required>
                        </p>   
                    </div>
                    <br>
                    <a href="../index.php"><input type="submit" value="Créer le produit" id="creerProduit"></a>
                </fieldset>
            </form>
            
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

                const tva = document.getElementById("idTVA");
                const prix = document.getElementById("idPrixProd");

                const descSimple = document.getElementById("idDescSimple");
                const descDetaille = document.getElementById("idDescDetaille");
                
                const codeBarre = document.getElementById("idCodeBarre");
                const messageErrCodeBarre = document.getElementById("idMessageErrCodeBarre");

                const poid = document.getElementById("poidColis");
                const volume = document.getElementById("volumeColis");
                const checkMajeur = document.getElementById("checkMajeur");

                const photo = document.getElementById("photo");

                const creerProduit = document.getElementById("creerProduit");
                
                selectSousCategorieAlimentaire.addEventListener('change', () => {
                    if(selectSousCategorieAlimentaire.value === "Sucré" || selectSousCategorieAlimentaire.value === "Salé"){
                        uniteLiquide.style.display = "none";
                        uniteVetement.style.display = "none";
                        
                        uniteMasse.style.display = "block";
                    } else if (selectSousCategorieAlimentaire.value === "Boisson"){
                        uniteMasse.style.display = "none";
                        uniteVetement.style.display = "none";

                        uniteLiquide.style.display = "block";
                    } else {
                        uniteMasse.style.display = "none";
                        uniteVetement.style.display = "none";
                        uniteLiquide.style.display = "none";
                    }
                })

                categorie.addEventListener('change', () => {
                    if(categorie.value === "Alimentaire"){
                        pSousCategorie.style.display = "block";
                    } 
                    else if(categorie.value === "Electroménager" || categorie.value === "Electronique" ||
                    categorie.value === "Soin & Hygiène"){
                        uniteMasse.style.display = "block";

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
                        event.preventDefault();
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
        <footer>

        </footer>
    </body>
</html>
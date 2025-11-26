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
    $idProduit = $_GET['produit'];

    // utilisation des fonctions de recuperation des données
    $tabInfoProduit = detail_produit($idProduit);
    $tabCategorieDuProduit = get_categorie_produit($idProduit);
    $tabImageProduit = get_id_image_produit($idProduit);

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $nomPblc = $_POST['nomPblc'];

        // permet de verifier si les checkbox sont definis et/ou selectionné
        $checkMajeur = isset($_POST['checkMajeur']) ? 1 : 0;
        $checkEnLigne = isset($_POST['checkEnLigne']) ? 1 : 0;
        
        if($_POST['seuilAlerte'] === ""){ $_POST['seuilAlerte'] = 0; }
        if($_POST['qtStock'] === ""){ $_POST['qtSock'] = 0;}

        update_info_produit( $idProduit,$_POST['nomPrv'],$nomPblc,$_POST['prix'],$_POST['tva'],
                        $_POST['codeBarre'],$checkMajeur,$checkEnLigne,$_POST['qtAchete'],
                        $_POST['qtStock'],$_POST['seuilAlerte'],$_POST['descSimple'],
                        $_POST['descDetaille'],$_POST['poidColis'],$_POST['volumeColis']);
        if($_POST['sous_categorie'] == ""){
            update_categorie_produit($idProduit, $_POST['categorie']);
        } else {
            update_categorie_produit($idProduit, $_POST['sous_categorie']);
        }
        
        if (isset($_FILES['photoPrincipale'])){
            // recupere le nom du fichier envoyé
            $nomImageTemp = $_FILES['photoPrincipale']['name'];
            // recupere le nom temporaire du fichier pour le deplacer
            $cheminTemp = $_FILES['photoPrincipale']['tmp_name'];
            
            $nomImage = $idProduit . "_1.png";
            
            $cheminFinal = HOME_SITE . "ressources/produit/" . $nomImage;
            // definition des caractéristiques d'une image
            $url = "ressources/produit/" . $nomImage;
            $altDefault = "Image du produit : " . $nomPblc;

            if(move_uploaded_file($cheminTemp,$cheminFinal)){
                // appel à la fonction pour modifier la bdd
                
                update_image_produit($tabImageProduit['id_image_principale'], $url, $nomPblc, $altDefault);
            }
        }

        if(isset($_FILES['photo2'])){
            // recupere le nom du fichier envoyé
            $nomImageTemp = $_FILES['photo2']['name'];
            // recupere le nom temporaire du fichier pour le deplacer
            $cheminTemp = $_FILES['photo2']['tmp_name'];
            
            $nomImage = $idProduit . "_2.png";
            
            $cheminFinal = HOME_SITE . "ressources/produit/" . $nomImage;
            // definition des caractéristiques d'une image
            $url = "ressources/produit/" . $nomImage;
            $altDefault = "Image du produit : " . $nomPblc;

            if(move_uploaded_file($cheminTemp,$cheminFinal)){
                // ajout d'une image dans la base de données si elle n'est pas deja présente
                if($tabImageProduit['id_image2'] == null){
                    $idImage2 = add_image( $url, $nomPblc, $altDefault);
                    link_image_produit($idProduit,$idImage2,2);        
                }else{
                    // appel à la fonction pour modifier la bdd
                    update_image_produit($tabImageProduit['id_image1'], $url, $nomPblc, $altDefault);
                }
            }
        }

        if(isset($_FILES['photo3'])){
            // recupere le nom du fichier envoyé
            $nomImageTemp = $_FILES['photo3']['name'];
            // recupere le nom temporaire du fichier pour le deplacer
            $cheminTemp = $_FILES['photo3']['tmp_name'];
            
            $nomImage = $idProduit . "_3.png";
            
            $cheminFinal = HOME_SITE . "ressources/produit/" . $nomImage;
            // definition des caractéristiques d'une image
            $url = "ressources/produit/" . $nomImage;
            $altDefault = "Image du produit : " . $nomPblc;

            if(move_uploaded_file($cheminTemp,$cheminFinal)){
                // ajout d'une image dans la base de données si elle n'est pas deja présente
                if($tabImageProduit['id_image2'] == null){
                    $idImage3 = add_image( $url, $nomPblc, $altDefault);
                    link_image_produit($idProduit,$idImage3,3);        
                }else{
                // appel à la fonction pour modifier la bdd
                    update_image_produit($tabImageProduit['id_image2'], $url, $nomPblc, $altDefault);
                }
            }
        }

        // rediraction vers la page produit apres validation du formulaire
        header("Location: ../");
        exit();
    } 
?>

<!DOCTYPE html>
<html>
    <head>
        <?php include HOME_SITE . 'link_head.php'; ?>
        <title>Alizon - Modifier un produit</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <?php include "../../header.php" ?>
        <main class="bodyModifProduit">
            <!-- Bouton de retour sur la page de gestion des stocks -->
            <div class="entete">
                <a href="../index.php"><img src="../../../../image/retour.svg" alt="bouton retour en arrière"></a>
                <h1>Modifier mon produit</h1>
            </div>
            <form action="" name="formulaire_modification_produit" method="post" enctype="multipart/form-data">
                <h3>Informations produit</h3>
                <div>
                    <p>
                        <label for="nomPrv">Libellé privé*</label>
                        <input type="text" name="nomPrv" id="idNomPrv" value="<?= $tabInfoProduit['nom_stock']?>" required>
                    </p>
                    <p>
                        <label for="nomPblc">Libellé public*</label>
                        <input type="text" name="nomPblc" id="idNomPblc" value="<?= $tabInfoProduit['nom_public']?>" required>
                    </p>
                </div>
                <div class="divEnLigne">
                    <p>
                        <label for="prixProd">Prix* hors taxe (€)</label>
                        <input type="text" name="prix" id="idPrix" value="<?= $tabInfoProduit['prix']?>" required>
                    </p>
                    <p>
                        <label for="tva">TVA*</label>
                        <?php $select5 = ''; $select10 = ''; $select20 = '';
                            if($tabInfoProduit['tva'] == 5){$select5 = 'selected';}elseif($tabInfoProduit['tva'] == 10){$select10 = 'selected';}else{$select20 = 'selected';} ?>
                        <select name="tva" id="idtva" required>
                            <option value="">-- Taux de TVA --</option>
                            <option value="5" <?= $select5 ?>>5%</option>
                            <option value="10" <?= $select10 ?>>10%</option>
                            <option value="20" <?= $select20 ?>>20%</option>
                        </select>
                    </p>
                    <p>
                        <label for="codeBarre">Code barre*</label>
                        <input type="text" name="codeBarre" id="idCodeBarre" maxlength="13" value="<?= $tabInfoProduit['code_barre']?>" required>
                        <span id="messageErrCodeBarre" style="display:none; color:red">Le code barre doit comporter 13 chiffres</span>
                    </p>
                </div>
                <div>
                    <label for="checkMajeur">Réservé aux majeurs</label>
                    <input type="checkbox" name="checkMajeur" id="idCheckMajeur" <?php if($tabInfoProduit['plus_18'] === 1){?> checked <?php } ?>>
                    <label for="checkEnLigne">Produit en ligne</label>
                    <input type="checkbox" name="checkEnLigne" id="idCheckEnLigne" <?php if($tabInfoProduit['en_ligne'] === 1){?> checked <?php }?>>
                </div>
                <p>Categorie actuel : <?= $tabCategorieDuProduit['nom_categorie'] ?></p>
                <div class="divEnLigne">
                    <p>
                        <label for="categorie">Catégories*</label>
                        <select name="categorie" id="idCategorie" style="width: 175px;" required>
                            <option value="">-- Choisir une catégorie --</option>

                            <?php
                                $tabCategorie = get_categorie_parent();
                                foreach($tabCategorie as $nomCat){
                                    $cat = htmlspecialchars($nomCat['nom_categorie']);
                                    $selected = ($cat == $tabCategorieDuProduit['nom_categorie']) ? 'selected' : '';
                            ?>
                                <option value="<?= $cat ?>" <?= $selected ?>><?= $cat ?></option>
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
                        <label for="qtAchete">Quantité acheté</label>
                        <input type="number" name="qtAchete" value="<?= $tabInfoProduit['quantite_unite']?>">
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
                <h3>Images du produit</h3>
                <div class="blockImg">
                    <h4>Image principale</h4>
                    <img src="<?= HOME_SITE . 'ressources/produit/' . htmlentities($idProduit . '_1.png')?>" alt="">
                    <div>
                        <label for="photoPrincipale">Modifier l'image principale</label>
                        <input type="file" name="photoPrincipale">
                    </div>
                    <h4>Image secondaire</h4>
                    <?php if($tabImageProduit['id_image1'] != null){?> <img src="<?= HOME_SITE . 'ressources/produit/' . htmlentities($idProduit . '_2.png') ?>" alt=""> <?php }?>
                    <div>
                        <label for="photo2">Ajouter/Modifier la seconde image</label>
                        <input type="file" name="photo2" accept="image/png">
                    </div>
                    <h4>Troisième image</h4>
                    <?php if($tabImageProduit['id_image2'] != null){?> <img src="<?= HOME_SITE . 'ressources/produit/' . htmlentities($idProduit . '_3.png') ?>" alt=""> <?php }?>
                    <div>
                        <label for="photo3">Ajouter/Modifier la troisième image</label>
                        <input type="file" name="photo3" accept="image/png">
                    </div>
                </div>
                <h3>Gestion du stock</h3>
                <div class="divEnLigne">
                    <p>
                        <label for="qtStock">Quantite en stock</label>
                        <input type="number" name="qtStock" id="idQtStock" value="<?= $tabInfoProduit['quantite'] ?>">
                    </p>
                    <p>
                        <label for="seuilAlerte">Seuil d'alerte</label>
                        <input type="number" name="seuilAlerte" id="idSeuilAlerte" value="<?= $tabInfoProduit['seuil_alerte']?>">
                    </p>
                </div>
                <h3>Description</h3>
                <div>
                    <p>
                        <label for="descSimple">Description simple (200 caractères maximum)</label>
                        <textarea name="descSimple" id="idDescSimple" maxlength="200"><?= $tabInfoProduit['description'] ?></textarea>
                    </p>
                    <p>
                        <label for="descDetaille">Description detaille (2000 caractères maximum)</label>
                        <textarea name="descDetaille" id="idDescDetaille" maxlength="2000"><?= $tabInfoProduit['description_detaillee']?></textarea>
                    </p>
                </div>
                <h3>Livraison</h3>
                <div class="divEnLigne">
                    <p>
                        <label for="poidColis">Poid du colis</label>
                        <input type="text" name="poidColis" id="idPoidColis" value="<?= $tabInfoProduit['poids']?>">
                    </p>
                    <p>
                        <label for="volumeColis">Volume du colis</label>
                        <input type="text" name="volumeColis" id="idVolumeColis" value="<?= $tabInfoProduit['volume']?>">
                    </p>
                </div>
                <input type="submit" value="Valider les modifications" id="idModifierProduit">
            </form>
        </main>
        <script>
            const nomPrv = document.getElementById("idNomPrv");
            const nomPblc = document.getElementById("idNomPblc");
            const prix = document.getElementById("idPrix");
            const tva = document.getElementById("idtva");
            const codeBarre = document.getElementById("idCodeBarre");
            const modifierProduit = document.getElementById("idModifierProduit");

            const categorie = document.getElementById("idCategorie");
            const pSousCategorie = document.getElementById("pSousCategorieAlimentaire");
            const selectSousCategorieAlimentaire = document.getElementById("sous_cate");

            const uniteLiquide = document.getElementById("blockUniteLiquide");
            const uniteMasse = document.getElementById("blockUniteMasse");
            const uniteVetement = document.getElementById("blockUniteVetement");
            
            const descSimple = document.getElementById("idDescSimple");
            const descDetaille = document.getElementById("idDescDetaille");

            const poidColis = document.getElementById("idPoidColis");
            const volumeColis = document.getElementById("idVolumeColis");
            
            poidColis.addEventListener('input', () => {
                poidColis.value = poidColis.value.replace(",",".");
            })
            volumeColis.addEventListener('input', () => {
                volumeColis.value = volumeColis.value.replace(",",".");
            })
            prix.addEventListener('input', () => {
                prix.value = prix.value.replace(",",".");
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
            
            codeBarre.addEventListener('input', () =>{
                codeBarre.value = codeBarre.value.replace(/\D/g,"");
                if(codeBarre.value.length < 13){
                    messageErrCodeBarre.style.display = "block";
                    event.preventDefault();
                } else {
                    messageErrCodeBarre.style.display = "none";
                }
            })
            function checkCodeBarre(chaineCodeBarre){
                if(chaineCodeBarre.length < 13) return true;
                else return false;
            }

            

            selectSousCategorieAlimentaire.addEventListener('change', () => {
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

            modifierProduit.addEventListener('click' , () =>  {
                if(nomPrv.value === ""|| nomPblc.value === "" || tva.value === "" || 
                    prix.value === "" || poidColis.value === "" || volumeColis.value === "" || 
                    checkCodeBarre(codeBarre.value)){
                    alert("Les champs obligatoires ne sont pas tous remplis");
                    event.preventDefault();
                } else if(confirm("Confirmer la modification du produit ?")) {
                    alert("Produit modifier");
                } else {
                    event.preventDefault();
                }
            })
        </script>
        <?php include HOME_SITE . "footer.php" ?>
    </body>
</html>
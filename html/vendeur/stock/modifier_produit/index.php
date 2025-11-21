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

    require_once HOME_GIT . ".config.php";
    include HOME_GIT . "fonction_categorie.php";
    include HOME_GIT . "fonction_produit.php";

    $id_compte = $_SESSION['id_compte'];
    $idProduit = $_GET['produit'];

    $tabInfoProduit = get_info_produit($id_compte,$idProduit);
    $tabCategorieDuProduit = get_categorieProduit($idProduit);
    $tabImageProduit = get_id_image_produit($idProduit);
    echo "<pre>";
    print_r($tabImageProduit);
    echo "</pre>";

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        
        // permet de verifier si les checkbox sont definis et/ou selectionné
        $checkMajeur = isset($_POST['checkMajeur']) ? 1 : 0;
        $checkEnLigne = isset($_POST['checkEnLigne']) ? 1 : 0;

        set_info_produit( $id_compte,
                        $idProduit,
                        $_POST['nomPrv'],
                        $_POST['nomPblc'],
                        $_POST['prixProd'],
                        $_POST['tva'],
                        $_POST['codeBarre'],
                        $checkMajeur,
                        $checkEnLigne,
                        $_POST['qtAchete'],
                        $_POST['qtStock'],
                        $_POST['seuilAlerte'],
                        $_POST['descSimple'],
                        $_POST['descDetaille'],
                        $_POST['poidColis'],
                        $_POST['volumeColis']);
        
        if($_POST['categorie'] != $tabCategorieDuProduit['nom_categorie']){
            update_categorie_produit($idProduit, $tabCategorieDuProduit['nom_categorie']);
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
            $titre_img = explode('.',$nomImageTemp)[0];
            $altDefault = "Image du produit : " . $titre_img;

            if(move_uploaded_file($cheminTemp,$cheminFinal)){
                // appel à la fonction pour modifier la bdd
                update_image_produit($idImage, $url, $titre_img, $altDefault);
            }
        }
        if(isset($_POST['photo2'])){
            // recupere le nom du fichier envoyé
            $nomImageTemp = $_FILES['photo2']['name'];
            // recupere le nom temporaire du fichier pour le deplacer
            $cheminTemp = $_FILES['photo2']['tmp_name'];
            
            $nomImage = $idProduit . "_2.png";
            
            $cheminFinal = HOME_SITE . "ressources/produit/" . $nomImage;
            // definition des caractéristiques d'une image
            $url = "ressources/produit/" . $nomImage;
            $titre_img = explode('.',$nomImageTemp)[0];
            $altDefault = "Image du produit : " . $titre_img;

            if(move_uploaded_file($cheminTemp,$cheminFinal)){
                // appel à la fonction pour modifier la bdd
                update_image_produit($idImage, $url, $titre_img, $altDefault);
            }
        }
        if(isset($_POST['photo3'])){
            // recupere le nom du fichier envoyé
            $nomImageTemp = $_FILES['photo3']['name'];
            // recupere le nom temporaire du fichier pour le deplacer
            $cheminTemp = $_FILES['photo3']['tmp_name'];
            
            $nomImage = $idProduit . "_3.png";
            
            $cheminFinal = HOME_SITE . "ressources/produit/" . $nomImage;
            // definition des caractéristiques d'une image
            $url = "ressources/produit/" . $nomImage;
            $titre_img = explode('.',$nomImageTemp)[0];
            $altDefault = "Image du produit : " . $titre_img;

            if(move_uploaded_file($cheminTemp,$cheminFinal)){
                // appel à la fonction pour modifier la bdd
                update_image_produit($idImage, $url, $titre_img, $altDefault);
            }
        }
        header("Location: ../");
        exit();
    }
    
?>

<!DOCTYPE html>
<html>
    <head>
        <?php include HOME_SITE . 'link_head.php'; ?>
        <title>Modifier un produit</title>
        <meta charset="UTF-8">
    </head>
    <body>
        <?php include "../../header.php" ?>
        <main>
            <!-- Bouton de retour sur la page de gestion des stocks -->
            <a href="../index.php"><img src="../../../../image/retour.svg" alt="bouton retour en arrière"></a>
            <h1>Modifier mon produit</h1>
            <form action="" name="formulaire_modification_produit" method="post" enctype="multipart/form-data">
                <h3>Informations produit</h3>
                <div>
                    <p>
                        <label for="nomPrv">Libellé privé*</label>
                        <input type="text" name="nomPrv" id="nomPrv" value="<?= $tabInfoProduit['nom_stock']?>" required>
                    </p>
                    <p>
                        <label for="nomPblc">Libellé public*</label>
                        <input type="text" name="nomPblc" id="nomPblc" value="<?= $tabInfoProduit['nom_public']?>" required>
                    </p>
                </div>
                <div>
                    <p>
                        <label for="prixProd">Prix* hors taxe (€)</label>
                        <input type="text" name="prixProd" id="prixProd" value="<?= $tabInfoProduit['prix']?>" required>
                    </p>
                    <p>
                        <label for="tva">TVA* (%)</label>
                        <input type="number" name="tva" id="tva" value="<?= $tabInfoProduit['tva']?>" required>
                    </p>
                    
                    <p>
                        <label for="codeBarre">Code barre*</label>
                        <input type="text" name="codeBarre" id="codeBarre" maxlength="13" style="width:162.4px" value="<?= $tabInfoProduit['code_barre']?>" required>
                        <span id="messageErrCodeBarre" style="display:none; color:red">Le code barre doit comporter 13 chiffres</span>
                    </p>
                </div>
                <div>
                    <p>
                        <label for="checkMajeur">Réservé aux majeurs</label>
                        <input type="checkbox" name="checkMajeur" id="checkMajeur" <?php if($tabInfoProduit['plus_18'] === 1){?> checked <?php } ?>>
                    </p>
                    <p>
                        <label for="checkEnLigne">Produit en ligne</label>
                        <input type="checkbox" name="checkEnLigne" id="idCheckEnLigne" <?php if($tabInfoProduit['en_ligne'] === 1){?> checked <?php }?>>
                    </p>
                    <p>
                        <label for="categorie">Catégories*</label>
                        <select name="categorie" id="idCategorie" style="width: 175px;" required>
                            <option value="">-- Choisir une catégorie --</option>
                            <?php
                                $tabCategorie = get_categorie();
                                foreach($tabCategorie as $nomCat){
                                    $select = ($nomCat['nom_categorie'] === $tabCategorieDuProduit) ? 'selected' : '';
                            ?>
                            <option value="<?= htmlspecialchars($nomCat['nom_categorie']) ?>" <?= $select ?>>
                                <?= htmlspecialchars($nomCat['nom_categorie']) ?>
                            </option>
                            <?php } ?>
                        </select>
                    </p>
                    <p>
                        <label for="qtAchete">Quantité acheté</label>
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
                <h3>Photos du produit</h3>
                <div>
                    <h6>Image principale</h6>
                    <img src="<?= HOME_SITE . 'ressources/produit/' . htmlentities($tabImageProduit)?>" alt="">
                    <p>
                        <label for="photoPrincipale">Photo principale</label>
                        <input type="file" name="photoPrincipale">
                    </p>
                    <p>
                        <label for="photo2">Seconde photo</label>
                        <input type="file" name="photo2">
                    </p>
                    <p style="display:none" id="idBlockPhoto3">
                        <label for="photo3">Troisième photo</label>
                        <input type="file" name="photo3">
                    </p>
                </div>
                <hr>
                <h3>Gestion du stock</h3>
                <div>
                    <p>
                        <label for="qtStock">Quantite en stock</label>
                        <input type="number" name="qtStock" id="idQtStock" value="<?= $tabInfoProduit['quantite'] ?>">
                    </p>
                    <p>
                        <label for="seuilAlerte">Seuil d'alerte</label>
                        <input type="number" name="seuilAlerte" id="idSeuilAlerte" value="<?= $tabInfoProduit['seuil_alerte']?>">
                    </p>
                </div>
                <hr>
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
                <hr>
                <h3>Livraison</h3>
                <div>
                    <p>
                        <label for="poidColis">Poid du colis</label>
                        <input type="text" name="poidColis" id="idPoidColis" value="<?= $tabInfoProduit['poids']?>">
                    </p>
                    <p>
                        <label for="volumeColis">Volume du colis</label>
                        <input type="text" name="volumeColis" id="idVolumeColis" value="<?= $tabInfoProduit['volume']?>">
                    </p>
                </div>
                <input type="submit" value="Valider les modifications">
            </form>
        </main>
        <script>
            const blockPhoto3 = getElementById("idbBockPhoto3");
            const inputPhoto2 = getElementById("idInputPhoto2");

            inputPhoto2.addEventListener(('change') => {
                if(inputPhoto2.value != null){
                    blockPhoto3.style.display = "block";
                }
            })
        </script>
        <footer>

        </footer>
    </body>
</html>
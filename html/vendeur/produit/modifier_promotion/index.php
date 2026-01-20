<?php
    // Permet d'utiliser le fichier .config.php
    define("HOME_GIT", "../../../../");
    define("HOME_SITE", "../../../");

    require_once HOME_GIT . 'fonction_produit.php';
    require_once HOME_GIT . '.config.php';

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

    if ($_GET == NULL || !isset($_GET['produit']) || !isset($_GET['idPromo'])) {
       renvoi();
    }

    $_GET['produit'] = htmlentities(trim($_GET['produit'] ?? ''));
    $id_produit = $_GET['produit'];
    $id_promo = $_GET['idPromo'];
    $prix = detail_produit($_GET['produit'])['prix'];

    if(get_info_promotion_unique($id_promo) == null){
        renvoi();
    }

    $tab_info_promotion = get_info_promotion_unique($id_promo);
    if($tab_info_promotion['reduction'] != null){
        $pourcentage = $tab_info_promotion['reduction'];
    } else {
        $pourcentage = null;
    }
    
    $tab_image_promotion = get_image_promotion($tab_info_promotion['id_image_banniere']);
    $date_debut_initial = $tab_info_promotion['date_debut'];
    $date_fin_initial = $tab_info_promotion['date_fin'];

    if($tab_image_promotion != null){
        $id_image_initial = $tab_image_promotion['id_image'];
    } else {
        $id_image_initial = null;
    }

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($_POST['pourcentage']) && !empty($_POST['pourcentage'])){
            $pourcentage = $_POST['pourcentage'];
            $pourcentage = str_replace('-', "",$pourcentage);
        } else {
            $pourcentage = null;
        }

        $id_nouvelle_banniere = $id_image_initial;
        if(isset($_POST['supp_image_promo']) && $_POST['supp_image_promo'] == 'on'){
            $id_nouvelle_banniere = null;
            update_promotion($tab_info_promotion['id_promo'],$id_produit, $_POST['dateDebut'],$_POST['dateFin'],$euro,$id_nouvelle_banniere);
            delete_image($id_image_initial);
        } else if (
            isset($_FILES['photoPromotion']) &&
            $_FILES['photoPromotion']['error'] === UPLOAD_ERR_OK
        ){
            $nomImageTemp = $_FILES['photoPromotion'];
            // recupere le nom temporaire du fichier pour le deplacer
            $cheminTemp = $_FILES['photoPromotion']['tmp_name'];
            
            $nomImage = $id_produit . "_promotion.png";
            
            $cheminFinal = HOME_SITE . "ressources/promotion/" . $nomImage;
            // definition des caractéristiques d'une image
            $url = "ressources/promotion/" . $nomImage;
            $altDefault = "Image de promotion";
            if(
                $date_debut_initial != $_POST['dateDebut'] ||
                $date_fin_initial != $_POST['dateFin']
            ){
                if(banniere_libre($_POST['dateDebut'],$_POST['dateFin'])){
                    
                    if(move_uploaded_file($cheminTemp,$cheminFinal)){
                        $id_nouvelle_banniere = add_image($url, $nomImage, $altDefault);

                            if($id_nouvelle_banniere){
                                update_promotion($tab_info_promotion['id_promo'],$id_produit, $_POST['dateDebut'],$_POST['dateFin'],$pourcentage,$id_nouvelle_banniere);
                            }
                            if($id_image_initial){
                                delete_image($id_image_initial);
                            }
                        }
                    }
                } else {
                    if(move_uploaded_file($cheminTemp,$cheminFinal)){
                        $id_nouvelle_banniere = add_image($url, $nomImage, $altDefault);

                    if($id_nouvelle_banniere){
                        update_promotion($tab_info_promotion['id_promo'],$id_produit, $_POST['dateDebut'],$_POST['dateFin'],$euro,$id_nouvelle_banniere);
                    }
                    if($id_image_initial){
                        delete_image_bdd($id_image_initial);
                    }
                }
            }
        } else {
            update_promotion($tab_info_promotion['id_promo'],$id_produit, $_POST['dateDebut'],$_POST['dateFin'],$pourcentage,$id_nouvelle_banniere);
        }
        
        header("Location: ../?produit=" . $id_produit);
        exit();
    } 
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>Alizon - Modifier la promo</title>
        <?php include HOME_SITE . 'link_head.php'; ?>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="<?=HOME_SITE?>style.css">
    </head>
    <body>
        <?php include "../../header.php" ?>
        <main class="main_promo">
            <div class="entete">
                <a href="../index.php"><img src="../../../../image/retour.svg" alt="bouton retour en arrière"></a>
                <h1>Modifier la promotion</h1>
            </div>
            <p style="color:red;">Une promotion à un coût journalier de 26€ par jour</p>
            <form action="" method="post" enctype="multipart/form-data" class="form_promo">
                <h3>Promotion</h3>
                <div class="en_ligne">
                    <div class="en_colonne">
                        <label for="dateDebut">Date de début :</label>
                        <input type="date" id="dateDebut" name="dateDebut" value=<?= htmlentities($tab_info_promotion['date_debut'])?> required>
                    </div>
                    <div class="en_colonne">
                        <label for="dateFin">Date de fin :</label>
                        <input type="date" id="dateFin" name="dateFin" value=<?= htmlentities($tab_info_promotion['date_fin'])?> required>
                    </div>
                    <div class="en_colonne">
                        <label for="cout">Coût final : </label>
                        <input class="cout_final" type="text" id="cout" readonly>
                    </div>
                </div>
                <p style="display:none; color:red;" id="warning1">Date de fin antérieur à la date de debut</p>
                <p style="display:none; color:red;" id="warning2">Date(s) non selectionné(s)</p>
                
                <?php if($tab_image_promotion != null){ ?>
                    <img src=<?= HOME_SITE . $tab_image_promotion['url_image']?> alt="Banniere de promotion">
                    <label for="photoPromotion">Changer la banniere</label>
                    <input type="file" id="photoPromotion" name="photoPromotion" accept=".png">
                    <label for="supp_image_promo">Supprimer la bannière</label>
                    <input type="checkbox" id="supp_image_promo" name="supp_image_promo">
                <?php } else { ?>
                    <div class="ajout_banniere">
                        <label for="photoPromotion">Ajouter une bannière</label>
                        <input type="file" id="photoPromotion" name="photoPromotion" accept=".png">
                    </div>
                <?php } ?>
                <h3>Réduction</h3>
                <p>Prix actuel : <?=htmlentities($prix)?>€</p>
                <div class="en_ligne">
                    <div class="en_colonne">
                        <label for="pourcentage">Pourcentage</label>
                        <input type="text" id="pourcentage" name="pourcentage" value="<?= htmlentities($pourcentage ?? '')?>">
                        <p style="display:none; color:red;" id="warning3">Le pourcentage ne peut <br>etre supérieur à 100</p>
                    </div>
                    <div class="en_colonne">
                        <label for="euro">Remise appliquée</label>
                        <input type="text" id="euro" name="euro" readonly>
                    </div>
                    <div class="en_colonne">
                        <label for="prixFinal">Prix final</label>
                        <input type="text" id="prixFinal" readonly>
                    </div>
                </div>
                <input type="submit" id="valider" value="Valider">
            </form>
            <a 
                id="supprimer_promotion"
                style="display:block; color:none;"
                href="supprimer_promotion?idProduit=<?=htmlentities($id_produit)?>&idPromo=<?=htmlentities($id_promo)?>"
            >Supprimer la promotion
            </a>
        </main>
        <?php include "../../../footer.php" ?>    
    </body>
    <script>

        // PROMOTION //
        const PRIX = 26;
        const cout = document.getElementById("cout");
        const dateDebut = document.getElementById("dateDebut");
        const dateFin = document.getElementById("dateFin");
        const valider = document.getElementById("valider");
        const warning1 = document.getElementById("warning1");
        const warning2 = document.getElementById("warning2");
        const btn_suppr = document.getElementById("supprimer_promotion");

        if(!verif_date_pour_suppression(dateDebut.value)){
            btn_suppr.style.display = "none";
        }

        function verif_date_pour_suppression(date){
            const dateCourante = new Date();
            const dateCible = new Date(date);

            const difference = dateCourante.getTime() - dateCible.getTime();
            const vingtQuatreHeure = 86400000;

            if (difference < 0) {
                return true;
            }
            if (difference >= 0 && difference <= vingtQuatreHeure) {
                return false;
            }
            return true;
        }

        dateDebut.addEventListener('change', () => {
            if(dateFin.value != ""){
                if(dateDebut.value > dateFin.value) {
                    warning1.style.display = "block";
                } else {
                    warning1.style.display = "none";
                    calculP();
                }
            }
            if(!verif_date_pour_suppression(dateDebut.value)){
                btn_suppr.style.display = "none";
            } else {
                btn_suppr.style.display = "block";
            }
            
        });
        dateFin.addEventListener('change', () => {
            if(dateDebut.value != ""){
                if(dateDebut.value > dateFin.value) {
                    warning1.style.display = "block";
                } else {
                    warning1.style.display = "none";
                    calculP();
                }
            }
            
        });

        function calculP() {
            const d1 = new Date(dateDebut.value + "T00:00:00");
            const d2 = new Date(dateFin.value + "T00:00:00");

            const diffJours = (d2 - d1) / 86400000;

            if (diffJours < 0) {
                cout.value = "";
                return;
            }

            cout.value = PRIX * diffJours + PRIX + "€";
        }

        // REDUCTION //
        const warning3 = document.getElementById("warning3");
        const pourcentage = document.getElementById("pourcentage");
        const euro = document.getElementById("euro");
        const prixInitial = <?= json_encode($prix) ?>;
        const prixFinal = document.getElementById("prixFinal");

        function fill_form_with_data(){
            const d1 = new Date(dateDebut.value + "T00:00:00");
            const d2 = new Date(dateFin.value + "T00:00:00");

            const diffJours = (d2 - d1) / 86400000;
            cout.value = PRIX * diffJours + PRIX + "€";
            
            euro.value = prixInitial * (pourcentage.value / 100);
            euro.value = Number.parseFloat(euro.value).toFixed(2);
            prixFinal.value = prixInitial - euro.value;
            prixFinal.value = Number.parseFloat(prixFinal.value).toFixed(2);
        }
        
        fill_form_with_data();
        
        pourcentage.addEventListener('input', () => {
            pourcentage.value = pourcentage.value.replace(",",".");
            pourcentage.value = pourcentage.value.replace(/[^\d.,]/g,"");
            if(pourcentage.value <= 100){
                calculR();
            } else {
                warning3.style.display = "block";
            }
        })

        function calculR(){
            if(pourcentage.value != ""){
                prixFinal.value = prixInitial * (1 - pourcentage.value / 100);
                euro.value = prixInitial - prixFinal.value;
                prixFinal.value = Number.parseFloat(prixFinal.value).toFixed(2);
                euro.value = Number.parseFloat(euro.value).toFixed(2);
            } else {
                euro.value = "";
                prixFinal.value = "";
            }
        }
        
        // VALIDATION DU FORM //
        valider.addEventListener('click', (event) => {
            warning1.style.display = "none";
            warning2.style.display = "none";
            warning3.style.display = "none";

            if (!dateDebut.value || !dateFin.value) {
                warning2.style.display = "block";
                event.preventDefault();
                return;
            }

            if (dateDebut.value > dateFin.value) {
                warning1.style.display = "block";
                event.preventDefault();
            }

            if (pourcentage.value >= 100){
                warning3.style.display = "block";
                event.preventDefault();
            }
        });
    </script>
</html>
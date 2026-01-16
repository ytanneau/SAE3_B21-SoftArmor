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

    if ($_GET == NULL || !isset($_GET['produit'])) {
       echo "Produit non trouvé";
       renvoi();
    }

    $_GET['produit'] = htmlentities(trim($_GET['produit'] ?? ''));
    $id_produit = $_GET['produit'];

    $prix = detail_produit($_GET['produit'])['prix'];
    

    if($_SERVER["REQUEST_METHOD"] == "POST"){

        if(isset($_POST['pourcentage']) && $_POST['pourcentage'] !== ""){
            $pourcentage = $_POST['pourcentage'];
            $pourcentage = str_replace('-', "",$pourcentage);
        } else {
            $pourcentage = 0;
        }

        if (isset($_FILES['photoPromotion']) &&
                $_FILES['photoPromotion']['error'] === UPLOAD_ERR_OK && 
                banniere_libre($_POST['dateDebut'],$_POST['dateFin'])){
        $nomImageTemp = $_FILES['photoPromotion'];
        // recupere le nom temporaire du fichier pour le deplacer
        $cheminTemp = $_FILES['photoPromotion']['tmp_name'];
        
        $nomImage = $id_produit . "_promotion.png";
        
        $cheminFinal = HOME_SITE . "ressources/promotion/" . $nomImage;
        // definition des caractéristiques d'une image
        $url = "ressources/promotion/" . $nomImage;
        $altDefault = "Image de promotion";
        if(move_uploaded_file($cheminTemp,$cheminFinal)){
            $id_image_principal = add_image($url,$nomImage, $altDefault);
        }
        } else {
            $id_image_principal = null;
        }
        creer_promotion($id_produit, $_POST['dateDebut'],$_POST['dateFin'],$pourcentage,$id_image_principal);
        header("Location: ../?produit=" . $id_produit);
        exit();
    }

?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>Alizon - Démarrer une promotion</title>
        <?php include HOME_SITE . 'link_head.php'; ?>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="<?=HOME_SITE?>style.css">
    </head>
    <body>
        <?php include "../../header.php" ?>
        <h1>Démarrer une promotion</h1>
        <p style="color:red;">Une promotion à un coûp journalier de 26€ par jour</p>
        <form action="" method="post" enctype="multipart/form-data" class="form_promo">
            <h3>Promotion</h3>
            <label for="dateDebut">Date de début</label>
            <input type="date" id="dateDebut" name="dateDebut" required>
            <label for="dateFin">Date de fin (incluse)</label>
            <input type="date" id="dateFin" name="dateFin" required>
            <p style="display:none; color:red;" id="warning1">Date de fin antérieur à la date de debut</p>
            <p style="display:none; color:red;" id="warning2">Date(s) non selectionné(s)</p>
            <label for="cout">Coût final : </label>
            <input type="text" id="cout" readonly>
            <div id="divPhoto" style="display:block;">
                <label for="photoPromotion">Ajouter une banniere</label>
                <input type="file" id="photoPromotion" name="photoPromotion" accept=".png">
            </div>

            <h3>Réduction</h3>
            <p>Prix actuel : <?=htmlentities($prix)?>€</p>
            <label for="pourcentage">Pourcentage</label>
            <input type="text" id="pourcentage" name="pourcentage">
            <p style="display:none; color:red;" id="warning3">Le pourcentage ne peut etre supérieur à 100</p>
            <label for="euro">Remise appliquée</label>
            <input type="text" id="euro" name="euro" readonly>
            <label for="prixFinal">Prix final</label>
            <input type="text" id="prixFinal" readonly>
            <input type="submit" id="valider" value="Valider">
        </form>
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
        const divPhoto = document.getElementById("divPhoto");

        // const tab_date_occupe = <?php // echo json_encode($tab_date) ?>;

        dateDebut.addEventListener('change', () => {
            if(dateFin.value != ""){
                if(dateDebut.value > dateFin.value) {
                    warning1.style.display = "block";
                } else {
                    warning1.style.display = "none";
                    calculP();
                }
            } 
            if (check_date(dateDebut.value)){
                divPhoto.style.display = "block";
            } else {
                divPhoto.style.display = "none";
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
            if (check_date(dateFin.value)){
                divPhoto.style.display = "block";
            } else {
                divPhoto.style.display = "none";
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

        /*function check_date(date){
            for()
        }*/
        // REDUCTION //

        const warning3 = document.getElementById("warning3");
        const pourcentage = document.getElementById("pourcentage");
        const euro = document.getElementById("euro");
        const prixInitial = <?= json_encode($prix) ?>;
        const prixFinal = document.getElementById("prixFinal");

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
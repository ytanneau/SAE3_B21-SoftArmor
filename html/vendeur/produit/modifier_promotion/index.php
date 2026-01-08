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
    $tab_info_promotion = get_info_promotion($id_produit);

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $euro = $_POST['euro'];
        $euro = explode('-', $euro)[1];

            if (isset($_FILES['photoPromotion']) &&
                    $_FILES['photoPromotion']['error'] === UPLOAD_ERR_OK && 
                    banniere_libre($_POST['dateDebut'],$_POST['dateFin'])){
            $nomImageTemp = $_FILES['photoPromotion'];
            // recupere le nom temporaire du fichier pour le deplacer
            $cheminTemp = $_FILES['photoPromotion']['tmp_name'];
            
            $nomImage = $id_produit . "_promotion.png";
            
            $cheminFinal = HOME_SITE . "ressources/produit/" . $nomImage;
            // definition des caractéristiques d'une image
            $url = "ressources/produit/" . $nomImage;
            $altDefault = "Image de promotion";
            if(move_uploaded_file($cheminTemp,$cheminFinal)){
                $id_image_principal = add_image($url,$nomImage, $altDefault);
            }
        } else {
            $id_image_principal = null;
        }
        update_promotion($tab_info_promotion['id_promo'],$id_produit, $_POST['dateDebut'],$_POST['dateFin'],$euro,$id_image_principal);
        
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
        <h1>Modifier la promotion</h1>
        <p style="color:red;">Une promotion à un coûp journalier de 26€ par jour</p>
        <form action="" method="post" enctype="multipart/form-data">
            <h3>Promotion</h3>
            <label for="dateDebut">Date de début</label>
            <input type="date" id="dateDebut" name="dateDebut" value=<?= htmlentities($tab_info_promotion['date_debut'])?> required>
            <label for="dateFin">Date de fin (incluse)</label>
            <input type="date" id="dateFin" name="dateFin" value=<?= htmlentities($tab_info_promotion['date_fin'])?> required>
            <p style="display:none; color:red;" id="warning1">Date de fin antérieur à la date de debut</p>
            <p style="display:none; color:red;" id="warning2">Date(s) non selectionné(s)</p>
            <label for="cout">Coût final : </label>
            <input type="text" id="cout" readonly>
            
            <h3>Réduction</h3>
            <p>Prix actuel : <?=htmlentities($prix)?></p>
            <label for="pourcentage">Pourcentage</label>
            <input type="text" id="pourcentage">
            <p style="display:none; color:red;" id="warning3">Le pourcentage ne peut etre supérieur à 100</p>
            <label for="euro">Remise appliquée</label>
            <input type="text" id="euro" name="euro" value=<?= htmlentities($tab_info_promotion['reduction'])?> readonly>
            <label for="prixFinal">Prix final</label>
            <input type="text" id="prixFinal" readonly>
            <label for="photoPromotion">Choisir une banniere</label>
            <input type="file" id="photoPromotion" name="photoPromotion" accept=".png">
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

        dateDebut.addEventListener('change', () => {
            if(dateFin.value != ""){
                if(dateDebut.value > dateFin.value) {
                    warning1.style.display = "block";
                } else {
                    warning1.style.display = "none";
                    calculP();
                }
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

        pourcentage.value = ((prixInitial / (prixInitial - euro.value)) - 1) * 100;

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
                euro.value = prixFinal.value - prixInitial;
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

            if (!dateDebutP.value || !dateFinP.value) {
                warning2.style.display = "block";
                event.preventDefault();
                return;
            }

            if (dateDebutP.value > dateFinP.value) {
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
<?php
    define("HOME_GIT", "../../../../");
    define("HOME_SITE", "../../../");
    
    require_once HOME_GIT . '.config.php';
    require_once HOME_GIT . 'fonction_produit.php';

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

    $id_produit = $_GET['produit'];
    $prix = get_prix_produit($id_produit);

    if($_SERVER['REQUEST_METHOD'] == "POST"){
        if($_POST['pourcentage'] <= 100 && $_POST['pourcentage'] != null && $_POST['dateFin'] != null && $_POST['dateDebut'] != null){
            $id_reduction = create_reduction($id_produit, $_POST['dateDebut'], $_POST['dateFin'], $_POST['pourcentage']);
        }

        if($id_reduction === -1){
            echo '<script language="Javascript">alert("Erreur de création de la réduction")</script>';
        } else {
            header('location: ../?produit='.$id_produit);
        }
    }
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>Alizon - Démarrer une réduction</title>
        <?php include HOME_SITE . 'link_head.php'; ?>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="<?=HOME_SITE?>style.css">
    </head>
    <body>
        <?php include "../../header.php" ?>
        <main class="main_reduc">
            <div class="entete">
                <a href="../index.php?produit=<?=$id_produit?>"><img src="../../../../image/retour.svg" alt="bouton retour en arrière"></a>
                <h1>Démarrer une réduction</h1>
            </div>
            <p>Prix actuel : <?=htmlentities($prix)?>€</p>
            <form action="" method="post">
                <div class="en_ligne">
                    <div class="en_colonne">
                        <label for="dateDebut">Date de début de la réduction : </label>
                        <input type="date" id="dateDebut" name="dateDebut" required>
                    </div>
                    <div class="en_colonne">
                        <label for="dateFin">Date de fin de la réduction : </label>
                        <input type="date" id="dateFin" name="dateFin" required>
                    </div>
                </div>

                <p style="display:none;" id="warning1">Date de fin antérieur à la date de debut</p>
                <p style="display:none;" id="warning2">Date(s) non selectionnée(s)</p>
                <p style="display:none;" id="warning3">Date de debut déjà passé</p>

                <div class="en_ligne">
                    <div class="en_colonne">
                        <label for="pourcentage">Pourcentage : </label>
                        <input type="text" id="pourcentage" name="pourcentage" required>
                        <p style="display:none;" id="warningPourcentage">Le pourcentage ne peut <br>être supérieur à 100</p>
                    </div>
                    <div class="en_colonne">
                        <label for="euro">Remise appliquée : </label>
                        <input type="text" id="euro" readonly>
                    </div>
                    <div class="en_colonne">
                        <label for="prixFinal">Prix final : </label>
                        <input type="text" id="prixFinal" readonly>
                    </div>
                </div>
                <input type="submit" id="valider">
            </form>
        </main>
        <?php include HOME_SITE . "footer.php"?>
        <script>
            

            // REDUCTION //
            const warningPourcentage = document.getElementById("warningPourcentage");
            const pourcentage = document.getElementById("pourcentage");
            const euro = document.getElementById("euro");
            const prixInitial = <?= json_encode($prix) ?>;
            const prixFinal = document.getElementById("prixFinal")
            const dateDebut = document.getElementById("dateDebut")
            const dateFin = document.getElementById("dateFin")
            const warning1 = document.getElementById("warning1");
            const warning2 = document.getElementById("warning2");
            const warning3 = document.getElementById("warning3");

            warningPourcentage.style.display = "none";

            pourcentage.addEventListener('input', () => {
                pourcentage.value = pourcentage.value.replace(",",".");
                pourcentage.value = pourcentage.value.replace(/[^\d.,]/g,"");
                if(pourcentage.value <= 100){
                    calculR();
                } else {
                    warningPourcentage.style.display = "block";
                }
                
            })

            function calculR(){
                if(pourcentage.value != ""){
                    prixFinal.value = prixInitial * (1 - pourcentage.value / 100);
                    euro.value = prixInitial - prixFinal.value;
                    prixFinal.value = Number.parseFloat(prixFinal.value).toFixed(2) + "€";
                    euro.value = Number.parseFloat(euro.value).toFixed(2);
                } else {
                    euro.value = "";
                }
            }

            dateDebut.addEventListener('change', () => {
                if(dateFin.value != ""){
                    if(dateDebut.value > dateFin.value) {
                        warning1.style.display = "block";
                    } else if (dateFin == ""){
                        dateFin.value = dateDebut.value;
                    } else if(new Date(dateDebut.value).getTime() < dateCourante.getTime()){
                        warning4.style.display = "block";
                    } else {
                        warning1.style.display = "none";
                        warning4.style.display = "none";
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
                if (check_date(dateFin.value)){
                    divPhoto.style.display = "block";
                } else {
                    divPhoto.style.display = "none";
                }
            });

            pourcentage.addEventListener('input', () => {
                pourcentage.value = pourcentage.value.replace(",",".");
                pourcentage.value = pourcentage.value.replace(/[^\d.,]/g,"");
                if(pourcentage.value <= 100){
                    calculR();
                } else {
                    warning.style.display = "block";
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

            let date = new Date()
            let current_string_date

            if(date.getMonth() < 9){
                current_string_date= date.getFullYear() + "-0" + (date.getMonth()+1) + "-" + date.getDate()
            } else {
                current_string_date = date.getFullYear() + "-" + date.getMonth() + "-" + date.getDate()
            }
            dateDebut.value = current_string_date
            dateFin.value = current_string_date
        </script>
    </body>
</html>
                
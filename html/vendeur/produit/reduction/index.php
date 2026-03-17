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
    $date_occupe_reduction = get_all_date_reduction($id_produit);
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
            <p>Prix actuel du produit : <?=htmlentities($prix)?>€</p>
            <form action="" method="post">
                <div class="en_ligne">
                    <div class="en_colonne">
                        <label for="dateDebut">Début de la réduction : </label>
                        <input type="date" id="dateDebut" name="dateDebut" required>
                    </div>
                    <div class="en_colonne">
                        <label for="dateFin">Fin de la réduction : </label>
                        <input type="date" id="dateFin" name="dateFin" required>
                    </div>
                </div>

                <p style="display:none;" class="warning" id="warning_date_anterieur">Date de fin antérieur à la date de debut</p>
                <p style="display:none;" class="warning" id="warning_date_passe">Date de debut déjà passé</p>
                <p style="display:none;" class="warning" id="warning_date_occupe">Date déjà prise pas une reduction sur ce produit</p>

                <div class="en_ligne">
                    <div class="en_colonne">
                        <label for="pourcentage">Pourcentage : </label>
                        <input type="text" id="pourcentage" name="pourcentage" required>
                        <p style="display:none;" id="warningPourcentage">Le pourcentage ne peut <br>être supérieur à 100</p>
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
            const prixInitial = <?= json_encode($prix) ?>;
            const prixFinal = document.getElementById("prixFinal")
            const dateDebut = document.getElementById("dateDebut")
            const dateFin = document.getElementById("dateFin")
            const warning_date_anterieur = document.getElementById("warning_date_anterieur");
            const warning_date_passe = document.getElementById("warning_date_passe");
            const warning_date_occupe = document.getElementById("warning_date_occupe");

            const date_occupe = <?= json_encode($date_occupe_reduction)?>;
            console.log(date_occupe)
            let date = new Date()
            let current_string_date

            pourcentage.addEventListener('input', () => {
                pourcentage.value = pourcentage.value.replace(",",".");
                pourcentage.value = pourcentage.value.replace(/[^\d.,]/g,"");
                if(pourcentage.value <= 100 && pourcentage.value != ""){
                    prixFinal.value = prixInitial * (1 - pourcentage.value / 100);
                    prixFinal.value = Number.parseFloat(prixFinal.value).toFixed(2) + "€";
                } else {
                    warningPourcentage.style.display = "block";
                }
            })

            dateDebut.addEventListener('change', () => {
                if(dateFin.value != ""){
                    if(dateDebut.value > dateFin.value) {
                        warning_date_anterieur.style.display = "block";
                    } else if(new Date(dateDebut.value).getTime() < date.getTime()){
                        warning_date_passe.style.display = "block";
                    } else {
                        warning_date_anterieur.style.display = "none";
                        warning_date_passe.style.display = "none";
                    }
                }

                if(check_dispo_date(dateDebut.value)){
                    warning_date_occupe.style.display = "block"
                } else {
                    warning_date_occupe.style.display = "none"
                }
            });

            dateFin.addEventListener('change', () => {
                if(dateDebut.value != ""){
                    if(dateDebut.value > dateFin.value) {
                        warning_date_anterieur.style.display = "block";
                    } else {
                        warning_date_anterieur.style.display = "none";
                    }
                }
                if(check_dispo_date(dateFin.value)){
                    warning_date_occupe.style.display = "block"
                } else {
                    warning_date_occupe.style.display = "none"
                }
            });

            function check_dispo_date(date) {
                if(date_occupe == null){
                    return true
                } else {
                    date_occupe.forEach(periode => {
                        if(new Date(date).getTime() >= new Date(periode['date_debut']).getTime() && 
                            new Date(date).getTime() <= new Date(periode['date_fin']).getTime()){
                            return true
                        } else {
                            return false
                        }
                    });
                }
            }

            // INITIALISATION DES INPUT DATES A LA DATE DU JOUR //
            

            if(date.getMonth() < 9){
                current_string_date= date.getFullYear() + "-0" + (date.getMonth()+1) + "-" + date.getDate()
            } else {
                current_string_date = date.getFullYear() + "-" + date.getMonth() + "-" + date.getDate()
            }
            dateDebut.value = current_string_date
            dateFin.value = current_string_date

            if(check_dispo_date(dateDebut.value)){
                warning_date_occupe.style.display = "block"
            } else {
                warning_date_occupe.style.display = "none"
            }
        </script>
    </body>
</html>
                
<?php
    define("HOME_GIT", "../../../../../");
    define("HOME_SITE", "../../../../");
    
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

    if ($_GET == NULL || !isset($_GET['produit']) || !isset($_GET['idReduc'])) {
       echo "Produit non trouvé";
       renvoi();
    }

    $id_produit = $_GET['produit'];
    $id_reduction = $_GET['idReduc'];
    $prix = get_prix_produit($id_produit);
    $tab_reduction = get_reduction($id_reduction);

    if($tab_reduction == null){
        renvoi();
    };

    if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_POST)){
        update_reduction($id_reduction, $_POST['dateDebut'], $_POST['dateFin'], $_POST['pourcentage']);
        header('location: ../../?produit='.$id_produit);
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
    <body><?php include "../../../header.php" ?>
        <main class="main_reduc">
            <div class="entete">
                <a href="../../index.php?produit=<?=$id_produit?>"><img src="../../../../../image/retour.svg" alt="bouton retour en arrière"></a>
                <h1>Modifier la réduction</h1>
            </div>
            <p>Prix actuel du produit : <?=htmlentities($prix)?>€</p>
            <form action="" method="post">
                <div class="en_ligne">
                    <div class="en_colonne">
                        <label for="dateDebut">Debut de la réduction : </label>
                        <input type="date" id="dateDebut" name="dateDebut" required>
                    </div>
                    <div class="en_colonne">
                        <label for="dateFin">Fin de la réduction : </label>
                        <input type="date" id="dateFin" name="dateFin" required>
                    </div>
                </div>

                <p style="display:none;" class="warning" id="warning_date_anterieur">Date de fin antérieur à la date de debut</p>
                <p style="display:none;" class="warning" id="warning_date_passe">Date de debut déjà passé</p>

                <div class="en_ligne">
                    <div class="en_colonne">
                        <label for="pourcentage">Pourcentage : </label>
                        <input type="text" id="pourcentage" name="pourcentage" required value="<?=htmlentities($tab_reduction['pourcentage'])?>">
                        <p style="display:none;" id="warningPourcentage">Le pourcentage ne peut <br>être supérieur à 100</p>
                    </div>
                    <div class="en_colonne">
                        <label for="prixFinal">Prix final : </label>
                        <input type="text" id="prixFinal" readonly>
                    </div>
                </div>
                <input type="submit" id="valider">
                <a id="supprimer" href="supprimer_reduc.php?idProduit=<?=htmlentities($id_produit)?>&idReduc=<?=htmlentities($id_reduction)?>">Supprimer la réduction</a>
            </form>
        </main>
        <?php include HOME_SITE . "footer.php"?>
    </body>
    <script>
        let tab_reduction = <?= json_encode($tab_reduction)?>;

        const warningPourcentage = document.getElementById("warningPourcentage");
        const pourcentage = document.getElementById("pourcentage");
        const prixInitial = <?= json_encode($prix) ?>;
        const prixFinal = document.getElementById("prixFinal")
        const dateDebut = document.getElementById("dateDebut")
        const dateFin = document.getElementById("dateFin")
        const warning_date_anterieur = document.getElementById("warning_date_anterieur");
        const warning_date_passe = document.getElementById("warning_date_passe");

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
        });

        dateDebut.value = tab_reduction['date_debut']
        dateFin.value = tab_reduction['date_fin']
        prixFinal.value = prixInitial * (1-(pourcentage.value / 100))
        prixFinal.value = Number.parseFloat(prixFinal.value).toFixed(2) + "€"
    </script>
</html>

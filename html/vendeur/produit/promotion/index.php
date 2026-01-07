<?php
    // Permet d'utiliser le fichier .config.php
    define("HOME_GIT", "../../../../");
    define("HOME_SITE", "../../../");

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

    $_GET['produit'] = htmlentities(trim($_GET['produit'] ?? ''));

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
        <h1>Démarer une promotion</h1>
        <p style="color:red;">Une promotion à un coûp journalier de 26€ par jour</p>
        <form action="" method="post">
            <label for="">Date de début</label>
            <input type="date" id="dateDebut">
            <label for="">Date de fin (incluse)</label>
            <input type="date" id="dateFin">
            <p style="display:none; color:red;" id="warning1">Date de fin antérieur à la date de debut</p>
            <p style="display:none; color:red;" id="warning2">Date(s) non selectionné(s)</p>
            <label for="">Coût final : </label>
            <input type="text" value="" id="cout" disabled>
            <input type="submit" id="valider" value="Valider">
        </form>
        <?php include "../../../footer.php" ?>    
    </body>
    <script>
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
                    calcul();
                }
            }
            
        });
        dateFin.addEventListener('change', () => {
            if(dateDebut.value != ""){
                if(dateDebut.value > dateFin.value) {
                    warning1.style.display = "block";
                } else {
                    warning1.style.display = "none";
                    calcul();
                }
            }
            
        });

        function calcul() {
            const d1 = new Date(dateDebut.value + "T00:00:00");
            const d2 = new Date(dateFin.value + "T00:00:00");

            const diffJours = (d2 - d1) / 86400000;

            if (diffJours < 0) {
                cout.value = "";
                return;
            }

            cout.value = PRIX * diffJours + PRIX + "€";
        }

        valider.addEventListener('click', (event) => {
            warning1.style.display = "none";
            warning2.style.display = "none";

            if (!dateDebut.value || !dateFin.value) {
                warning2.style.display = "block";
                event.preventDefault();
                return;
            }

            if (dateDebut.value > dateFin.value) {
                warning1.style.display = "block";
                event.preventDefault();
            }
        });

    </script>
</html>
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

    $prix = detail_produit($_GET['produit'])['prix'];

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        print_r($_POST);
    }

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
        <form action="" method="post" enctype="multipart/form-data">
            <h3>Promotion</h3>
            <label for="dateDebutP">Date de début</label>
            <input type="date" id="dateDebutP" name="dateDebutP" required>
            <label for="dateFinP">Date de fin (incluse)</label>
            <input type="date" id="dateFinP" name="dateFinP" required>
            <p style="display:none; color:red;" id="warning1">Date de fin antérieur à la date de debut</p>
            <p style="display:none; color:red;" id="warning2">Date(s) non selectionné(s)</p>
            <label for="cout">Coût final : </label>
            <input type="text" id="cout" disabled>
            
            <h3>Réduction</h3>
            <p>Prix actuel : <?=htmlentities($prix)?></p>
            <label for="pourcentage">Pourcentage</label>
            <input type="text" id="pourcentage" name="pourcentage">
            <label for="euro">Remise appliquée</label>
            <input type="text" id="euro" name="euro" disabled>
            <label for="prixFinal">Prix final</label>
            <input type="text" id="prixFinal" disabled>
            <input type="file" id="photoPromotion" name="photoPromotion" accept=".png">
            <input type="submit" id="valider" value="Valider">
        </form>
        <?php include "../../../footer.php" ?>    
    </body>
    <script>

        // PROMOTION //
        const PRIX = 26;
        const cout = document.getElementById("cout");
        const dateDebutP = document.getElementById("dateDebutP");
        const dateFinP = document.getElementById("dateFinP");
        const valider = document.getElementById("valider");
        const warning1 = document.getElementById("warning1");
        const warning2 = document.getElementById("warning2");

        dateDebutP.addEventListener('change', () => {
            if(dateFinP.value != ""){
                if(dateDebutP.value > dateFinP.value) {
                    warning1.style.display = "block";
                } else {
                    warning1.style.display = "none";
                    calculP();
                }
            }
            
        });
        dateFinP.addEventListener('change', () => {
            if(dateDebutP.value != ""){
                if(dateDebutP.value > dateFinP.value) {
                    warning1.style.display = "block";
                } else {
                    warning1.style.display = "none";
                    calculP();
                }
            }
            
        });

        function calculP() {
            const d1 = new Date(dateDebutP.value + "T00:00:00");
            const d2 = new Date(dateFinP.value + "T00:00:00");

            const diffJours = (d2 - d1) / 86400000;

            if (diffJours < 0) {
                cout.value = "";
                return;
            }

            cout.value = PRIX * diffJours + PRIX + "€";
        }

        // REDUCTION //
        const warning3 = document.getElementById("warning3");
        const warning4 = document.getElementById("warning4");
        const pourcentage = document.getElementById("pourcentage");
        const euro = document.getElementById("euro");
        const prixInitial = <?= json_encode($prix) ?>;
        const prixFinal = document.getElementById("prixFinal");

        pourcentage.addEventListener('input', () => {
            pourcentage.value = pourcentage.value.replace(",",".");
            pourcentage.value = pourcentage.value.replace(/[^\d.,]/g,"");
            calculR();
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

            if (!dateDebutP.value || !dateFinP.value) {
                warning2.style.display = "block";
                event.preventDefault();
                return;
            }

            if (dateDebutP.value > dateFinP.value) {
                warning1.style.display = "block";
                event.preventDefault();
            }
        });
    </script>
</html>
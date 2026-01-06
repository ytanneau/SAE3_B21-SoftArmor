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
        <label for="">Date de début</label>
        <input type="date" id="dateDebut">
        <label for="">Date de fin (incluse)</label>
        <input type="date" id="dateFin">
        <p>Coût final : </p>
        <input type="text" value="" id="cout">
        <?php include "../../../footer.php" ?>    
    </body>
    <script>
        const cout = document.getElementById("cout");
        const dateDebut = document.getElementById("dateDebut");
        const dateFin = document.getElementById("dateFin");
        dateDebut.addEventListener('change', () => {
            console.log(dateDebut);   
        })
    </script>
</html>
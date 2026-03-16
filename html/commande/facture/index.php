<?php
const HOME_GIT = "../../";
const HOME_SITE = "../";
const IP = "host.docker.internal";
const PORT = "9000";
const RAPTOR = false;

$JOUR_SEMAINE = ["dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi"];
$MOIS_ANNEE = ["janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre"];


if (!isset($_SESSION)) {
    session_start();
}

// Si pas co, alors go page connexion
if (!isset($_SESSION['logged_in'])) {
    header("location: " . HOME_SITE . "compte/connexion");
}

// Si co avec compte vendeur, envoyé à la page vendeur
if (isset($_SESSION["raison_sociale"])) {
    header("location: " . HOME_SITE . "vendeur/stock");
}
require_once HOME_GIT . ".config.php";
require_once HOME_GIT . "/fonction_commande.php";
require_once HOME_GIT . "/fonction_facture.php";
require_once HOME_GIT . "/fonction_compte.php";

if (isset($_GET["commande"])) {
    $liste_elements = get_elements_commande($_GET["commande"]);
    $date_commande = get_date_commande($_GET['commande']);
} else {
    $liste_commandes = get_commandes($_SESSION["id_compte"]);

}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alizon - Vos commandes</title>
    <?php include HOME_SITE . 'link_head.php' ?>

    <script>
        function generePDF() {
            window.print();
        }
    </script>
</head>

<body class="liste">
    <?php
    include HOME_SITE . "header.php";

    ?>

    <main>

        <?php if (isset($_GET["commande"])) { ?>
            <?php if (count($liste_elements) == 0) { ?>
                <p>Vous n'avez pas accès à cette commande</p>
            <?php } else {
                facture_client($_GET['commande']);
            } ?>

        <?php } ?>


    </main>

    <?php
    if (!isset($_GET["commande"])) {
        include HOME_SITE . "footer.php";
    } ?>
</body>

</html>
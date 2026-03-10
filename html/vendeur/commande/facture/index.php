<?php
const HOME_GIT = "../../../../";
const HOME_SITE = "../../../";

$JOUR_SEMAINE = ["dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi"];
$MOIS_ANNEE = ["janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre"];



if (!isset($_SESSION)) {
    session_start();
}

// Si pas co, alors go page connexion
if (!isset($_SESSION['logged_in'])) {
    header("location: " . HOME_SITE . "vendeur");
}

// Si co avec compte client, retour à l'accueil
else if (!isset($_SESSION["raison_sociale"])) {
    header("location: " . HOME_SITE);
}

require_once HOME_GIT . ".config.php";
require_once HOME_GIT . "/fonction_commande.php";
require_once HOME_GIT . "/fonction_facture.php";

if (isset($_GET["commande"])) {
    $liste_elements = get_elements_commande_vendeur($_GET["commande"], $_SESSION["id_compte"]);
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alizon - Les commandes</title>
    <?php include HOME_SITE . 'link_head.php' ?>
</head>

<body class="facture-page">
    <?php include HOME_SITE . 'vendeur/header.php' ?>
    <main>

        <?php if (isset($_GET["commande"])) { ?>

            <div class="facture">
                <div class="facture-head">

                </div>

                <a href="."><img src="<?= HOME_SITE ?>image/retour.svg"></a>
                <?php if (count($liste_elements) == 0) { ?>
                    <p>Vous n'avez pas accès à cette commande</p>
                <?php } else {
                    $somme_totale = 0;
                    $pseudo = get_pseudo_commande($_GET['commande']);
                    $date_commande = get_date_commande($_GET['commande']);

                    $d = strtotime($date_commande);
                    $jour = $JOUR_SEMAINE[date("w", $d)];
                    $mois = $MOIS_ANNEE[date((int) "m", $d)];

                    ?>

                    <p>Commande du <?= $jour . date(" d ", $d) . $mois . date(" Y à H:i:s", $d) ?></p>
                    <p>Faite par <?= $pseudo ?></p>
                    <h1>Liste des éléments de la commande : </h1>
                    <ul>
                        <?php foreach ($liste_elements as $element) { ?>
                            <li>
                                <p>Produit : <?= $element["nom_produit"] ?></p>
                                <p>Prix unitaire : <?= number_format($element["prix"], 2, ',', ' ') ?> €</p>
                                <p>Quantité achetée : <?= $element["quantite"] ?></p>
                                <p>Prix total : <?= number_format($element["prix"] * $element["quantite"], 2, ',', ' ') ?> €</p>
                            </li>
                            <hr>

                            <?php $somme_totale += $element["prix"] * $element["quantite"] ?>
                        <?php } ?>
                    </ul>

                    <p>Somme totale de la commande : <?= number_format($somme_totale, 2, ',', ' ') ?> €</p>

                    <button class="bouton" onclick="generePDF()">Générer le fichier PDF de cette commande</button>
                <?php } ?>
            </div>

        <?php } ?>

    </main>

    <?php facture_vendeur($_SESSION['id_compte'], $_GET['commande']) ?>

</body>
</html>
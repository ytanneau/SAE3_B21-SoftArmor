<?php
const HOME_GIT = "../../";
const HOME_SITE = "../";

$JOUR_SEMAINE = ["dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi"];
$MOIS_ANNEE = ["décembre", "janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre"];



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

if (isset($_GET["commande"])) {
    $liste_elements = get_elements_commande($_GET["commande"]);
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
</head>

<body class="liste">
    <?php include HOME_SITE . 'header.php'?>
    <main>

    <?php if (isset($_GET["commande"])) {?>

        <div>
            <a href="."><img src="../image/retour.svg"></a>

            <?php if (count($liste_elements) == 0) { ?>
                <p>Vous n'avez pas accès à cette commande</p>

            <?php } else { 
                $somme_totale = 0;
                $vendeur_prec = "";
                ?>
                
                <h1>Liste des éléments de la commande : </h1>
                <ul>
                    <hr>
                <?php foreach ($liste_elements as $element) {
                    if ($element["nom_vendeur"] != $vendeur_prec) {?>
                        <h2>Vendu par <?=$element["nom_vendeur"]?></h2>
                    <?php
                    $vendeur_prec = $element["nom_vendeur"];
                    } ?>

                    <li>
                        <p>Produit : <?=$element["nom_produit"]?></p>
                        <p>Prix unitaire : <?=number_format($element["prix"], 2, ',', ' ')?> €</p>
                        <p>Quantité achetée : <?=$element["quantite"]?></p>
                        <p>Prix total : <?=number_format($element["prix"] * $element["quantite"], 2, ',', ' ')?> €</p>
                    </li>
                    <hr>

                    <?php $somme_totale += $element["prix"] * $element["quantite"]?>
                <?php } ?>
                </ul>
                
                <p>Somme totale de la commande : <?=number_format($somme_totale, 2, ',', ' ')?> €</p>
            <?php } ?>
        </div>

    <?php } else { ?>
        <ul>
            <?php if (count($liste_commandes) == 0) { ?>
                <p>Vous n'avez effectué aucune commande sur le site avec ce compte</p>
            <?php } else {
                    foreach ($liste_commandes as $commande) {
                    $d = strtotime($commande["date_commande"]);
                    $jour = $JOUR_SEMAINE[date("w", $d)];
                    $mois = $MOIS_ANNEE[date("w", $d)];
                    ?>

                    <li>
                        <div>
                            <p>Commande du <?=$jour . date(" d ", $d) . $mois . date(" Y à H:i:s", $d)?></p>
                            <a href="?commande=<?=$commande["id_commande"]?>" class="bouton">Consulter la commande</a>
                        </div>
                        <hr>
                    </li>

            <?php } } ?>

        </ul>

    </main>
        
    <?php } ?>

</body>
</html>
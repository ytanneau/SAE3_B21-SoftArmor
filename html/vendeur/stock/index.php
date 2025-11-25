<?php

define("HOME_GIT", "../../../");
define("HOME_SITE", "../../");

if (!isset($_SESSION)) {
    session_start();
}

// Si je suis connecté mais pas en tant que vendeur, retour à l'accueil client
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && !isset($_SESSION['raison_sociale'])) {
    header('location: ' . HOME_SITE);
    exit;
}
// Sinon si je ne suis pas connecté, retour à la page connexion vendeur
else if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] === false) {
    header('location: ../');
    exit;
}

//permet d'utiliser le fichier config.php
require_once HOME_GIT . '.config.php';
require_once HOME_GIT . 'fonction_produit.php';

function ecrire_nom($rows){
    foreach ($rows as $row){
        ?>
        
        <table id=<?= htmlentities($row['id_produit'] ?? '')?>>
            <tr>
                <!-- <td><img src="MenuBurger.png" alt=""> </td> -->
                <td> 
                    <!-- le nom du produit (nom_stock) avec le lien qui est l'id du produit (id_produit) -->
                    <a href= "../produit/?produit=<?= htmlentities($row['id_produit'] ?? '') ?>"> <?= htmlentities($row['nom_stock'] ?? '')?> 
                    </a>
                </td>
                <!-- <td><img src="eyeclose.png" alt=""> </td>
                <td><img src="promotion.png" alt=""> </td>
                <td><img src="Fleche.png" alt=""> </td> -->
                <td> | </td>
                <td>
                    <form action="./update_stock.php">
                    <label for="nb">quantité</label>
                    <input type="text" id="nb" name="nb" value=<?= htmlentities($row['quantite'] ?? '')?>>
                    <input type="submit" value="Valider">
                    </form> 
                </td>
            </tr>
        </table>
        
        <?php
    }
}

//commande qui permet de séléctionner l'id du produit, son nom et sa quantité en stock
$stmt = vendeur_All_produit($_SESSION['id_compte']);



?>

<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <?php include HOME_SITE . 'link_head.php' ?>
        <title>Alizon Vendeur - Stock</title>
    </head>
    <body>
        <?php include HOME_SITE . 'vendeur/header.php'; ?>
        <?php include HOME_SITE . 'vendeur/toolbar_stock.php'; ?>

        <main>
            <!-- affiche tous les produits -->
            <?php ecrire_nom($stmt); ?>
        </main>
        <?php include HOME_SITE . "vendeur/footer.php" ?>
    </body>
</html>



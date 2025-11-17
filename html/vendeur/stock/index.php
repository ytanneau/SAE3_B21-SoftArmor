<?php

define("HOME_GIT", "../");
if (!isset($_SESSION)) {
    session_start();
}
//verifie si quelqun est connecté
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('location: ' . HOME_GIT);
    exit;
}

//permet d'utiliser le fichier config.php
require_once '../../../.config.php';
require_once HOME_GIT . 'fonction_produit.php';

function ecrire_nom($nom_stock){
    $rows = $nom_stock->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row){
        ?>
        
        <table>
            <tr>
                <td><img src="MenuBurger.png" alt=> </td>
                <td> 
                    <!-- le nom du produit (nom_stock) avec le lien qui est l'id du produit (id_produit) -->
                    <a href= "../produit/index.php?produit=<?= $row['id_produit'] ?>"> <?= $row['nom_stock']?> 
                    </a>
                </td>
                <td><img src="eyeclose.png" alt=""> </td>
                <td><img src="promotion.png" alt=""> </td>
                <td><img src="Fleche.png" alt=""> </td>
                <td> | </td>
                <td><?= $row['quantite'] ?></td>
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
    <title>Alizon</title>
    <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <main>
            <?php ecrire_nom($stmt); ?>
            <div>
                <a href="./nouveau_produit/"> Ajouter un produit</a>
            </div>
        </main>
    </body>
</html>



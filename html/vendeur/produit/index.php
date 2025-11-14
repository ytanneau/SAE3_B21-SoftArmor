<?php
//permet d'utiliser le fichier config.php
    define("HOME_GIT", "../../../");
    require_once HOME_GIT . '.config.php';
    require_once HOME_GIT . 'fonction_produit.php';

    if (!isset($_SESSION)) {
        session_start();
    }
    //verifie si quelqun est connecté
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] === false) {
        header('location: ../');
        exit;
    }
    function ecrire_nom($rows, $rows2, $produit){
        global $rows;
        global $rows2;
        global $produit;
        ?>
        <!-- tableau a mettre en haut a droite -->
        <table>
            <tr>
                <th>nom en stock </th>
                <td><?= $rows['nom_stock']?> </td>
            </tr>
                <th>nom public </th>
                <td><?= $rows['nom_public']?>  </td>
            </tr>
                <th>Prix actuelle </th>
                <td><?= $rows['prix']?>  </td>
            </tr>
                <th>taux TVA </th>
                <td><?= $rows['tva']?>  </td>
            </tr>
                <th>Poids </th>
                <td><?=$rows['poids']?> </td>
            </tr>
                <th>Volume </th>
                <td><?= $rows['volume'] ?></td>
            </tr>
        </table>
        <div>
            <?php
            if ($rows2['id_image_principale'] != NULL) {
                ?> <img src= "../ressource/produit/<?php $produit . `_` . $rows2['id_image_principale'] ?>" > <?php
            }
            if ($rows2['id_image1'] != NULL) {
                ?> <img src= "../ressource/produit/<?php $produit . `_` . $rows2['id_image1'] ?>" > <?php
            }
            if ($rows2['id_image2'] != NULL) {
                ?> <img src= "../ressource/produit/<?php $produit . `_` . $rows2['id_image2'] ?>" > <?php
            }
            ?>
            
        </div>
        <!-- div a mettre en dessous du tableau -->
        <div>
            <?= $rows['description'] ?>
        </div>
        <!-- A mettre encore en dessous -->
        <div>
            <?= $rows['description_detaillee'] ?>
        </div>
        <div>
            <table>
                <tr>
                    <td>
                        <?= $rows['quantite'] ?>
                    </td>
                </tr>
            </table>
        </div>

    <?php

    }
    //commande qui permet de séléctionner les caractéristiques du produit pour les réutiliser dans le document
    $rows = detail_produit($_GET['produit']);
    $rows2 = vendeur_image_produit($_GET['produit']);
    $sqlverif = vendeur_verif_produit($_GET['produit'], $_SESSION['id_compte']);
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
            <?php ecrire_nom($rows, $rows2, $_GET['produit']); ?>
        </main>
    </body>
</html>



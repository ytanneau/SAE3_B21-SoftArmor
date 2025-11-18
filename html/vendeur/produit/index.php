<?php
    // Permet d'utiliser le fichier .config.php
    define("HOME_GIT", "../../../");
    define("HOME_SITE", "../../");

    if (!isset($_SESSION)) {
        session_start();
    }
    function renvoi(){
        if (headers_sent()) {
            die('Échec de redirection. Cliquez sur ce lien svp : <a href="../">Ici</a>');
        }
        else{
            exit(header("Location: ../"));
        }
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

    require_once HOME_GIT . '.config.php';
    require_once HOME_GIT . 'fonction_produit.php';

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
            <img src="<?= HOME_SITE . 'ressources/produit/' . $produit . '_' . $rows2['id_image_principale'] . '.png' ?>" > 
            <?php
                if ($rows2['id_image1'] != NULL) {
                    ?> <img src="../ressources/produit/<?= $produit . '_' . $rows2['id_image1'] . '.png' ?>" > <?php
                }
                if ($rows2['id_image2'] != NULL) {
                    ?> <img src= "../ressources/produit/<?= $produit . '_' . $rows2['id_image2'] . '.png' ?>" > <?php
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

    if ($sqlverif == NULL) {
        renvoi();
    }

    // Si on a cliqué sur Supprimer et que le produit en paramètre GET existe bien
    if ($_POST != NULL && isset($rows)) {
        try {
            $supprime = supprimer_produit_stock($_GET['produit']);
        } catch (PDOException $e) {
            die('Suppression du produit ' . $_GET['produit'] . ' impossible');
        }
    }
?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Alizon</title>
        <link rel="stylesheet" href="style.css">
        <script src="confirmation.js"></script>
    </head>
    <body>
        <main>
            <?php if (!isset($supprime) || $supprime === false) {
                ecrire_nom($rows, $rows2, $_GET['produit']);
            } else { ?>
                <h1>Produit supprimé</h1>
                <a href="../stock">Revenir au stock</a>
            <?php } ?>
            
            <form id="supprimer" action="" method="post">
                <input type="submit" value="Supprimer">
            </form>
        </main>
    </body>
</html>



<?php
    // Permet d'utiliser le fichier .config.php
    define("HOME_GIT", "../../../");
    define("HOME_SITE", "../../");

    require_once HOME_GIT . '.config.php';
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

    //commande qui permet de séléctionner les caractéristiques du produit pour les réutiliser dans le document
    $rows = detail_produit($_GET['produit']);
    $rows2 = vendeur_image_produit($_GET['produit']);
    $sqlverif = vendeur_verif_produit($_GET['produit'], $_SESSION['id_compte']);
    $id_produit = $_GET['produit'];
    
    if ($sqlverif == NULL) {
        renvoi();
    }

    // Si on a cliqué sur Supprimer et que le produit en paramètre GET existe bien
    if ($_POST != NULL && isset($rows)) {
        try {
            supprimer_produit_stock($id_produit);
        } catch (PDOException $e) {
            die('Suppression du produit ' . $id_produit . ' impossible : ' . $e->getMessage());
        }

        header("Location: ../");
        exit();
    }
    
    $compteur = 0;
    $tab_promo = get_info_promotion($id_produit);
    foreach($tab_promo as $ligne){
        $compteur++;
    }
?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Alizon - <?= htmlentities($rows['nom_stock'] ?? 'Produit')?></title>
        <script src="confirmation.js"></script>
        <?php include HOME_SITE . 'link_head.php' ?>
    </head>
    <body>
        <?php include HOME_SITE . 'vendeur/header.php'; ?>
        <main class="produit-vendeur">
            <a href="../accueil"><img src="../../image/retour.svg" class = "fleche_produit_arriere"></a>
            <?php if (!isset($supprime) || $supprime === false) {?>
                <table>
                    <tr>
                        <th>Référence</th>
                        <td><?= htmlentities($rows['nom_stock'] ?? '')?> </td>
                    </tr>
                        <th>Nom public </th>
                        <td><?= htmlentities($rows['nom_public'] ?? '') ?>  </td>
                    </tr>
                        <th>Prix actuel </th>
                        <td><?= htmlentities($rows['prix'] ?? '') ?>  </td>
                    </tr>
                        <th>Taux TVA </th>
                        <td><?= htmlentities($rows['tva'] ?? '') ?>  </td>
                    </tr>
                        <th>Poids </th>
                        <td><?= htmlentities($rows['poids'] ?? '') ?> </td>
                    </tr>
                        <th>Volume </th>
                        <td><?= htmlentities($rows['volume'] ?? '')  ?></td>
                    </tr>
                    <tr>
                        <th>Quantité </th>
                        <td><?= htmlentities($rows['quantite'] ?? '')  ?></td>
                    </tr>
                </table>
                <div>
                    <img src="<?= HOME_SITE . 'ressources/produit/' . htmlentities($_GET['produit'] ?? '') . '_1.png' ?>" > 
                    <?php
                        if (isset($rows2['id_image1'])) {
                            ?><img src="<?= HOME_SITE . 'ressources/produit/' . htmlentities($_GET['produit'] ?? '') . '_2.png' ?>" > <?php
                        }
                        if (isset($rows2['id_image2'])) {
                            ?> <img src="<?= HOME_SITE . 'ressources/produit/' . htmlentities($_GET['produit'] ?? '') . '_3.png' ?>" > <?php
                        }
                    ?>
                </div>
                <!-- div a mettre en dessous du tableau -->
                <div>
                    <?= htmlentities($rows['description'] ?? '') ?>
                </div>
                <!-- A mettre encore en dessous -->
                <div>
                    <?= htmlentities($rows['description_detaillee'] ?? '') ?>
                </div>
                <div class="toolbar" id="produit">
                    <ul>
                        <li>
                            <form id="supprimer" action="" method="post">
                                <input type="hidden" name="supprimer" value="true">
                                <input type="submit" value="Supprimer le produit">
                            </form>
                        </li>
                        <li>
                            <a class="bouton_vendeur_produit" href="../stock/modifier_produit?produit=<?= htmlentities($_GET['produit'] ?? '')?>">Modifier ce produit</a>
                        </li>
                        <li>
                            <?php if($compteur === 2){ ?>
                                <button style="color:grey; width:510px;" class="bouton_vendeur_produit" disabled>Promotion/Reduction <br> (Maximum de deux promotions par vendeur)</button>
                            <?php } else {?>
                                <a class="bouton_vendeur_produit" href="promotion?produit=<?= htmlentities($_GET['produit'] ?? '')?>">Promotion/Reduction</a>
                            <?php } ?>
                        </li>
                            <?php if($tab_promo != null){
                                foreach($tab_promo as $ligne){
                                    $id_promo = $ligne['id_promo'];
                                    $date = $ligne['date_debut'];
                                    $temp_date = explode("-",$date);  
                                    $new_date = $temp_date[2] . "/" . $temp_date[1] . "/" . $temp_date[0];
                                    ?>
                        <li>
                            <a 
                                class="bouton_vendeur_produit" 
                                href="modifier_promotion?produit=<?= htmlentities($_GET['produit'] . "&idPromo=" . $id_promo)?>">
                                Modifier la promotion du <?= htmlentities($new_date)?>
                            </a>
                        </li>
                            <?php }
                                } 
                            ?>
                        <li>
                            <a class="bouton_avis_vendeur_produit" href="../avis?produit=<?= htmlentities($_GET['produit'] ?? '')?>">Voir les avis</a>
                        </li>
                    </ul>
                </div>
                <?php 
            } ?>
            
        </main>
        <?php include HOME_SITE . "footer.php" ?>
    </body>
</html>



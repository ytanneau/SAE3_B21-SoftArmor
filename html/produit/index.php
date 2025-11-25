<?php
// Inclusion du fichier de configuration
define('HOME_GIT', '../../');
define('HOME_SITE', '../');

if (!isset($_SESSION)) {
    session_start();

    if(isset($_SESSION['raison_sociale'])){
        header('location: /vendeur/stock/');
    }
}

require_once (HOME_GIT . '.config.php');
require_once (HOME_GIT . 'fonction_avis.php');
require_once (HOME_GIT . 'fonction_produit.php');
require_once (HOME_GIT . 'fonction_global.php');
require_once (HOME_GIT . 'fonction_panier.php');

if (!isset($_GET['produit']) || !is_numeric($_GET['produit'])) {
    die("ID du produit invalide.");
}

$id_produit = htmlentities($_GET['produit']);

try {
    $produit = detail_produit_image($id_produit);
    $note = note_produit($id_produit)['note_moy'];

    if (!$produit) {
        die("Produit introuvable.");
    }

    // Récupérer les avis
    $liste_avis = avis_client_produit($_GET['produit']);
} catch (PDOException $e) {
    die("Erreur lors de la récupération du produit : " . $e->getMessage());
}

// Preparer le prix formaté
$formatted_prix_ht = '';
$formatted_prix_ttc = '';

if (isset($produit['prix'])) {
    if (is_numeric($produit['prix'])) {
        $formatted_prix_ht = number_format($produit['prix'], 2, ',', ' ') . ' €';
        $formatted_prix_ttc = number_format($produit['prix'] * (1 + $produit['tva'] / 100), 2, ',', ' ') . ' €';
    } else {
        $formatted_prix_ht = htmlentities($produit['prix'] ?? '');
        $formatted_prix_ttc = $formatted_prix_ht;
    }
}

if ($_POST != NULL) {
    $qte= $_POST['quantite'];
    $id_prod = $_GET['produit'];
    $id_cli = $_SESSION['id_compte'];
    ajouter_panier($id_prod,$id_cli,$qte);
}

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <?php include HOME_SITE . "link_head.php"; ?>
    <title><?= htmlentities($produit['nom_public'] ?? 'Produit') ?></title>
</head>
<body>
    <?php include HOME_SITE . "header.php"; ?>

    <script>
        function changer(val) {
            const inputQuantite = document.getElementById('input_quantite');
            let value = Number(inputQuantite.value);
            if (value + val > 0 && value + val < 50000) {
                inputQuantite.stepUp(val);
            }
        }
    </script>

    <?php
        $img_principale_url   = htmlentities($produit['image_principale_url'] ?? '');
        $img_principale_title = htmlentities($produit['image_principale_titre'] ?? ($produit['nom_public'] ?? ''));
        $img_principale_alt   = htmlentities($produit['image_principale_alt'] ?? ($produit['nom_public'] ?? ''));

        $img1_url   = htmlentities($produit['image_1_url'] ?? '');
        $img1_title = htmlentities($produit['image_1_titre'] ?? ($produit['nom_public'] ?? ''));
        $img1_alt   = htmlentities($produit['image_1_alt'] ?? ($produit['nom_public'] ?? ''));

        $img2_url   = htmlentities($produit['image_2_url'] ?? '');
        $img2_title = htmlentities($produit['image_2_titre'] ?? ($produit['nom_public'] ?? ''));
        $img2_alt   = htmlentities($produit['image_2_alt'] ?? ($produit['nom_public'] ?? ''));
    ?>
    
    <main>
        <div class="gauche">
            <a href="../"><img src="../image/retour.svg"></a>

            <section class="detail_produit">
                <!-- Présentation du produit -->
                <article>
                    <div>
                        <!-- Image principale -->
                        <div>
                            <img src="<?= HOME_SITE . $img_principale_url ?>" title="<?= $img_principale_title ?>" alt="<?= $img_principale_alt ?>">
                        </div>

                        <!-- Images facultatives -->
                        <div>
                            <?php 
                                if (!empty($img1_url)) { ?>
                                    <img src="<?= HOME_SITE . $img1_url ?>" title="<?= $img1_title ?>" alt="<?= $img1_alt ?>">
                                <?php }

                                if (!empty($img2_url)) { ?>
                                    <img src="<?= HOME_SITE . $img2_url ?>" title="<?= $img2_title ?>" alt="<?= $img2_alt ?>">
                                <?php } 
                            ?>
                        </div>
                    </div>
                    
                    <!-- Détails du produit -->
                    <div>
                        <h1><?= htmlentities($produit['nom_public'] ?? '') ?></h1>

                        <p>
                            <em>par <?= htmlentities($produit['nom_vendeur'] ?? '') ?></em>
                        </p>

                        <div>
                            <div class="etoiles">
                                <?php if (count($liste_avis) > 0) {
                                    afficher_moyenne_note($note);
                                } else {
                                    echo 'Produit non noté';
                                } ?>
                            </div>
                            
                            <!-- Nombre d'avis -->
                            <a href="#avis"><?= count($liste_avis) > 0 ? count($liste_avis) . ' avis' : '' ?></a>
                        </div>
                        
                        <!-- Description du produit -->
                        <p>
                            <?= nl2br(htmlentities($produit['description'] ?? '')) ?>
                        </p>
                    </div>
                </article>


                <!-- Description détaillée -->
                <article>
                    <?php
                        $description_detaillee = nl2br(htmlentities($produit['description_detaillee'] ?? ''));

                        if (!empty($description_detaillee)) { ?>
                            <h2>Description détaillée</h2>
                            <p>
                                <?= $description_detaillee ?>
                            </p>
                        <?php } 
                    ?>
                </article>

                <hr>
            </section>

            <!-- Section des avis -->
            <section id="avis">
                <!-- Rajouter le nombre -->
                <h2>Avis (<?= count($liste_avis) ?>)</h2>

                <a class="bouton" href="../avis/index.php?produit=<?= urlencode($produit['id_produit']) ?>">Ajouter un avis</a>

                <ul class="liste_avis">
                    <?php foreach ($liste_avis as $avis) { ?>
                        <li>
                            <div>
                                <div>
                                    <?php if (isset($avis['profile'])) {?>
                                        <img height="40px" width="40px" src="../ressources/27_1.png">
                                    <?php
                                        } else {?>
                                        <img height="40px" width="40px" src="<?=HOME_SITE . 'image/compte.svg'?>">
                                    <?php } ?>

                                    <div class="etoiles">
                                        <?= afficher_moyenne_note(htmlentities($avis['note'] ?? '')) ?>
                                    </div>
                                </div>

                                <div>
                                    <h3><?= htmlentities($avis['titre'] ?? '') ?></h3>
                                    <p><?= htmlentities($avis['commentaire'] ?? '') ?></p>
                                    <p><?= 'Avis rédigé par ' . htmlentities($avis['pseudo'] ?? '') .  ' le ' . date('d/m/Y', strtotime(htmlentities($avis['date_avis'] ?? ''))) ?></p>
                                </div>
                            </div>

                            <?php if (isset($avis['url_image'])) { ?>
                                <img src="<?= HOME_SITE . $avis['url_image'] ?>" title="<?= $avis['alt_image'] ?>" alt="<?= $avis['alt_image'] ?>">
                            <?php } ?>
                        </li>
                    <?php } ?>
                </ul>
            </section>
        </div>
                    
        <!-- Achat du produit  -->

        <?php if (isset($_SESSION['logged_in'])) {
            $page = "../achat";
        } else {
            $page = HOME_SITE . "compte/inscription";
        } ?>
        
        <div>
            <aside>
                <div>
                    <span>Prix HT</span> 
                    <span class="prix">
                        <?= $formatted_prix_ht ?>
                    </span>
                </div>

                <div>
                    <span>Prix TTC</span> 
                    <span class="prix">
                        <?= $formatted_prix_ttc ?>
                    </span>
                </div>

                <form action="" method="post">
                    <div>
                        <?php if (isset($_SESSION['logged_in'])) { ?>
                            <label for="quantite">Quantité</label>
                
                            <span class="input_quantite">
                                <input type="button" onclick="changer(-1)" value="-"><input id="input_quantite" type="number" name="quantite" min=1 value=1 max=50000 pattern="\d*" required><input type="button" onclick="changer(1)" value="+">
                            </span>
                        <?php } ?>
                    </div> 
                    
                    <?php if (isset($_SESSION['logged_in'])) { ?>
                        <input class="bouton" type="submit" value="Ajouter au panier">
                    <?php } else { ?>
                        <p>Connectez-vous pour ajouter ce produit à votre panier</p>
                    <?php } ?>

                    <a class="bouton" href="<?=$page?>/index.php?produit=<?= urlencode($produit['id_produit']) ?>">Acheter</a>
                </form>
            </aside>
        </div>
        
    </main>
        <?php include HOME_SITE . "footer.php" ?>
</body>
</html>

<?php
    define('HOME_GIT', '../../../');
    define('HOME_SITE', '../../');

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

    function pset($value) {
        return isset($value) ? htmlentities($value) : "";
    }

    require_once HOME_GIT . 'fonction_produit.php';
    require_once HOME_GIT . 'fonction_avis.php';

    //verifie si quelqun est connecté


    if (isset($_GET['produit'])) {
        $data = avis_client_produit($_GET['produit']);
        $produit = avis_produit($_GET['produit']);
        //print_r($produit);
    }
    else{
        $data = NULL;
    }

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alizon - avis</title>
<?php
    require_once HOME_SITE . 'link_head.php';
?>
</head>

<body id="avis_vendeur">
<?php
    require_once HOME_SITE . 'vendeur/header.php';
?>
    <main>
    <a href="../stock"><img src="../../image/retour.svg" class = "fleche_produit_arriere"></a>
<?php
    if ($produit == NULL ){
?>
        <h1>Désoler se produit existe pas</h1>
<?php        
    }
    else{
        //print_r($data);
?>
    <article>
        <div>
            <img src="<?=HOME_SITE . htmlentities($produit['image_principale_url'])?>" alt="<?=htmlentities($produit['image_principale_alt'])?>" title="<?=htmlentities($produit['image_principale_titre'])?>">
        </div>
        <div>
            <?=htmlentities($produit['nom_public'])?>
<?php
            if ($data != null) {
?>
            <br>Note moyenne : <?=htmlentities(round($produit['note_moy'] ?? 0, 1))?></br>
<?php
            afficher_moyenne_note($produit['note_moy']);

            } else {
                echo "<br>Il n'y a pas d'avis pour ce produit";
            }
?>
            <br>Nombre d'avis : <?=htmlentities($produit['nb_avis'] ?? "0")?>
        </div>
    </article>
    <section>
<?php
    if ($data != null){
?>
            <ul class="liste_avis">
                <?php foreach ($data as $avis) { ?>
                    <li>
                        <div>
                            <div>
                                <?php if (isset($avis['profile'])) {?>
                                    <img height="40px" width="40px" src="../ressources/27_1.png">
                                <?php
                                    } else {?>
                                    <img height="40px" width="40px" src="<?=HOME_SITE . 'image/compte.svg'?>">
                                <?php } 
                                ?>
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
                            <a href="<?=HOME_SITE . $avis['url_image']?>" target="_blank">
                                <img src="<?= HOME_SITE . $avis['url_image'] ?>" title="<?= $avis['alt_image'] ?>" alt="<?= $avis['alt_image'] ?>">
                            </a>
                        <?php } ?>
                    </li>

                <?php } ?>
            </ul>
<?php 
    }
?>
    </section>
<?php
    }
?>
    </main>
    <?php include HOME_SITE . "footer.php" ?>
</body>
</html>
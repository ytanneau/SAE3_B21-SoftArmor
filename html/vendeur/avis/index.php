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

    $id_produit = htmlentities(trim($_GET['produit'] ?? ''));

    // Rediriger à la page de stock
    if (empty($id_produit)) {
        header('location: ../stock');
        exit;
    }

    require_once HOME_GIT . 'fonction_produit.php';
    require_once HOME_GIT . 'fonction_avis.php';


    $data = avis_client_produit($_GET['produit']);
    $produit = detail_produit($_GET['produit']);

    $image_principale = get_url_image($produit['id_image_principale']);
    $image1 = get_url_image($produit['id_image1']);
    $image2 = get_url_image($produit['id_image2']);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alizon - Avis</title>
<?php
    require_once HOME_SITE . 'link_head.php';
?>
</head>

<body id="avis_vendeur">
    <?php require_once HOME_SITE . 'vendeur/header.php'; ?>
    
    <main>

    <a href="../stock"><img src="../../image/retour.svg" class = "fleche_produit_arriere"></a>

    <?php if ($produit == NULL) { ?>
        <h1>Désolé, ce produit n'existe pas</h1>
    <?php } ?>

    <article>
        <div>
            <img src="<?=HOME_SITE . htmlentities($image_principale['url'])?>" alt="<?=htmlentities($image_principale['alt'])?>" title="<?=htmlentities($image_principale['titre'])?>">
        </div>
        <div>
            <?=htmlentities($produit['nom_public'])?>
            
            <?php if ($data != null) { ?>
                <br>Note moyenne : <?=htmlentities(round($produit['note_moy'] ?? 0, 1))?></br>
                <?php afficher_moyenne_note($produit['note_moy']);
            } else {
                echo "<br>Il n'y a pas d'avis pour ce produit";
            } ?>

            <br>Nombre d'avis : <?=htmlentities($produit['nb_avis'] ?? "0")?>
        </div>
    </article>
    <section>
        <?php if ($data != null) { ?>
            <ul class="liste_avis">
                <?php foreach ($data as $avis) { 
                    $image_pp = get_url_image($avis['id_image_pp']);

                    if (!$image_pp) {
                        $image_pp = [
                            "url" => "image/compte.svg",
                            "titre" => "Photo de profil",
                            "alt" => "Photo de profil"
                        ];
                    }
                    ?>
                    <li>
                        <div>
                            <div>
                                <img class="image_pp" src="<?= HOME_SITE . $image_pp['url'] ?>" title="<?= $image_pp['titre'] ?>" alt="<?= $image_pp['alt'] ?>">
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
                                <img src="<?= HOME_SITE . $avis['url_image'] ?>" title="<?= $avis['titre_image'] ?>" alt="<?= $avis['alt_image'] ?>">
                            </a>
                        <?php } ?>
                    </li>
                <?php } ?>
            </ul>
        <?php } ?>
    </section>

    </main>

    <?php include HOME_SITE . "footer.php" ?>
</body>
</html>
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
    <title>Alizon avis</title>
<?php
    require_once HOME_SITE . 'link_head.php';
?>
</head>

<body id="avis_vendeur">
<?php
    require_once HOME_SITE . 'vendeur/header.php';
?>
    <main>
<?php
    if ($data === NULL){
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
            <br>Note moyenne : <?=htmlentities($produit['note_moy'])?>
            <br>
<?php
    afficher_moyenne_note($produit['note_moy']);
?>
            <br>Nombre d'avis : <?=htmlentities($produit['nb_avis'])?>
        </div>
    </article>
    <section>
        <ul>
<?php
        foreach($data as $row){
?>
            <li>
                <div>
                    <?=pset($row['pseudo'])?>
                    <br><div>
<?php
                    afficher_moyenne_note($row['note']);
?>
                    </div><br><?=pset($row['titre'])?>
                    <p><?=pset($row['commentaire'])?></p>
                    <?=pset($row['date_avis'])?>
                </div>
<?php
    if (isset($row['url_image'])){
?>
                <img src="<?=HOME_SITE . pset($row['url_image'])?>" alt="<?=pset($row['alt_image'])?>" tilte="<?=pset($row['titre_image'])?>">
<?php
    }
?>
            </li>
<?php
        }
?>
        </ul>
    </section>
<?php
    }
?>
    </main>
</body>
</html>
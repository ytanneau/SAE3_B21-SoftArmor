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

if (!isset($_GET['id_produit']) || !is_numeric($_GET['id_produit'])) {
    die("ID du produit invalide.");
}

$id_produit = htmlentities($_GET['id_produit']);

try {
    $produit = detail_produit_image($id_produit);

    if (!$produit) {
        die("Produit introuvable.");
    }

    // Récupérer les avis
    $liste_avis = avis_client_produit($_GET['id_produit']);
} catch (PDOException $e) {
    die("Erreur lors de la récupération du produit : " . $e->getMessage());
}

// Preparer le prix formaté
$formatted_prix = '';

if (isset($produit['prix'])) {
    if (is_numeric($produit['prix'])) {
        $formatted_prix = number_format($produit['prix'], 2, ',', ' ') . ' €';
    } else {
        $formatted_prix = htmlentities($produit['prix'] ?? '');
    }
}

if($_POST!=NULL){
    $qte= $_POST['quantite'];
    $id_prod = $_GET['id_produit'];
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

    <a href="../"><p>Revenir au catalogue</p></a>

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

    <img height="200px" src="<?= HOME_SITE . $img_principale_url ?>" title="<?= $img_principale_title ?>" alt="<?= $img_principale_alt ?>">
    
    <!-- Afficher les images facultatives -->
    <?php if (!empty($img1_url)) { ?>
        <img height="200px" src="<?= HOME_SITE . $img1_url ?>" title="<?= $img1_title ?>" alt="<?= $img1_alt ?>">
    <?php } ?>

    <?php if (!empty($img2_url)) { ?>
        <img height="200px" src="<?= HOME_SITE . $img2_url ?>" title="<?= $img2_title ?>" alt="<?= $img2_alt ?>">
    <?php } ?>
    
        
    
    <h1><?= htmlentities($produit['nom_public'] ?? '') ?></h1>

    <p>
        <strong>Vendeur :</strong>
        <?= htmlentities($produit['id_vendeur'] ?? '') ?>
    </p>

    <p>
        <strong>Description :</strong>
        <?= nl2br(htmlentities($produit['description'] ?? '')) ?>
    </p>

    <p>
        <strong>Prix :</strong>
        <?= $formatted_prix ?> (TVA <?= htmlentities($produit['tva'] ?? '') ?>%)
    </p>

    <p>
        <strong>Détails :</strong>
        <?= nl2br(htmlentities($produit['description_detaillee'] ?? '')) ?>
    </p>
    
    <!-- Affichage des avis -->
    <h3>Avis</h3>

    <ul>
        <?php foreach ($liste_avis as $avis) { ?>
            <li>
                <p><?= htmlentities($avis['titre'] ?? '') ?></p>
                <p><?= afficher_moyenne_note(htmlentities($avis['note'] ?? ''))?></p>
                <p><?= htmlentities($avis['titre'] ?? '') ?></p>
                <p><?= htmlentities($avis['commentaire'] ?? '') ?></p>
                <p><?= 'Avis rédigé par ' . htmlentities($avis['pseudo'] ?? '') .  ' le ' . date('d/m/Y', strtotime(htmlentities($avis['date_avis'] ?? ''))) ?></p>
            </li>
        <?php } ?>
    </ul>
    

    <a href="../achat/index.php?produit=<?= urlencode($produit['id_produit']) ?>"><p>Acheter</p></a>
    
    <form action="" method="post">
        <label for="quantite">Quantité</label>
        <input type="number" name="quantite" min=1 value=1 pattern="\d*" required>
        <button type="submit">Ajouter au Panier</button>
    </form>

</body>
</html>

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

require_once(HOME_GIT . '.config.php');
require_once(HOME_GIT . 'fonction_avis.php');
require_once(HOME_GIT . 'fonction_global.php');

if (!isset($_GET['id_produit']) || !is_numeric($_GET['id_produit'])) {
    die("ID du produit invalide.");
}

$id_produit = (int) $_GET['id_produit'];

// Requête SQL pour récupérer les informations du produit avec les images et le vendeur
$sql = "
SELECT
    p.*,
    v.raison_sociale AS id_vendeur,
    ip.id_image_principale,
    i_principale.url_image AS image_principale_url,
    i_principale.titre AS image_principale_titre,
    i_principale.alt AS image_principale_alt,
    i1.url_image AS image_1_url,
    i1.titre AS image_1_titre,
    i1.alt AS image_1_alt,
    i2.url_image AS image_2_url,
    i2.titre AS image_2_titre,
    i2.alt AS image_2_alt
FROM _produit p
JOIN _vendeur v ON p.id_vendeur = v.id_compte
LEFT JOIN _images_produit ip ON p.id_produit = ip.id_produit
LEFT JOIN _image i_principale ON ip.id_image_principale = i_principale.id_image
LEFT JOIN _image i1 ON ip.id_image1 = i1.id_image
LEFT JOIN _image i2 ON ip.id_image2 = i2.id_image
WHERE p.id_produit = :id_produit
";

// au cas ou j'en ai besoin
// $query= "SELECT produit_visible.id_produit,nom_public,prix,url_image,alt,_image.titre,note_moy AS moyenne FROM produit_visible INNER JOIN _images_produit ON produit_visible.id_produit = _images_produit.id_produit INNER JOIN _image ON _images_produit.id_image_principale = _image.id_image INNER JOIN produit_note ON produit_note.id_produit = produit_visible.id_produit WHERE produit_note.id_produit = produit_visible.id_produit;";
// $produit_img = $pdo->query($query);

try {
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_produit', $id_produit, PDO::PARAM_INT);
    $stmt->execute();
    $produit = $stmt->fetch(PDO::FETCH_ASSOC);

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
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlentities($produit['nom_public'] ?? 'Produit') ?></title>
</head>
<body>
    <a href="../"><p>Revenir au catalogue</p></a>

    <?php
        $img1_url   = $produit['image_principale_url'] ?? '';
        $img1_title = $produit['image_principale_titre'] ?? ($produit['nom_public'] ?? '');
        $img1_alt   = $produit['image_principale_alt'] ?? ($produit['nom_public'] ?? '');

        $img2_url   = $produit['image_1_url'] ?? '';
        $img2_title = $produit['image_1_titre'] ?? ($produit['nom_public'] ?? '');
        $img2_alt   = $produit['image_1_alt'] ?? ($produit['nom_public'] ?? '');

        $img3_url   = $produit['image_2_url'] ?? '';
        $img3_title = $produit['image_2_titre'] ?? ($produit['nom_public'] ?? '');
        $img3_alt   = $produit['image_2_alt'] ?? ($produit['nom_public'] ?? '');
    ?>

    <?php if ($img1_url !== ''): ?>
        <img src="<?= htmlentities($img1_url) ?>" title="<?= htmlentities($img1_title ?? '') ?>" alt="<?= htmlentities($img1_alt ?? '') ?>">
    <?php endif; ?>

    <?php if ($img2_url !== ''): ?>
        <img src="<?= htmlentities($img2_url) ?>" title="<?= htmlentities($img2_title ?? '') ?>" alt="<?= htmlentities($img2_alt ?? '') ?>">
    <?php endif; ?>

    <?php if ($img3_url !== ''): ?>
        <img src="<?= htmlentities($img3_url) ?>" title="<?= htmlentities($img3_title ?? '') ?>" alt="<?= htmlentities($img3_alt ?? '') ?>">
    <?php endif; ?>

    <h1><?= htmlentities($produit['nom_public'] ?? '') ?></h1>

    <?php if (!empty($produit['id_vendeur'])): ?>
        <p><strong>Vendeur :</strong> <?= htmlentities($produit['id_vendeur'] ?? '') ?></p>
    <?php endif; ?>

    <?php if (!empty($produit['description'])): ?>
        <p><strong>Description :</strong><br><?= nl2br(htmlentities($produit['description'] ?? '')) ?></p>
    <?php endif; ?>

    <?php if ($formatted_prix !== ''): ?>
        <p><strong>Prix :</strong> <?= $formatted_prix ?> (TVA <?= htmlentities($produit['tva'] ?? '') ?>%)</p>
    <?php endif; ?>

    <?php if (!empty($produit['description_detaillee'])): ?>
        <p><strong>Détails :</strong><br><?= nl2br(htmlentities($produit['description_detaillee'] ?? '')) ?></p>
    <?php endif; ?>
    
    <!-- Affichage des avis -->
    <ul>
        <?php foreach ($liste_avis as $avis) { ?>
            <li>
                <p><?= htmlentities($avis['pseudo'] ?? '') ?></p>
                <p><?= htmlentities($avis['note'] ?? '') . 'étoile(s)' ?></p>
                <p><?= htmlentities($avis['titre'] ?? '') ?></p>
                <p><?= htmlentities($avis['commentaire'] ?? '') ?></p>
                <p><?= 'Avis rédigé le ' . date('d/m/Y', strtotime(htmlentities($avis['date_avis'] ?? ''))) ?></p>
            </li>
        <?php } ?>
    </ul>
    

    <a href="../produit/achat/index.php?produit=<?= urlencode($produit['id_produit']) ?>"><p>Acheter</p></a>

</body>
</html>

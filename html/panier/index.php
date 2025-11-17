<?php
// Constantes
define('HOME_GIT', "../../");
define('HOME_SITE', '../');

// Redirige les utilisateurs non connectés et les vendeurs
if (!isset($_SESSION)) {
    session_start();

    if(isset($_SESSION['raison_sociale'])){
        header('location: ' . HOME_GIT . 'vendeur/stock/');
        exit;
    }

    if (!isset($_SESSION['logged_in'])) {
        header('location: ' . HOME_SITE);
        exit;
    }

    $id_client = $_SESSION['id_compte'];
}

require_once (HOME_GIT . '.config.php');
require_once (HOME_GIT . 'fonction_produit.php');

// Récupération des éléments du panier
$sql = "SELECT * FROM produit_panier WHERE id_client = :id_client";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_client', $id_client, PDO::PARAM_INT);
    $stmt->execute();
    $elts_panier = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors de la récupération du panier : " . $e->getMessage());
}

//supprime le produit selectionné
if($_POST!=NULL){
    $id_prod = $_POST['id_produit'];
    supprimer_produit_panier($id_prod,$id_client);
    header('Refresh:0');
}   
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <?php include HOME_SITE . 'link_head.php' ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon panier</title>
</head>
<body>
    <?php include HOME_SITE . 'header.php' ?>

    <h1>Mon panier</h1>

    <?php if (!$elts_panier) { ?>
        <h2>Votre panier est vide.</h2>
    <?php } else { ?>

    <ul>
        <?php 
            $total_ht = 0;
            $total_ttc = 0;

            foreach ($elts_panier as $elt) { ?>
                <?php 
                    $prix_ttc =  $elt['prix'] * (1 + $elt['tva'] / 100);

                    $total_ht += $elt['prix'] * $elt['quantite_panier'];
                    $total_ttc += $prix_ttc * $elt['quantite_panier'];
                ?>

                <li>
                    <img height="200px" src="<?= HOME_SITE . $elt['image_principale_url'] ?>" title="<?= $elt['image_principale_titre'] ?>" alt="<?= $elt['image_principale_alt'] ?>">
                    <p><?= $elt['nom_public'] ?></p>
                    <p><?= $elt['nom_vendeur'] ?></p>
                    <p><?= $elt['description'] ?></p>
                    <p><?= 'Prix HT : ' . number_format($elt['prix'], 2, ',', ' ') . ' €' ?></p>
                    <p><?= 'Prix TTC : ' . number_format($prix_ttc, 2, ',', ' ') . ' €' ?></p>
                    <p><?= 'Quantité : ' . $elt['quantite_panier'] ?></p>

                    <form action="" method="post">
                        <input type="hidden" name="id_produit" value="<?= $elt['id_produit'] ?>">
                        <button type="submit">Supprimer</button>
                    </form>
                </li>
            <?php } 
        ?>
    </ul>

    <p><strong>Prix total HT :</strong> <?= number_format($total_ht, 2, ',', ' ') . ' €'; ?></p>
    <p><strong>Prix total TTC :</strong> <?= number_format($total_ttc, 2, ',', ' ') . ' €'; ?> </p>

    <!-- bouton de paiement -->
    <form action="../achat" method="post">
        <input type="text" name="panier" id="panier" value="1">
        <input type="submit" value="Passer au paiement">
    </form>

    <?php } ?>
</body>
</html>
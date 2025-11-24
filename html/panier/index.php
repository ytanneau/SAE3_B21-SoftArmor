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

//supprime le produit selectionné
if ($_POST != NULL) {
    $id_prod = $_POST['id_produit'];
    supprimer_produit_panier($id_prod,$id_client);
}

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

 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <?php include HOME_SITE . 'link_head.php' ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon panier</title>
</head>
<body class="liste">
    <?php include HOME_SITE . 'header.php' ?>

    <main>
        <div class="gauche">
            <article class="entete">
                <h1>Mon panier</h1>
            </article>

            <?php if (!$elts_panier) { ?>
                <div id="panier_vide">
                    <img src="<?=HOME_SITE?>image/panier_vide.svg">
                    <h2>Votre panier est vide.</h2>
                </div>
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
        
                        <li class="produit_panier">
                            <img class="image_produit_moyen" src="<?= HOME_SITE . $elt['image_principale_url'] ?>" title="<?= $elt['image_principale_titre'] ?>" alt="<?= $elt['image_principale_alt'] ?>">
                            <article>
                                <h3><?= $elt['nom_public'] ?></h3>
                                <p><?= $elt['description'] ?></p>
                                <p>Vendeur : <?= $elt['nom_vendeur'] ?></p>
                            </article>
                            <article>
                                <p class="prix"><?=number_format($elt['prix'], 2, ',', ' ') . ' €' ?></p>
                                <p><?= 'Quantité : ' . $elt['quantite_panier'] ?></p>
        
                                <form action="" method="post">
                                    <input type="hidden" name="id_produit" value="<?= $elt['id_produit'] ?>">
                                    <button onclick="actualiser()" type="submit" class="bouton">Supprimer</button>
                                </form>
                            </article>
                        </li>
        
                        <hr>
                    <?php } 
                ?>
            </ul>
        </div>
        
        <!-- bouton de paiement -->
        <div>
            <aside>
                <?php if ($elts_panier) { ?>
                    <div>
                        <span>Total HT</span>
                        <span class="prix HT"><?= number_format($total_ht, 2, ',', ' ') . ' €'; ?></span>
                    </div>
                    <div>
                        <span>Total TTC</span>
                        <span class="prix"><?= number_format($total_ttc, 2, ',', ' ') . ' €'; ?></span>
                    </div>
                <?php } ?>
                
                <form action="../achat" method="get">
                    <input type="hidden" name="id_produit" id="id_produit" value="panier">
                    <input type="submit" value="Passer au paiement" class="bouton">
                </form>
            </aside>
        </div>

        <?php } ?>

    </main>
</body>

    <script>
        function actualiser() {
            window.location.reload();
        }
    </script>
</html>
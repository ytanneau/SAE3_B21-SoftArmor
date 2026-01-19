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

    if (isset($_SESSION) && isset($_SESSION['id_compte'])) {
        $id_client = $_SESSION['id_compte'];
    }
}

require_once (HOME_GIT . '.config.php');
require_once (HOME_GIT . 'fonction_produit.php');
require_once (HOME_GIT . 'fonction_panier.php');

//supprime le produit selectionné
if ($_POST != NULL) {
    $id_prod = $_POST['id_produit'];

    if (isset($_SESSION['id_compte'])) {
        supprimer_produit_panier($id_prod,$id_client);

    } else {
        retirer_panier_visiteur($id_prod);
    }
}

if (isset($_SESSION['logged_in'])) {
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
} elseif (isset($_COOKIE['panier'])) {
    $ids_panier = unserialize($_COOKIE['panier']);
    $elts_panier = [];

    foreach($ids_panier as $elt_panier) {
        $produit = detail_produit($elt_panier['id_produit']);
        $produit['quantite_panier'] = $elt_panier['quantite'];
        array_push($elts_panier, $produit);
    }

} else {
    $elts_panier = [];
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <?php include HOME_SITE . 'link_head.php' ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alizon - Mon panier</title>

    <script>
        function change(n, id) {
            let valeur = parseInt(document.getElementById("input_quantite" + id).value) + parseInt(n);

            document.location.href = "update_quantite.php?produit=" + id + "&nb=" + valeur;
        }
    </script>
</head>
<body class="liste">
    <?php include HOME_SITE . 'header.php' ?>

    <main>

    <?php if (!$elts_panier) { ?>
        <div id="panier_vide">
            <img src="<?=HOME_SITE?>image/panier_vide.svg">
            <h2>Votre panier est vide.</h2>
            <a href="../"><h2>Revenir au catalogue</h2></a>
        </div>
    <?php } else { ?>

        <div class="gauche">
            <article class="entete">
                <h1>Mon panier</h1>
            </article>

            <ul>
                <?php 
                    $total_ht = 0;
                    $total_ttc = 0;
        
                    foreach ($elts_panier as $elt) {                            
                            $prix_ttc =  $elt['prix_actuel'] * (1 + $elt['tva'] / 100);
        
                            $total_ht += $elt['prix_actuel'] * $elt['quantite_panier'];
                            $total_ttc += $prix_ttc * $elt['quantite_panier'];

                            // Récupération des images
                            $image_p = get_url_image($elt['id_image_principale']);
                        ?>
        
                        <li class="produit_panier">
                            <a href="../produit/?produit=<?=$elt['id_produit']?>"><img class="image_produit_moyen" src="<?= HOME_SITE . $image_p['url'] ?>" title="<?= $image_p['titre'] ?>" alt="<?= $image_p['alt'] ?>"></a>
                            <div>
                                <article>
                                    <div>
                                        <a href="../produit/?produit=<?=$elt['id_produit']?>"><h3><?= $elt['nom_public'] ?></h3></a>
                                        <p><?= $elt['description'] ?></p>
                                        <p>Vendeur : <?= $elt['raison_sociale'] ?></p>
                                    </div>

                                    <form action="" method="post"> <!-- Bouton poubelle à droite pour format tel -->
                                        <input type="hidden" name="id_produit" value="<?= $elt['id_produit'] ?>">
                                        <button onclick="actualiser()" type="submit"><img class="icon" src="<?=HOME_SITE?>image/supprimer_blanc.svg"></button>
                                    </form> 
                                </article>
                                <article>
                                    <p class="prix"><?=number_format($prix_ttc, 2, ',', ' ') . ' €' ?></p>
                                    
                                    <form action="update_quantite.php">

                                        <div>
                                            <label for="nb">Quantité</label>
                                            <input type="hidden" name="produit" value="<?=$elt['id_produit']?>">
                                                <span class="input_quantite">
                                                    <input type="button" value="-" onclick="change(-1, <?=$elt['id_produit']?>)"><input id="input_quantite<?=$elt['id_produit']?>" type="text" size="4" name="nb" value=<?=$elt['quantite_panier']?> required><input type="button" value="+" onclick="change('+1', <?=$elt['id_produit']?>)">
                                                </span>
                                        </div> 
                                    </form>

                                    <p class="prix">Sous total : <?=number_format($prix_ttc * $elt['quantite_panier'], 2, ',', ' ')?> €</p>
            
                                    <form action="" method="post">
                                        <input type="hidden" name="id_produit" value="<?= $elt['id_produit'] ?>">
                                        <button onclick="actualiser()" type="submit" class="bouton grave">Supprimer</button>
                                    </form>
                                </article>
                            </div>
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
                
                <form action="<?= isset($_SESSION['id_compte']) ? '../achat' : '../compte/connexion'?>" method="get">
                    <input type="hidden" name="produit" id="produit" value="panier">
                    <input type="submit" value="Passer au paiement" class="bouton">
                </form>
            </aside>
        </div>

        <?php } ?>

    </main>
</body>
    <?php include HOME_SITE . "footer.php" ?>
    <script>
        function actualiser() {
            window.location.reload();
        }
    </script>
</html>
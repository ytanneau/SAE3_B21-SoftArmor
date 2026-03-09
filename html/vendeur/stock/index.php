<?php

define("HOME_GIT", "../../../");
define("HOME_SITE", "../../");

if (!isset($_SESSION)) {
    session_start();
}

// Si je suis connecté mais pas en tant que vendeur, retour à l'accueil client
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && !isset($_SESSION['raison_sociale'])) {
    header('location: ' . HOME_SITE);
    exit;
}
// Sinon si je ne suis pas connecté, retour à la page connexion vendeur
else if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] === false) {
    header('location: ../');
    exit;
}

//permet d'utiliser le fichier config.php
require_once HOME_GIT . '.config.php';
require_once HOME_GIT . 'fonction_produit.php';

function ecrire_nom($rows){
    ?>
        <table>
    <?php
    if (empty($rows)) {
    ?>
    <h1>Vous n'avez pas de produit</h1>
    <?php
    } else {
        foreach ($rows as $row){
        ?>
        
            <tr id=<?= htmlentities($row['id_produit'] ?? '')?>>
                <!-- <td><img src="MenuBurger.png" alt=""> </td> -->
                <td> 
                    <!-- le nom du produit (nom_stock) avec le lien qui est l'id du produit (id_produit) -->
                    <a href= "../produit/?produit=<?= htmlentities($row['id_produit'] ?? '') ?>"> <?= htmlentities($row['nom_stock'] ?? '')?> 
                    </a>
                </td>
                <td>
                    <?php if ($row['en_reduction']) {?>
                        <a href= "../produit/?produit=<?= htmlentities($row['id_produit'] ?? '') ?>"><button class="bouton"><img src="<?= HOME_SITE ?>/image/reduction.svg" title="Ce produit est en réduction" alt=""></button></a>
                    <?php } if ($row['en_promotion']) { ?>
                        <a href= "../produit/?produit=<?= htmlentities($row['id_produit'] ?? '') ?>"><button class="bouton"><img src="<?= HOME_SITE ?>/image/promo.svg" title="Ce produit est en promotion" alt=""></button></a>
                    <?php } ?>
                    

                    <a href= "../avis/?produit=<?= htmlentities($row['id_produit'] ?? '') ?>"><button class="bouton"><img src="<?= HOME_SITE ?>/image/etoile.svg"></button></a>
                    <a href= "../stock/modifier_produit/?produit=<?= htmlentities($row['id_produit'] ?? '') ?>"><button class="bouton"><img src="<?= HOME_SITE ?>/image/modifier.svg"></button></a>

                    <span> | </span>

                    <form action="./update_stock.php">
                        <label for="nb">Quantité
                            <span class="aide" data-tooltip="' + ' devant pour ajouter une valeur\n' - ' devant pour retirer une valeur\net appuyez sur entrée pour valider\nex: '+52' ajoute 52 à la quantité dans le panier">?</span>
                        </label>
                        <input type="hidden" id="produit" name="produit" value=<?= htmlentities($row['id_produit'] ?? '')?>>
                        <input type="text" size="8" id="nb" name="nb" value=<?= htmlentities($row['quantite'] ?? '')?>>
                        <input type="submit" class="bouton" value="Valider">
                    </form> 
                </td>
            </tr>
        
        <?php
        }
    }
    ?>
        </table>
    <?php
}

//commande qui permet de séléctionner l'id du produit, son nom et sa quantité en stock
$stmt = vendeur_All_produit($_SESSION['id_compte']);



?>

<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <?php include HOME_SITE . 'link_head.php' ?>
        <title>Alizon - Mon Stock</title>
    </head>
    <body class="stock">
        <?php include HOME_SITE . 'vendeur/header.php'; ?>
        <?php include HOME_SITE . 'vendeur/toolbar_stock.php'; ?>
        <main class="content_produit_vendeur">
            <a href="../accueil"><img src="../../image/retour.svg" class = "fleche_produit_arriere"></a>
            <!-- affiche tous les produits -->
            <?php ecrire_nom($stmt); ?>
        </main>
        <?php include HOME_SITE . "footer.php" ?>
    </body>
    <script>
        let listeAides = document.getElementsByClassName('aide');

        for (i = 0; i < listeAides.length; i++) {
            let element = listeAides[i];

            element.addEventListener('mouseover', (e) => {
                let baliseAide = document.createElement('p');

                let lignes = e.target.getAttribute('data-tooltip').split('\\n');

                for (ligne of lignes) {
                    let baliseLigne = document.createElement('span');
                    baliseLigne.textContent = ligne;
                    baliseAide.appendChild(baliseLigne);

                    baliseAide.appendChild(document.createElement('br'));
                }

                baliseAide.className = 'info-bulle';
                
                let x = e.target.getBoundingClientRect().x - 250;
                let y = e.target.getBoundingClientRect().y - 40 - 20 * lignes.length;
                
                baliseAide.setAttribute('style', 'top : ' + y + 'px; left : ' + x + 'px');

                e.target.appendChild(baliseAide);
            });

            element.addEventListener('mouseout', e => {
                e.target.removeChild(document.getElementsByClassName('info-bulle')[0]);
            })
        }
    </script>
</html>



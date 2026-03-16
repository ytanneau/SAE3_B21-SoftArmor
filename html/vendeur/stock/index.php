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
    <?php if (empty($rows)) { ?>
        <h1>Vous n'avez pas de produit</h1>
    <?php } else {
        foreach ($rows as $row){
        ?>
        
            <tr id=<?= htmlentities($row['id_produit'] ?? '')?>>
                <!-- <td><img src="MenuBurger.png" alt=""> </td> -->
                <td> 
                    <input type="hidden" onclick="testBoutonsSelect()" class="selectCheckbox" name="<?=$row['id_produit']?>">
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
                            <span class="aide" data-tooltip="' + ' devant pour ajouter une valeur\n' - ' devant pour retirer une valeur\net appuyez sur entrée pour valider\nex: '+52' ajoute 52 à la quantité dans le stock">?</span>
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
            <div>
                <a href="../accueil"><img src="../../image/retour.svg" class = "fleche_produit_arriere"></a>
                <?php if (!empty($stmt)) { ?>
                    <div id="select">
                        <button class="bouton" onclick="activateSelectMode()">Sélectionner des produits</button>
                        <span class="aide" data-tooltip="Permet de générer un fichier PDF\navec uniquement les produits sélectionnés">?</span>
                    </div>
                <?php } ?>
            </div>
            <!-- affiche tous les produits -->
            <?php ecrire_nom($stmt); ?>
        </main>
        <?php include HOME_SITE . "footer.php" ?>
    </body>
    <script src="<?=HOME_SITE?>infobulle.js"></script>
    <script>
        let listeInputs = Array.from(document.getElementsByClassName("selectCheckbox"));
        let divSelect = document.getElementById("select");

        function activateSelectMode() {

            for (i = 0; i < listeInputs.length; i++) {
                listeInputs[i].type = "checkbox";
            }

            divSelect.removeChild(divSelect.firstElementChild);
            
            // Création bouton validation
            let bt = document.createElement("button");
            bt.classList.add("bouton");
            bt.classList.add("valider");
            bt.textContent = "Télécharger la sélection";
            bt.setAttribute("onclick", "valideSelect()");
            bt.style.display = "none";
            divSelect.appendChild(bt);

            // Création bouton pour annuler
            bt = document.createElement("button");
            bt.classList.add("bouton");
            bt.classList.add("annuler");
            bt.textContent = "Annuler";
            bt.setAttribute("onclick", "window.location = ''");
            divSelect.appendChild(bt);

            // Création bouton pour tout sélectionner
            bt = document.createElement("button");
            bt.classList.add("bouton");
            bt.classList.add("select");
            bt.textContent = "Tout sélectionner";
            bt.setAttribute("onclick", "selectAll()");
            bt.setAttribute("id", "selectAllButton");
            divSelect.appendChild(bt);

        }

        function deactivateSelectMode() {
            window.location = "";
        }

        function valideSelect() {
            let produitSelect = false;
            
            let lien = "cataloge/cat.php?";

            listeInputs.forEach(element => {
                if (element.checked) {
                    lien += "p[]=" + element.name + '&';
                    produitSelect = true;
                }
            });
            
            if (produitSelect) {
                window.location = lien;
            }

        }

        function testBoutonsSelect() {
            if (!auMoins1ProduitSelect()) {
                document.getElementsByClassName("valider")[0].style.display = "none";
            } else {
                document.getElementsByClassName("valider")[0].style.display = "";

            }
        }

        function auMoins1ProduitSelect() {
            return listeInputs.some(element => {return element.checked});
        }

        function selectAll() {
            listeInputs.forEach(element => {
                element.checked = true;
            });

            let bt = document.getElementById("selectAllButton");
            bt.textContent = "Tout désélectionner";
            bt.setAttribute("onclick", "unselectAll()");

            testBoutonsSelect();
        }

        function unselectAll() {
            listeInputs.forEach(element => {
                element.checked = false;
            });

            let bt = document.getElementById("selectAllButton");
            bt.textContent = "Tout sélectionner";
            bt.setAttribute("onclick", "selectAll()");

            testBoutonsSelect();
        }
    </script>
</html>



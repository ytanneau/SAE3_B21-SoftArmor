<?php
const HOME_GIT = "../../";
const HOME_SITE = "../";
const IP = "host.docker.internal";
const PORT = "9000";
const RAPTOR = false;

$JOUR_SEMAINE = ["dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi"];
$MOIS_ANNEE = ["janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre"];


if (!isset($_SESSION)) {
    session_start();
}

// Si pas co, alors go page connexion
if (!isset($_SESSION['logged_in'])) {
    header("location: " . HOME_SITE . "compte/connexion");
}

// Si co avec compte vendeur, envoyé à la page vendeur
if (isset($_SESSION["raison_sociale"])) {
    header("location: " . HOME_SITE . "vendeur/stock");
}
require_once HOME_GIT . ".connexion_delivraptor.php";
require_once HOME_GIT . ".config.php";
require_once HOME_GIT . "fonction_commande.php";
require_once HOME_GIT . "fonction_categorie.php";


$liste_commandes = get_commandes($_SESSION["id_compte"]);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alizon - Vos commandes</title>
    <?php include HOME_SITE . 'link_head.php' ?>

    <script>
        function generePDF(url) {
            window.open(url).print();
        }

        async function printPage(url) {
            const response = await fetch(url);
            const html = await response.text();

            // Ouvre une nouvelle fenêtre
            const printWindow = window.open("", "_blank");

            // Injecte le HTML récupéré
            printWindow.document.write(html);
            printWindow.document.close();

            // Attend que la page soit chargée avant impression
            printWindow.onload = () => {
                printWindow.print();
                printWindow.close();
            };
        }

        // Utilisation
        //printPage("https://example.com");

    </script>
</head>

<body class="liste-commande-page">
    <?php
    include HOME_SITE . "header.php";
    include HOME_SITE . "toolbar_categories.php";
    ?>

    <main>
        <?php if (count($liste_commandes) == 0) { ?>
            <p>Vous n'avez effectué aucune commande sur le site avec ce compte</p>
        <?php } else {
            ?>
            <div>
                <span class="aide"
                    data-tooltip="Etape 1 : Création d’un bordereau de livraison\nEtape 2 : Prise en charge du colis chez le vendeur\nEtape 3 : Arrivée chez le transporteur.\nEtape 4 : Départ vers la plateforme régionale\nEtape 5 : Arrivée sur la plateforme régionale\nEtape 6 : Départ vers le centre local\nEtape 7 : Arrivée au centre local\nEtape 8 : Départ pour la livraison finale\nEtape 9 : Livré ou refusé\nEtape inconnue : livraison non prise en charge par le site\nProblème serveur : Erreur de connexion au serveur">?</span>
            </div>
            <?php
            $fd = connexion_socket(IP, PORT);
            if ($fd != false) {
                $conn = connexion_delivraptor($fd, $id_delivraptor, $mdp_delivraptor);
            } else {
                $conn = 0;
            }
            ?>
            <table class="liste-commande">
                <thead>
                    <tr>
                        <th>Livreur</th>
                        <th>Date</th>
                        <th>Livraison</th>
                        <th>Image</th>
                        <th>Facture</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($liste_commandes as $commande) {
                        $d = strtotime($commande["date_commande"]);
                        $jour = $JOUR_SEMAINE[date("w", $d)];
                        $mois = $MOIS_ANNEE[date("m", $d) - 1];
                        ?>
                        <tr>
                            <td><?php if ($commande['bordereau_colis']) {
                                ?><p>Raptor livraison<br>Bordereau : <?=htmlentities($commande['bordereau_colis'])?>
                            <?php } else {
                                ?><p>Autre</p><?php
                            } ?>
                            </td>
                            <td>
                                <p><?= $jour . date(" d ", $d) . $mois . date(" Y à H:i:s", $d) ?></p>
                            </td>
                            <td>
                                <p><?= livraison_info($commande['bordereau_colis'])?></p>
                            </td>
                            <td>
                                <?php if ($commande['bordereau_colis'] != null && $conn == 1) {

                                    $info_colis = get_info_colis($fd, $commande['bordereau_colis']);

                                    if ($info_colis["RENDU"] == "1") {

                                        if (!file_exists(HOME_SITE . "ressources/colis/" . $commande['bordereau_colis'] . ".png")) {

                                            //recuperation de l'image
                                            $texte_img = get_image_colis($fd, $commande['bordereau_colis']);
                                        }
                                        ?>
                                        <a href="<?= HOME_SITE . "ressources/colis/" . $commande['bordereau_colis'] . ".png" ?>"
                                            target="_blank">
                                            <img src="<?= HOME_SITE . "ressources/colis/" . $commande['bordereau_colis'] . ".png" ?>"
                                                alt="Image du colis" title="image_colis">
                                        </a>
                                        <?php

                                    }
                                }

                                ?>
                            </td>
                            <td>
                                <button class="bouton"
                                    onclick="printPage('<?= 'facture/?commande=' . $commande['id_commande'] ?>')">Consulter la
                                    facture</button>
                                <!--a href="./facture?commande=<?= $commande["id_commande"] ?>" class="bouton">Consulter la
                                    facture</a-->
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>

            <?php
            deconnexion_socket($fd);
        }
        ?>
        
    </main>
    <!-- Navigation (pour teléphone) -->
    <nav id="nav_tel">
        <a href="<?=HOME_SITE?>panier"><img src="<?=HOME_SITE?>image/panier.svg" title="Acceder au Panier" alt="panier"></a>
        <a href="<?=HOME_SITE?>"><img src="<?=HOME_SITE?>image/home.svg" title="Acceder à la page d'Accueil" alt="accueil"></a>

        <?php if ($_SESSION['logged_in'] ?? false === true) { ?>
            <a href="<?=HOME_SITE?>compte/informations"><img src="<?=HOME_SITE?>image/compte.svg" title="Acceder aux information du compte" alt="compte"></a>
        <?php } else { ?>
            <a href="<?=HOME_SITE?>compte/connexion"><img src="<?=HOME_SITE?>image/connexion.svg" title="Se connecter" alt="connexion"></a>
        <?php } ?>
    </nav>  

    <?php
    if (!isset($_GET["commande"])) {
        include HOME_SITE . "footer.php";
    } ?>
</body>
<script src="<?= HOME_SITE ?>infobulle.js"></script>

</html>
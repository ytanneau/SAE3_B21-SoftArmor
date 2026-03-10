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
        function generePDF() {
            window.print();
        }
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
            <span class="aide"
                data-tooltip="Etape 1 : Création d’un bordereau de livraison\nEtape 2 : Prise en charge du colis chez le vendeur\nEtape 3 : Arrivée chez le transporteur.\nEtape 4 : Départ vers la plateforme régionale\nEtape 5 : Arrivée sur la plateforme régionale\nEtape 6 : Départ vers le centre local\nEtape 7 : Arrivée au centre local\nEtape 8 : Départ pour la livraison finale\nEtape 9 : Livré ou refusé\nEtape inconnue : livraison non prise en charge par le site\Problème serveur : Erreur de connexion au serveur">?</span>
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
                        <th>Liveur</th>
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
                        $mois = $MOIS_ANNEE[date((int) "m", $d)];
                        ?>
                        <tr>
                            <td><?php if ($commande['bordereau_colis']) {
                                echo "Raptor livairaison <br> Bordereau : " . htmlentities($commande['bordereau_colis']);
                            } else {
                                echo "Autre";
                            } ?>
                            </td>
                            <td><?= $jour . date(" d ", $d) . $mois . date(" Y à H:i:s", $d) ?></td>
                            <td>
                                <?= livraison_info($commande['bordereau_colis']); ?>
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
                            <td><a href="./info?commande=<?= $commande["id_commande"] ?>" class="bouton">Consulter la
                                    facture</a></td>
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

    <?php
    if (!isset($_GET["commande"])) {
        include HOME_SITE . "footer.php";
    } ?>
</body>
<script src="<?= HOME_SITE ?>infobulle.js"></script>

</html>
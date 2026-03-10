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

<body class="liste">
    <?php
    include HOME_SITE . "header.php";
    include HOME_SITE . "toolbar_categories.php";
    ?>

    <main>
        <?php if (count($liste_commandes) == 0) { ?>
            <p>Vous n'avez effectué aucune commande sur le site avec ce compte</p>
        <?php } else {
            ?>
            <span class="aide" data-tooltip="Etape 1 : Création d’un bordereau de livraison\nEtape 2 : Prise en charge du colis chez le vendeur\nEtape 3 : Arrivée chez le transporteur.\nEtape 4 : Départ vers la plateforme régionale\nEtape 5 : Arrivée sur la plateforme régionale\nEtape 6 : Départ vers le centre local\nEtape 7 : Arrivée au centre local\nEtape 8 : Départ pour la livraison finale\nEtape 9 : Livré ou refusé\nEtape inconnue : livraison non prise en charge par le site\Problème serveur : Erreur de connexion au serveur">?</span>
            <?php
            if (RAPTOR) {

                //connexion delivraptor
                $conn = false;
                $fd = connexion_socket(IP, PORT);
                $conn = connexion_delivraptor($fd, $id_delivraptor, $mdp_delivraptor);

                foreach ($liste_commandes as $commande) {
                    $d = strtotime($commande["date_commande"]);
                    $jour = $JOUR_SEMAINE[date("w", $d)];
                    $mois = $MOIS_ANNEE[date((int) "m", $d)];

                    if ($commande["bordereau_colis"] != null) {

                        //si connexion socket
                        if ($fd) {
                            //si connexion delivraptor
                            if ($conn == "1") {
                                $bordereau = $commande["bordereau_colis"];

                                //recuperation des données du colis
                                $info_colis = get_info_colis($fd, $bordereau);

                                $texte_img = "";
                                //si le colis est rendu dans la boite au lettre et si l'image n'existe pas
                                if ($info_colis["RENDU"] == "1" && !file_exists(HOME_SITE . "ressources/colis/$bordereau.png")) {

                                    //recuperation de l'image
                                    $texte_img = get_image_colis($fd, $bordereau);
                                }
                            }
                        }
                    }


                    ?>

                    <li>
                        <div>
                            <div>
                                <?php
                                //si il y a eu connexion delivraptor et que il n'y a pas d'erreur
                                if ($conn == "1" and $info_colis["ERROR"] == "N/A" and $texte_img == ""):
                                    //initialisation de tout les variables
                                    $texte_refus = "";
                                    $texte_rendu = "";
                                    $texte_etape = "";
                                    $livraison = "";
                                    ?>


                                    <p>Bordereau:<?php echo htmlentities($bordereau); ?></p>



                                    <?php
                                    //affichage du refsu de colis
                                    switch ($info_colis["REFUS"]) {
                                        case '0':
                                            $texte_refus = "Colis endommagé";
                                            break;
                                        case '1':
                                            $texte_refus = "Ne correspond pas à la commande";
                                            break;
                                        case '2':
                                            $texte_refus = "En retard";
                                            break;
                                        case '3':
                                            $texte_refus = "Plus besoin du colis";
                                            break;
                                    }
                                    //affichage rendu du colis
                                    switch ($info_colis["RENDU"]) {
                                        case '0':
                                            $texte_rendu = "Colis remis en main propre";
                                            break;
                                        case '1':
                                            $texte_rendu = "Colis dans la boite au lettre";
                                            break;
                                        case '2':
                                            $texte_rendu = "Colis refusé. cause : $texte_refus";
                                            break;

                                        default:
                                            $texte_rendu = "";
                                            break;
                                    }

                                    ?>
                                    <p><?php echo htmlentities($texte_rendu); ?></p>

                                    <?php
                                    //affichage des etapes
                                    $livraison = "Colis en cours de livraison";
                                    switch ($info_colis["ETAPE"]) {
                                        case "1":
                                            $livraison = "Colis en cours de traitement";
                                            $texte_etape = "";
                                            break;
                                        case "2":
                                            $texte_etape = "Prise en charge du colis chez Alizon";
                                            break;
                                        case "3":
                                            $texte_etape = "Arrivée chez le transporteur";
                                            break;
                                        case "4":
                                            $texte_etape = "Départ vers la plateforme régionale";
                                            break;
                                        case "5":
                                            $texte_etape = "Arrivée sur la plateforme régionale";
                                            break;
                                        case "6":
                                            $texte_etape = "Départ vers le centre local";
                                            break;
                                        case "7":
                                            $texte_etape = "Arrivée au centre local";
                                            break;
                                        case "8":
                                            $texte_etape = "Départ pour la livraison finale";
                                            break;
                                        case "9":
                                            $texte_etape = "Fin de livraison";
                                            $livraison = "";
                                            break;


                                    }
                                    ?>
                                    <p><?php echo htmlentities($livraison); ?></p>
                                    <p><?php echo htmlentities($texte_etape); ?></p>
                                    <?php
                                    //si la connexion est refusé
                                elseif ($conn == "0"): ?>
                                    <p>Connexion refusé</p>
                                    <?php
                                    //si la connexion est accepté mais il y a une erreur sur le colis 
                                elseif ($conn == "1" and $info_colis["ERROR"] != "N/A"): ?>
                                    <p>Erreur, le colis n'existe pas</p>
                                    <?php
                                    //si la connexion est accepté mais il y a une erreur sur l'image
                                elseif ($conn == "1" and $texte_img != ""): ?>
                                    <p>Erreur, <?php echo htmlentities($texte_img) ?></p>
                                <?php endif; ?>
                            </div>
                            <p>Commande du <?= $jour . date(" d ", $d) . $mois . date(" Y à H:i:s", $d) ?></p>
                            <a href="?commande=<?= $commande["id_commande"] ?>" class="bouton">Consulter la commande</a>
                            <?php if ($info_colis["RENDU"] == "1") { ?>
                                <img src="<?= HOME_SITE ?>ressources/colis/<?= htmlentities($bordereau) ?>.png" alt="image colis">
                            <?php } ?>
                        </div>
                        <hr>
                    </li>

                <?php } ?>
                <?php
                //deconnexion socket et delivraptor
                deconnexion_socket($fd);
            } else {
                $conn = false;
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
                                            <a href="<?= HOME_SITE . "ressources/colis/" . $commande['bordereau_colis'] . ".png" ?>" target="_blank">
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
        } ?>



    </main>

    <?php
    if (!isset($_GET["commande"])) {
        include HOME_SITE . "footer.php";
    } ?>
</body>
<script src="<?=HOME_SITE?>infobulle.js"></script>

</html>
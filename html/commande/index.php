<?php
const HOME_GIT = "../../";
const HOME_SITE = "../";

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

require_once HOME_GIT . ".config.php";
require_once HOME_GIT . "/fonction_commande.php";

if (isset($_GET["commande"])) {
    $liste_elements = get_elements_commande($_GET["commande"]);
} else {
    $liste_commandes = get_commandes($_SESSION["id_compte"]);

}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Alizon - Vos commandes</title>
    <?php include HOME_SITE . 'link_head.php' ?>
</head>

<body class="liste">
    <?php include HOME_SITE . 'header.php'?>
    <main>

    <?php if (isset($_GET["commande"])) {?>

        <div>
            <a href="."><img src="../image/retour.svg"></a>

            <?php if (count($liste_elements) == 0) { ?>
                <p>Vous n'avez pas accès à cette commande</p>

            <?php } else { 
                $somme_totale = 0;
                $vendeur_prec = "";
                ?>
                
                <h1>Liste des éléments de la commande : </h1>
                <ul>
                    <hr>
                <?php foreach ($liste_elements as $element) {
                    if ($element["nom_vendeur"] != $vendeur_prec) {?>
                        <h2>Vendu par <?=$element["nom_vendeur"]?></h2>
                    <?php
                    $vendeur_prec = $element["nom_vendeur"];
                    } ?>

                    <li>
                        <p>Produit : <?=$element["nom_produit"]?></p>
                        <p>Prix unitaire : <?=number_format($element["prix"], 2, ',', ' ')?> €</p>
                        <p>Quantité achetée : <?=$element["quantite"]?></p>
                        <p>Prix total : <?=number_format($element["prix"] * $element["quantite"], 2, ',', ' ')?> €</p>
                    </li>
                    <hr>

                    <?php $somme_totale += $element["prix"] * $element["quantite"]?>
                <?php } ?>
                </ul>
                
                <p>Somme totale de la commande : <?=number_format($somme_totale, 2, ',', ' ')?> €</p>
            <?php } ?>
        </div>

    <?php } else { ?>
        <ul>
            <?php if (count($liste_commandes) == 0) { ?>
                <p>Vous n'avez effectué aucune commande sur le site avec ce compte</p>
            <?php } else {
                    foreach ($liste_commandes as $commande) {
                    $d = strtotime($commande["date_commande"]);
                    $jour = $JOUR_SEMAINE[date("w", $d)];
                    $mois = $MOIS_ANNEE[date((int)"m", $d)];

                    $fd = connexion_socket();
                    $conn = connexion_delivraptor($fd,"root","root");
                    if ($conn == "true"){
                        $bordereau = $commande["bordereau"];
                        $info_colis =get_info_colis($fd,$bordereau);
                        $texte_img="";
                        if ($info_colis["RENDU"] == "1") {
                            $img = get_image_colis($fd,$bordereau);
                            switch ($img) {
                                case '3':
                                    $texte_img="Colis inexistent";
                                    break;
                                case '4':
                                    $texte_img="Photo inexistante";
                                    break;
                                
                                default:
                                    $fich = file_put_contents(HOME_SITE . "ressources/colis/test.png",$img);
                                    break;
                            }
                        }   
                    }
                    deconnexion_socket($fd);
                ?>

                    <li>
                        <div>
                            <div>
                                <?php

                                if ($conn =="true" and $info_colis["ERROR"]=="N/A" and $texte_img =="") :?>

                                    
                                
                                <p>Bordereau:<?php echo htmlentities($bordereau) ; ?></p>
                                
                                
                                    
                                <?php
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

                                switch ($info_colis["RENDU"]) {
                                    case '0':
                                        $texte_rendu = "Colis remis en main propre";
                                        break;
                                    case '1':
                                        $texte_rendu ="Colis dans la boite au lettre";
                                        break;
                                    case '2':
                                        $texte_rendu ="Colis refusé. cause : $texte_refus";
                                        break;
                                    
                                    default:
                                        $texte_rendu ="";
                                        break;
                                    }
                                    
                                    ?>
                                    <p><?php echo htmlentities($texte_rendu) ;?></p>
                                
                                <?php
                                    $livraison = "Colis en cours de livraison";
                                    switch ($info_colis["ETAPE"]) {
                                        case "1":
                                            $texte_etape = "Création d’un bordereau de livraison";
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
                                    <p><?php echo htmlentities($livraison);?></p>
                                    <p><?php echo htmlentities($texte_etape);?></p>
                                <?php
                                elseif ($conn == "false") :?>
                                <p>Connexion refusé</p>
                                <?php elseif ($conn =="true" and $info_colis["ERROR"]!="N/A") :?>
                                    <p>Erreur, le colis <?php echo htmlentities($bordereau)?> n'existe pas</p>
                                <?php
                                elseif  ($conn =="true" and $texte_img !="") :?>
                                <p>Erreur, <?php echo htmlentities($texte_img)?></p>
                                <?php endif; ?>
                            </div>
                            <p>Commande du <?=$jour . date(" d ", $d) . $mois . date(" Y à H:i:s", $d)?></p>
                            <a href="?commande=<?=$commande["id_commande"]?>" class="bouton">Consulter la commande</a>
                        </div>
                        <hr>
                    </li>

            <?php } } ?>

        </ul>

    </main>
        
    <?php }
    if (!isset($_GET["commande"])) {
        include HOME_SITE . "footer.php";
    }?>
</body>
</html>
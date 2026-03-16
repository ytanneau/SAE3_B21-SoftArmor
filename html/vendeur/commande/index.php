<?php
const HOME_GIT = "../../../";
const HOME_SITE = "../../";

$JOUR_SEMAINE = ["dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi"];
$MOIS_ANNEE = ["janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre"];



if (!isset($_SESSION)) {
    session_start();
}

// Si pas co, alors go page connexion
if (!isset($_SESSION['logged_in'])) {
    header("location: " . HOME_SITE . "vendeur");
}

// Si co avec compte client, retour à l'accueil
else if (!isset($_SESSION["raison_sociale"])) {
    header("location: " . HOME_SITE);
}

require_once HOME_GIT . ".config.php";
require_once HOME_GIT . "/fonction_commande.php";


$liste_commandes = get_commandes_vendeur($_SESSION["id_compte"]);


?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alizon - Les commandes</title>
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
    <?php include HOME_SITE . 'vendeur/header.php' ?>
    <main class="liste-commande-vendeur">
        <div>
            <a href="../accueil"><img src="../../image/retour.svg" class="fleche_produit_arriere"></a>
            <?php if (count($liste_commandes) == 0) { ?>
                <p>Aucune commande n'inclut l'un de vos produits mis en vente</p>
            <?php } else { ?>
                <table class="liste-commande">
                    <thead>
                        <tr>
                            <th>Date de la commande</th>
                            <th>Client</th>
                            <th>Récapitulatif de la commande</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($liste_commandes as $commande) {
                            $d = strtotime($commande["date_commande"]);
                            $jour = $JOUR_SEMAINE[date("w", $d)];
                            $mois = $MOIS_ANNEE[date("m", $d) - 1];
                            ?>

                            <tr>
                                <td>
                                    <?= $jour . date(" d ", $d) . $mois . date(" Y à H:i:s", $d) ?>
                                </td>
                                <td>
                                    <?= $commande["pseudo_client"] ?>
                                </td>
                                <td>
                                    <button class="bouton"
                                        onclick="printPage('<?= 'facture/?commande=' . $commande['id_commande'] ?>')">Consulter la facture</button>
                                    <!--a href="?commande=<?= $commande["id_commande"] ?>" class="bouton" target="_blank">Consulter la commande</a-->
                                </td>
                            </tr>

                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
        </div>
    </main>

    <?php if (!isset($_GET["commande"])) {
        include HOME_SITE . "footer.php";
    } ?>

</body>

</html>
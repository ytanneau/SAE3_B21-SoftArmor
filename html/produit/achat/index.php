

<!DOCTYPE html>
<html>
    <head>
        <title>Alizon - Achat</title>
        <meta charset="UTF-8">
        <meta lang="fr">
<?php
if (!isset($_SESSION)) {
    session_start();
}

const HOMT_GIT = "../../../";
require_once HOME_GIT . ".config.php";

if (!isset($_GET['produit'])) {
    header(HOME_GIT . "html/");
}

$pdo->prepare("SELECT nom_public, prix, tva FROM produit WHERE id_produit = :id_produit");
$pdo->bindValue(":id_produit", $_GET['produit']);
$pdo->execute();

$produit = $requete->fetch(PDO::FETCH_ASSOC);
?>


    </head>

    <body>
        <h1>Achat du produit <?=$produit['nom_public']?></h1>

        <p>Prix HT : <?=$produit['prix']?></p>
        <p>TVA : <?=$produit['tva']?></p>
        <p>Prix TTC : <?=$produit['prix'] * $produit['tva']?></p>

        <button>Acheter</button>
    </body>
</html>
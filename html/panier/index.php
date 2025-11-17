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
<body>
    <?php include HOME_SITE . 'header.php' ?>

    <h1>Mon panier</h1>

    <?php if (!$elts_panier) { ?>
        <h2>Votre panier est vide.</h2>
    <?php } ?>

    <ul>
        <?php foreach ($elts_panier as $elt) { ?>
            <li>
                <p><?= $elt['nom_public'] ?></p>
                <p><?= $elt['prix'] . ' €' ?></p>
            </li>
        <?php } ?>
    </ul>
</body>
</html>
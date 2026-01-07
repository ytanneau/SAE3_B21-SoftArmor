<?php
    // Inclusion du fichier de configuration
    define('HOME_GIT', '../../');
    define('HOME_SITE', '../');

    require_once (HOME_GIT . '.config.php');
    require_once (HOME_GIT . 'fonction_recherche.php');

    // Récupérer dans un array tous les produits contenant la recherche
    $requete = $pdo->prepare("SELECT * FROM produit_visible");
    $requete->execute();
    $produits = $requete->fetchAll(PDO::FETCH_ASSOC);

    $produits_json = json_encode($produits);

    echo $produits_json;
?>
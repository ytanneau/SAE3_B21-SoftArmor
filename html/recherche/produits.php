<?php
    // Inclusion du fichier de configuration
    define('HOME_GIT', '../../');
    define('HOME_SITE', '../');
    
    require_once (HOME_GIT . '.config.php');
    require_once (HOME_GIT . 'fonction_recherche.php');

    // Récupérer dans un array tous les produits contenant la recherche
    $produits = get_produits_recherche($recherche);
    $produits_json = json_encode($produits);

    echo $produits_json;
?>
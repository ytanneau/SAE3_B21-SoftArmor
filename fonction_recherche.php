<?php

function get_produits_recherche($recherche) {
    global $pdo;
        
    $requete = $pdo->prepare("SELECT * FROM produit_visible WHERE nom_public LIKE '%Kouign%'");
    //$requete->bindValue(':recherche', $recherche, PDO::PARAM_STR);
    $requete->execute();
    return $requete->fetch(PDO::FETCH_ASSOC);
}
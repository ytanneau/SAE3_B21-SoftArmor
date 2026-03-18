<?php
    // Inclusion du fichier de configuration
    define('HOME_GIT', '../../');
    define('HOME_SITE', '../');

    require_once (HOME_GIT . '.config.php');
    require_once (HOME_GIT . 'fonction_avis.php');

    // On récupère le corps de la requête HTTP (au format JSON) dans un tableau associatif
    $data = json_decode(file_get_contents('php://input'), true);
    
    $id_avis = $data['id_avis'] ?? '';

    // Récupérer l'avis à partir du produit et 
    $infos = get_infos_avis($id_avis);
    

    echo json_encode([
        'note' => $infos['note'],
        'titre' => $infos['titre'],
        'commentaire' => $infos['commentaire'],
        'image' => $infos['url_image'] ?? ''
    ]);
?>
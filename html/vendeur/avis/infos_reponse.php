<?php
    // Inclusion du fichier de configuration
    define('HOME_GIT', '../../../');
    define('HOME_SITE', '../../');

    require_once (HOME_GIT . '.config.php');
    require_once (HOME_SITE . 'fonction_avis.php');

    // On récupère le corps de la requête HTTP (au format JSON) dans un tableau associatif
    $data = json_decode(file_get_contents('php://input'), true);

    $id_avis = $data['id_avis'] ?? '';

    // Récupérer le champ réponse
    $reponse = get_reponse($id_avis)['reponse'];

    echo json_encode([
        'reponse' => $reponse
    ]);
?>
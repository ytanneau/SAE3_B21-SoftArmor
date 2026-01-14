<?php
    // Inclusion du fichier de configuration
    define('HOME_GIT', '../../');
    define('HOME_SITE', '../');

    require_once (HOME_GIT . '.config.php');
    require_once (HOME_GIT . 'fonction_recherche.php');

    // On récupère le corps de la requête HTTP (au format JSON) dans un tableau associatif
    $data = json_decode(file_get_contents('php://input'), true);

    // On récupère la recherche, les filtres et tris éventuels
    $search  = $data['search'] ?? '';
    $filters = $data['filters'] ?? [];
    $sort    = $data['sort'] ?? [];

    // Construire la requête SQL à partir de la recherche
    $requete = 
        "SELECT p.*, i.url_image, i.titre, i.alt 
        FROM produit_en_ligne p
        INNER JOIN _image i
        ON p.id_image_principale = i.id_image
        WHERE 1 = 1";
    
    $params = [];
    
    // Filtre par recherche
    if (!empty($search)) {
        $requete .= " AND (nom_public LIKE :search OR description LIKE :search OR description_detaillee LIKE :search)";
        $params[':search'] = "%$search%";
    }
    
    if ($sort['field'] == 'nom_public') {
        $requete .= " ORDER BY nom_public ASC";
    }
    else {
        $requete .= " ORDER BY :field :order, nom_public ASC";
        $params[':field'] = $sort['field'];
        $params[':order'] = $sort['order'];
    }
    

    $requete = $pdo->prepare($requete);
    $requete->execute($params);
    $produits = $requete->fetchAll(PDO::FETCH_ASSOC);
    print_r($produit);
    echo json_encode([
        'produits' => $produits,
        'total' => count($produits)
    ]);
?>
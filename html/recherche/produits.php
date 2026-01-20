<?php
    // Inclusion du fichier de configuration
    define('HOME_GIT', '../../');
    define('HOME_SITE', '../');

    require_once (HOME_GIT . '.config.php');
    require_once (HOME_GIT . 'fonction_recherche.php');

    // On récupère le corps de la requête HTTP (au format JSON) dans un tableau associatif
    $data = json_decode(file_get_contents('php://input'), true);

    // On récupère la recherche, les filtres et tris éventuels
    $search     = $data['search'] ?? '';
    $filters    = $data['filters'] ?? [];
    $sort       = $data['sort'] ?? [];
    $category   = $filters['category'] ?? '';
    $prixmin    = $filters['price']['min'] ?? null;
    $prixmax    = $filters['price']['max'] ?? null;
    $prom       = $filters['sales'] ?? null;
    // Construire la requête SQL à partir de la recherche
    $requete = 
        "SELECT p.*, i.url_image, i.titre, i.alt 
        FROM produit_en_ligne p
        INNER JOIN _image i
        ON p.id_image_principale = i.id_image
        INNER JOIN _categorie c
        ON p.categorie = c.nom_categorie
        WHERE 1 = 1";
    
    $params = [];
    
    if ($prom == true) {
        $requete .= " AND en_promotion = 1";
    }

    // Filtre par recherche
    if (!empty($search)) {
        $requete .= " AND (nom_public LIKE :search OR description LIKE :search OR description_detaillee LIKE :search)";
        $params[':search'] = "%$search%";
    }

    if (!empty($category)) {
        $requete .= " AND (categorie = :categorie OR c.nom_categorie_sup = :categorie)";
        $params[':categorie'] = $category;
    }

    if ($prixmin !== null) {
        $requete .= " AND  (prix_actuel * (1 + tva / 100)) >= :prixmin";
        $params[':prixmin'] = $prixmin;
    }

    if ($prixmax !== null) {
        $requete .= " AND  (prix_actuel * (1 + tva / 100)) <= :prixmax";
        $params[':prixmax'] = $prixmax;
    }
    
    $sortableFields = [
        'nom_public' => 'nom_public',
        'note_moy'   => 'note_moy',
        'triPrix'    => 'prix_actuel',
        'triPrixCroi'=> 'prix_actuel',
        'triReduc'   => 'prix_actuel'
    ];

    $fieldKey = $sort['field'] ?? 'nom_public';
    $order = strtoupper($sort['order'] ?? 'ASC');

    $field = $sortableFields[$fieldKey] ?? 'nom_public';
    if (!in_array($order, ['ASC','DESC'])) {
        $order = 'ASC';
    }

    $requete .= " ORDER BY $field $order, nom_public ASC";


    $requete = $pdo->prepare($requete);
    $requete->execute($params);
    $produits = $requete->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode([
        'produits' => $produits,
        'total' => count($produits)
    ]);
?>
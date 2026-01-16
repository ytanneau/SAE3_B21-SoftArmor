<?php
    // Inclusion du fichier de configuration
    define('HOME_GIT', '../../');
    define('HOME_SITE', '../');

    // Redirige les utilisateurs non connectés
    if (!isset($_SESSION)) {
        session_start();

        if (!isset($_SESSION['logged_in'])) {
            header('location: ' . HOME_SITE);
            exit;
        }

        $id_compte = $_SESSION['id_compte'];
    }

    var_dump($_POST);

    /*
    require_once (HOME_GIT . '.config.php');
    require_once (HOME_GIT . 'fonction_avis.php');

    // On récupère la recherche, les filtres et tris éventuels
    $id_avis = $_POST['id_avis'] ?? '';
    $id_client = $_SESSION['id_client'] ?? '';
    $raison  = $_POST['raison'] ?? '';

    // Construire la requête SQL à partir de la recherche
    $requete = 
        "";
    
    $params = [];
    
    // Filtre par recherche
    if (!empty($search)) {
        $requete .= " AND (nom_public LIKE :search OR description LIKE :search OR description_detaillee LIKE :search)";
        $params[':search'] = "%$search%";
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
    */
?>
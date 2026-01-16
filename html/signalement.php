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

    require_once (HOME_GIT . '.config.php');
    require_once (HOME_GIT . 'fonction_avis.php');

    // On récupère la recherche, les filtres et tris éventuels
    $id_avis = $_POST['id_avis'] ?? '';
    $id_compte = $_SESSION['id_compte'] ?? '';
    $raison  = $_POST['raison'] ?? '';

    // Si il manque des informations, erreur
    echo json_encode([
        'success' => false,
        'message' => "L'avis n'a pas pu être signalé. Veuillez réessayer plus tard."
    ]);

    try {
        // Si déjà signalé par l'utilisateur, erreur
        if (avis_est_signale($id_avis, $id_compte)) {
            echo json_encode([
                'success' => false,
                'message' => "Vous avez déjà signalé cet avis."
            ]);
        }

        // Marquer l'avis comme signalé
        signaler_avis($id_compte, $id_avis, $raison);

        echo json_encode([
            'success' => true,
            'message' => "L'avis a été signalé à Alizon. Nous le vérifierons dans les plus brefs délais."
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => "Nous rencontrons des problèmes serveur. Veuillez réessayer plus tard."
        ]);
    }

    // Construire la requête SQL à partir de la recherche

    
    
    
?>
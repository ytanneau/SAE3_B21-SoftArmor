<?php
    // Inclusion du fichier de configuration
    define('HOME_GIT', '../../../');
    define('HOME_SITE', '../../');

    // Démarrer la session
    if (!isset($_SESSION)) {
        session_start();
        $id_compte = $_SESSION['id_compte'] ?? '';
    }

    require_once (HOME_GIT . '.config.php');
    require_once (HOME_GIT . 'fonction_avis.php');

    // On récupère la recherche, les filtres et tris éventuels
    $id_avis = $_POST['id_avis'] ?? '';
    $reponse  = $_POST['reponse'] ?? '';

    // Si il manque des informations, erreur
    if (empty($id_avis) || empty($reponse) || empty($id_compte)) {
        echo json_encode([
            'success' => false,
            'message' => "La réponse n'a pas pu être envoyée. Veuillez réessayer plus tard."
        ]);
        die();
    }

    try {
        // Si le vendeur a déjà répondu, erreur
        if (avis_est_repondu($id_avis)) {
            echo json_encode([
                'success' => false,
                'message' => "Vous avez déjà répondu à cet avis."
            ]);
            die();
        }

        // Marquer l'avis comme signalé
        repondre_avis($id_avis, $reponse);

        echo json_encode([
            'success' => true,
            'message' => "Votre réponse a été publiée."
        ]);
        die();
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => "Nous rencontrons des problèmes serveur. Veuillez réessayer plus tard. " . $e->getMessage()
        ]);
        die();
    }
?>
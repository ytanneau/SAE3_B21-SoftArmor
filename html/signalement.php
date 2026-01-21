<?php
    // Inclusion du fichier de configuration
    define('HOME_GIT', '../');
    define('HOME_SITE', '.');

    // Démarrer la session
    if (!isset($_SESSION)) {
        session_start();
        $id_compte = $_SESSION['id_compte'] ?? null;
    }

    require_once (HOME_GIT . '.config.php');
    require_once (HOME_GIT . 'fonction_avis.php');

    // On récupère la recherche, les filtres et tris éventuels
    $id_avis = $_POST['id_avis'] ?? '';
    $id_reponse = $_POST['id_reponse'] ?? '';
    $email = $_POST['email'] ?? null;
    $raison  = $_POST['raison'] ?? '';

    // Si il manque des informations, erreur
    if ((empty($id_avis) && empty($id_reponse)) || empty($raison) || (!isset($id_compte) && !isset($email))) {
        echo json_encode([
            'success' => false,
            'message' => "L'avis n'a pas pu être signalé. Veuillez réessayer plus tard."
        ]);
        die();
    }

    try {
        // Si déjà signalé par l'utilisateur, erreur
        if (avis_est_signale($id_avis, $id_compte, $email)) {
            echo json_encode([
                'success' => false,
                'message' => "Vous avez déjà signalé cet avis."
            ]);
            die();
        } else if (reponse_est_signalee($id_reponse, $id_compte, $email)) {
            echo json_encode([
                'success' => false,
                'message' => "Vous avez déjà signalé cette réponse."
            ]);
            die();
        }

        // Marquer l'avis comme signalé
        if (!empty($id_avis)) {
            signaler_avis($id_compte, $id_avis, $raison, $email);
        } else {
            signaler_reponse($id_compte, $id_reponse, $raison, $email);
        }
        

        echo json_encode([
            'success' => true,
            'message' => "L'avis a été signalé à Alizon. Nous le vérifierons dans les plus brefs délais."
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
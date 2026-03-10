<?php
    // Inclusion du fichier de configuration
    define('HOME_GIT', '../../');
    define('HOME_SITE', '../');

    // Démarrer la session
    if (!isset($_SESSION)) {
        session_start();
        $id_compte = $_SESSION['id_compte'] ?? null;
    }

    require_once (HOME_GIT . '.config.php');
    require_once (HOME_GIT . 'fonction_avis.php');

    // On récupère la note, le titre et la description
    $note = $_POST['note'] ?? '';
    $titre = $_POST['titre'] ?? '';
    $description = $_POST['description'] ?? '';
    $id_produit = $_POST['produit'] ?? '';

    $image = null;

    $erreur = condition_avis();

    // S'il y a des erreurs dans la soumission, fin du traitement
    if (!empty($erreur)) {
        echo json_encode([
            'success' => false,
            'message' => "L'avis n'a pas pu être créé. Veuillez réessayer plus tard."
        ]);
        supprimer_image($fichier, $image);
        die();
    }

    // Met l'image uploadée avec les autres pour éviter de la perdre
    if (isset($_FILES['image']) && isset($_FILES['image']['name'])) {
        $fichier = $id_compte . '_'. time();
        move_uploaded_file($_FILES['image']['tmp_name'], HOME_SITE . "ressources/avis/" . $fichier);

        if ($_FILES['image']['size'] > 0) {
            $image = 'ressources/avis/' . $id_produit . '_' . $id_compte . '.png';
        }
    }

    try {
        // Si déjà signalé par l'utilisateur, erreur
        if (check_avis_existe($id_produit, $id_compte)) {
            echo json_encode([
                'success' => false,
                'message' => "Vous avez déjà donné votre avis sur ce produit."
            ]);
            supprimer_image($fichier, $image);
            die();
        }

        // Créer l'avis
        if (!empty($id_produit)) {
            cree_avis($id_compte, $id_produit, $note, $titre, $description, $image);
        
            if ($image != null) {
                rename('../ressources/avis/' . $fichier, '../' . $image);
            }
        }
        
        echo json_encode([
            'success' => true,
            'message' => "Merci pour votre retour. Votre avis sera visible dans quelques instants."
        ]);
        die();
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => "Nous rencontrons des problèmes serveur. Veuillez réessayer plus tard. " . $e->getMessage()
        ]);
        supprimer_image($fichier, $image);
        die();
    }


    function supprimer_image($fichier, $image) {
        if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
            if (file_exists('../ressources/avis/' . $fichier)){
                unlink('../ressources/avis/' . $fichier);
            } else if (file_exists(HOME_SITE . $image)){
                unlink(HOME_SITE . $image);
            }
        }
    }
?>
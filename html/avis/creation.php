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
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $id_produit = $_POST['produit'] ?? '';

    $image = "";

    $erreur = condition_avis();

    // Met l'image uploadée avec les autres pour éviter de la perdre
    if (isset($_FILES['image']) && isset($_FILES['image']['name'])) {
        $fichier = $id_compte . '_'. time();
        move_uploaded_file($_FILES['image']['tmp_name'], HOME_SITE . "ressources/avis/" . $fichier);

        switch($_FILES['image']['type']) {
            case "image/png":
                $ext = ".png";
                break;
            case "image/jpeg":
                $ext = ".jpg";
                break;
            case "image/webp":
                $ext = ".webp";
                break;
        }

        if ($_FILES['image']['size'] > 0) {
            // Nom à donner à l'image en cas de succès de l'upload
            $image = 'ressources/avis/' . $id_produit . '_' . $id_compte . $ext;
        }
    }

    // S'il y a des erreurs dans la soumission, fin du traitement
    if (!empty($erreur)) {
        echo json_encode([
            'success' => false,
            'message' => "L'avis n'a pas pu être créé. Veuillez réessayer plus tard."
        ]);
        supprimer_image($fichier);
        die();
    }

    try {
        // Si déjà créé par l'utilisateur, on le modifie, sinon on le crée
        if (check_avis_existe($id_produit, $id_compte)) {
            modifier_avis($id_compte, $id_produit, $note, $titre, $description, $image);
        } else {
            cree_avis($id_compte, $id_produit, $note, $titre, $description, $image);
        }
            
        // Tout est bon : on donne à l'image son nom définitif
        if ($image != "") {
            rename('../ressources/avis/' . $fichier, '../' . $image);
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
        supprimer_image($fichier);
        die();
    }


    function supprimer_image($fichier) {
        if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
            if (file_exists('../ressources/avis/' . $fichier)){
                unlink('../ressources/avis/' . $fichier);
            }
        }
    }
?>
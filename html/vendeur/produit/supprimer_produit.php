<?php
    // Permet d'utiliser le fichier .config.php
    define("HOME_GIT", "../../../");
    define("HOME_SITE", "../../");

    require_once HOME_GIT . '.config.php';
    require_once HOME_GIT . 'fonction_produit.php';

    if (!isset($_SESSION)) {
        session_start();
    }

    // Si je suis connecté mais pas en tant que vendeur, retour à l'accueil client
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && !isset($_SESSION['raison_sociale'])) {
        header('location: ' . HOME_SITE);
        exit;

    // Sinon si je ne suis pas connecté, retour à la page connexion vendeur
    } else if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] === false) {
        header('location: ../');
        exit;
    }

    if ($_GET == NULL || !isset($_GET['produit'])) {
       echo "Produit non trouvé";
       renvoi();
    }

    $id_produit = $_GET['produit'];
    try {
        $tab_promo = get_info_promotion($id_produit);
        foreach($tab_promo as $promo){
            delete_promotion($promo['id_promo']);
        }
        $tab_reduc = get_all_reduction_with_id_produit($id_produit);
        foreach($tab_reduc as $reduc){
            delete_reduction($reduc['id_reduction']);
        }
        supprimer_produit_stock($id_produit);
    } catch (PDOException $e) {
        die('Suppression du produit ' . $id_produit . ' impossible : ' . $e->getMessage());
    }

    header("Location: ../index.php");
    exit();
?>
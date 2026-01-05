<?php
    define('HOME_GIT', '../../../');
    define('HOME_SITE', '../../');

    if (!isset($_SESSION)) {
        session_start();
    }

    // Si connecté en vendeur, rediriger vers le stock, si connecté en client, rediriger vers l'accueil
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        header(isset($_SESSION['raison_sociale']) ? 'location: accueil' : 'location: ' . HOME_SITE);
    }

    if ($_POST != null) {
        $erreurs = [];
        $fichier = HOME_GIT . 'fonction_compte.php';
        
        if (file_exists($fichier)) {
            require_once $fichier;
            $erreurs = create_profile_vendeur($_POST['raisonSocial'], $_POST['numSiret'], $_POST['numCobrec'], $_POST['email'], $_POST['adresse'], $_POST['compAdresse'], $_POST['codePostal'], $_POST['mdp'], $_POST['mdpc'], HOME_GIT);
        } else {
            $erreurs['fatal'] = true;
        }
    }
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php include HOME_SITE . 'link_head.php'; ?>
        <title>Alizon - Accueil vendeur</title>
    </head>
    <body>
        
    </body>
</html>
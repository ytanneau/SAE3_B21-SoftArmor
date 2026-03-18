<?php 
    // appel du fichier de configuration bdd
    define('HOME_GIT', '../../../../');
    define('HOME_SITE', '../../../');

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

    // appel des fichiers de configuration et fonctions

    $id_compte = $_SESSION['id_compte'];
?>

<!DOCTYPE html>
<html>
    <head>
        <?php include HOME_SITE . 'link_head.php';?>
        <meta charset="UTF-8">
        <title>Alizon - Erreurs d'adresse</title>

    </head>
    <body>
        <?php include HOME_SITE . 'header.php'?>
        <main class="erreur_adress">
            <h1>Erreur - Adresse saisi inconnue</h1>
            <h4>Par défaut, l'ancienne adresse est consérver</h4>
            <a href="index.php">Modifier mon compte</a>
            <a href="../../accueil">Revenir à l'acceuil</a>
        </main>
        <?php include HOME_GIT . 'footer.php' ?>
    </body>
</html>
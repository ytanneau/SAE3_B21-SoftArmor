<?php
    // importer le fichier de connexion à la bdd
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
    require_once HOME_GIT . ".config.php";
    include HOME_GIT . "fonction_vendeur.php";  
    
    $id_compte = $_SESSION['id_compte'];

    // recuperation des informations du vendeur
    $tabVendeur = get_informations_vendeur($id_compte);

    // assignation des variables aux élements du tableau
    $raisonSociale = $tabVendeur['raison_sociale'];
    $numSiret = $tabVendeur['num_siret'];
    $id_adresse = $tabVendeur['id_adresse'];
    $description = $tabVendeur['description'];

    // recuperation des informations d'adresse du vendeur
    $tabAdresseVendeur = get_adresse_vendeur($id_adresse);

    // définiton de la chaine adresse
    $chaineAdresse = $tabAdresseVendeur['adresse'] . " " . $tabAdresseVendeur['code_postal'] . " " . $tabAdresseVendeur['complement_adresse'];
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <?php include HOME_SITE . 'link_head.php';?>
        <meta charset="UTF-8">
        <title>Consulter mes informations</title>
    </head>
    <body>
        <!-- inclusion du header -->
        <?php include "../../header.php"?>
        <main>
            <!-- Bouton de retour sur la page de gestion des stocks -->
            <a href="../../stock/index.php"><img src="../../../../image/retour.svg" alt="bouton retour en arrière"></a>
            <!-- Zone d'affichage des informations du vendeur -->
            <h1>Mes informations</h1>
            <h3>Raison sociale</h3>
            <p><?= $raisonSociale ?></p>
            <h3>Numero de siret</h3>
            <p><?= $numSiret ?></p>
            <h3>Adresse</h3>
            <p><?= $chaineAdresse ?></p>
            <h3>Description</h3>
            <p>
                <?php
                    if($description === null){
                        echo "Pas de description";
                    } else {
                        echo $description;
                    } 
                ?>
            </p>
            <!-- bouton pour etre rediriger vers la modification des informations du vendeur -->
            <button><a href="modification_informations_vendeur/index.php">Modifier mes informations</a></button>
            <!-- bouton pour etre rediriger vers la desactivation du compte vendeur -->
            <button><a href="modification_informations_vendeur/desactivation.php">Désactiver mon compte</a></button>
        </main>
        <?php include HOME_SITE . "footer.php" ?>
    </body>
</html>
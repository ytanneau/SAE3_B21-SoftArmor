<?php
    // importer le fichier de connexion à la bdd
    define('HOME_GIT', '../../../../');
    define('HOME_SITE', '../../../');

    require_once HOME_GIT . ".config.php";

    if (!isset($_SESSION)) {
        session_start();
    }
    
    // Si je suis connecté mais pas en tant que vendeur, retour à l'accueil client
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && !isset($_SESSION['raison_sociale'])) {
        header('location: ' . HOME_SITE);
        exit;

    // Sinon si je ne suis pas connecté, retour à la page connexion vendeur
    } else if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] === false) {
        header('location: ../../');
        exit;
    }
    
    $id_compte = $_SESSION['id_compte'];
    // requete pour recuperer les informations du vendeur
    $stmt = $pdo->prepare("SELECT * FROM _vendeur WHERE id_compte = :id_compte");
    $stmt->execute([':id_compte' => $id_compte]);

    // decoupage des informations en tableau
    $tabVendeur = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $tabVendeur = $tabVendeur[0];

    // assignation des variables aux élements du tableau
    $raisonSociale = $tabVendeur['raison_sociale'];
    $numSiret = $tabVendeur['num_siret'];
    $id_adresse = $tabVendeur['id_adresse'];
    $description = $tabVendeur['description'];

    // recuperation des informations d'adresse du vendeur
    $stmt = $pdo->prepare("SELECT * FROM _adresse WHERE id_adresse = :id_adresse");
    $stmt->execute([':id_adresse' => $id_adresse]);

    // decoupage des informations en tableau
    $tabAdresseVendeur = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $tabAdresseVendeur = $tabAdresseVendeur[0];

    // définiton de la chaine addresse
    $chaineAdresse = $tabAdresseVendeur['adresse'] . " " . $tabAdresseVendeur['code_postal'] . " " . $tabAdresseVendeur['complement_adresse'];
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Consulter mes informations</title>
    </head>
    <body>
        <?php include "../../header.php"?>
        <main>
            <!-- Zone d'affichage des informations du vendeur -->
            <h1>Mes informations</h1>
            <h3>Raison sociale</h3>
            <p><?php echo $raisonSociale ?></p>
            <h3>Numero de siret</h3>
            <p><?php echo $numSiret ?></p>
            <h3>Adresse</h3>
            <p><?php echo $chaineAdresse ?></p>
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
            <button><a href="modification_informations_vendeur/desactivation.php">Désactiver mon compte</a></button>
        </main>
        <footer>

        </footer>
    </body>
</html>
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
    require_once HOME_GIT . ".config.php";
    include HOME_GIT . "fonction_vendeur.php";

    $id_compte = $_SESSION['id_compte'];

    // recuperation des informations vendeur
    $tabVendeur = get_informations_vendeur($id_compte);

    // definition des variables suivant les valeurs du tableau
    $raisonSociale = $tabVendeur['raison_sociale'];
    $description = $tabVendeur['description'];
    $id_adresse = $tabVendeur['id_adresse'];

    // recuperation des informations d'adresse du vendeur
    $tabAdresseVendeur = get_adresse_vendeur($id_adresse);

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        // récupération des données du formulaire de saisie
        $modifRaisonSociale = $_POST['raison_sociale'];
        $modifAdresse = $_POST['adresse'];
        $modifCodePostal = $_POST['code_postal'];
        $modifCompelementAdr = $_POST['complementAdr'];
        $modifDescription = $_POST['description'];

        $_SESSION['raison_sociale'] = $modifRaisonSociale;

        // Mise à jour des informations dans la base de donnée
        update_informations_vendeur($modifRaisonSociale, $modifDescription, $id_compte);

        // mise à jour de l'adresse du vendeur
        update_adresse_vendeur($id_compte, $modifAdresse, $modifCodePostal, $modifCompelementAdr);

        // redirection vers la page precedente apres la validation du formulaire
        header('Location: ../');
        exit();
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <?php include HOME_SITE . 'link_head.php';?>
        <meta charset="UTF-8">
        <title>Mes informations</title>
    </head>
    <body>
        <!-- inclusion du header -->
        <?php include "../../header.php"?>
        <main class="mainInfoVendeur">
            <!-- Bouton de retour sur la page de gestion des stocks -->
            <a href="../../stock/"><img src="../../../../image/retour.svg" alt="bouton retour en arrière"></a>

            <h1>Modifier mes informations</h1>
            <em>Pour des raisons de securité, le numero de siret ne peut etre modifé</em>

            <!-- formulaire de saisie des modifications des informations d'un vendeur -->
            <form action="" name="formulaireModif" method="post" enctype="multipart/form-data">
                <p>
                    <label for="raison_sociale">Raison sociale</label>
                    <input type="text" name="raison_sociale" id="id_raison_sociale" value="<?= $raisonSociale ?>">
                    <label for="adresse">Adresse</label>
                    <input type="text" name="adresse" id="id_adresse" value="<?= $tabAdresseVendeur['adresse'] ?>">
                    <label for="code_postal">Code postal</label>
                    <input type="text" name="code_postal" id="id_code_postal" value="<?= $tabAdresseVendeur['code_postal'] ?>">
                    <label for="complementAdr">Complement d'adresse</label>
                    <input type="text" name="complementAdr" id="id_complementAdr" value="<?= $tabAdresseVendeur['complement_adresse'] ?>">
                    <label for="description">Description</label>
                    <textarea type="textarea" name="description" id="idDescSimple"><?= $description ?></textarea>
                </p>
                <input type="submit" value="Valider la modification" id="idValiderModifVendeur">
            </form>
            <a href="desactivation/desactivation.php" id="idDesactivationCompte">Desactiver le compte</a>
        </main>
        <footer>

        </footer>
    </body>
</html>
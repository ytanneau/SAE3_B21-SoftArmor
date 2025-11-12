<?php
const HOME_GIT = "../../../";

if (!isset($_POST['produit'])) {
    header("location: " . HOME_GIT, );
}

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['logged_in'])) {
    header("location: " . HOME_GIT, );
}


// gestion du POST des données adresse 
if ($_POST != null){
    if (!isset($_POST['adresse'])) $_POST['adresse'] = "";
    if (!isset($_POST['complement_adresse'])) $_POST['complement_adresse'] = "";
    if (!isset($_POST['code_postal'])) $_POST['code_postal'] = "";

    $fichier = HOME_GIT . 'fonction_compte.php';
    if (file_exists($fichier)) {
        require_once $fichier;
        $erreurs = check_coordonnees($_POST['adresse'], $_POST['code_postal']);

        // enregistrer
        if ($erreurs == [] && isset($_POST['enregistrer']) && $_POST['enregistrer']) {
            sql_insert_adresse_client($pdo, $_SESSION['id_compte'], $_POST['adresse'], $_POST['complement_adresse'], $_POST['code_postal']);
        }

    } else {
        // pas d'accès au fichier fonctions

        $erreurs['fatal'] = true;
        // $fichierLog = __DIR__ . "/erreurs.log";
        // $date = date("Y-m-d H:i:s");
        // file_put_contents($fichierLog, "[$date] Failed find : require_once $fichier;\n", FILE_APPEND);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
        <h1>Entrez vos coordonnées bancaires</h1>
        <form action="payement_reussi.php" method="post">

        <label for="code_carte">Code de carte bancaire</label>
        <input type="text" name="code_carte" id="code_carte" required>
        <p class="contrainte">ex: 1234 5678 9123 4567</p>
        <?php
        if (isset($erreurs['code_carte'])){
        ?>
        <p class="error">
            <?="Erreur : ".$erreurs['code_carte']?>
        </p>
        <?php
        }
        ?>

        <br>
        <label for="date_exp">Date d'expiration de la carte</label>
        <input type="date" name="date_exp" id="date_exp">
        <p class="contrainte">ex: 12/25</p>
        <?php
        if (isset($erreurs['date_exp'])){
        ?>
        <p class="error">
            <?="Erreur : ".$erreurs['date_exp']?>
        </p>
        <?php
        }
        ?>


        <br>
        <label for="code_securite">Code de sécurité</label>
        <input type="number" name="code_securite" id="code_securite" size="3" required>
        <p class="contrainte">Nombre à 3 chiffres</p>
        <?php
        if (isset($erreurs['code_securite'])){
        ?>
        <p class="error">
            <?="Erreur : ".$erreurs['code_securite']?>
        </p>
        <?php
        }
        ?>

        <br>
        <input type="submit" value="Effectuer l'achat">

        </form>
    </body>
</html>
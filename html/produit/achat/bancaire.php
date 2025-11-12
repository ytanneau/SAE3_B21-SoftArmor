<?php
const HOME_GIT = "../../../";

echo "test";

if (!isset($_POST['produit'])) {
    header("location: " . HOME_GIT, );
}

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['logged_in'])) {
    header("location: " . HOME_GIT, );
}


// gestion du POST
if (isset($_POST)) {
    if (!isset($_POST['code_carte'])) $_POST['code_carte'] = "";
    if (!isset($_POST['date_exp'])) $_POST['date_exp'] = "";
    if (!isset($_POST['code_securite'])) $_POST['code_securite'] = "";

    $fichier = HOME_GIT . 'fonction_compte.php';
    if (file_exists($fichier)) {
        require_once $fichier;
        $erreurs = check_coordonnees_bancaires($_POST['code_carte'], $_POST['date_exp'], $_POST['code_securite']);


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
        <form action="" method="post">

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
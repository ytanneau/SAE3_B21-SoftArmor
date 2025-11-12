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

// gestion du POST des données coordonnées bancaires
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

    if ($erreurs != []) {
        header("Location: " . "bancaire.php");
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
    <h1>Bravo vous avez réussi à payer !</h1>
</body>
</html>
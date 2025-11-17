<?php
    define('HOME_GIT', '../../');
    define('HOME_SITE', '../');

    if (!isset($_SESSION)) {
        session_start();

        if(isset($_SESSION['raison_sociale'])){
            header('location: '. HOME_SITE .'/vendeur/stock/');
        }
        else if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] === false) {
            header('location: '. HOME_SITE);
            exit;
        }
    }

    if (isset($_POST)){
        if (isset($_POST['note'])) $_POST['note'] = null;
        if (isset($_POST['titre'])) $_POST['titre'] = null;
        if (isset($_POST['description'])) $_POST['description'] = null;
        if (isset($_POST['image'])) $_POST['image'] = null;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alizon - Avis</title>
</head>
<body>
    
    <main>
        <form action="" method="post" media...>

            <label for="note">Note</label>
            <input type="range" name="note" id="note">

            <label for="titre">Titre</label>
            <input type="text" name="titre" id="titre">

            <label for="description">Description</label>
            <input type="text" name="description" id="description">

            <label for="image">Image</label>
            <input type="image" src="image" alt="image">

            <input type="submit" value="crée l'avie">
        </form>
    </main>

</body>
</html>
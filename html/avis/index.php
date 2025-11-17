<?php
    define('HOME_GIT', '../../');
    define('HOME_SITE', '../');
    const EXISTE = "Existe déjà";
    const EXISTE_PAS = "Existe pas";

    // verifie qu'il est connecter et est un compte client
    if (!isset($_SESSION)) {
        session_start();

        if(isset($_SESSION['raison_sociale'])){
            header('location: '. HOME_SITE .'/vendeur/stock/');
        }
        else if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] === false) {
            header('location: '. HOME_SITE);
        }
    }

    require_once HOME_GIT . 'fonction_avis.php';
    require_once HOME_GIT . 'fonction_produit.php';

    // verifie si le produit existe et si l'avis existe pas déjà, et recuper les info du produit
    if (isset($_GET['produit'])){
        if (check_avis_existe($_GET['produit'], $_SESSION['id_compte'])){
            $erreur['avis'] = EXISTE;
        }
        else{
            $sql_produit = detail_produit_image($_GET['produit']);
            if ($sql_produit == null){
                $erreur['produit'] = EXISTE_PAS;   
            }
        }
    }
    else{
        $_GET['produit'] = null;
        $erreur['produit'] = EXISTE_PAS; 
    }

    if (isset($_POST)){
        if (!isset($_POST['note'])) $_POST['note'] = null;
        if (!isset($_POST['titre'])) $_POST['titre'] = null;
        if (!isset($_POST['description'])) $_POST['description'] = null;
        if (!isset($_POST['image'])) $_POST['image'] = null;

        $id_avis = null; // A DEFINIR, DEBROUILLE toi YANN;


        if (isset($_POST['image'])) {
            move_uploaded_file($_FILES['image']['tmp_name'], HOME_GIT . "ressources/avis/" . $id_avis);
        }
    }

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alizon - Avis</title>
</head>
<body>
    
    <main>
        <form action="" method="post" media... enctype="multipart/form-data">
            <input type="text" 
                name="produit" 
                id="produit"
                value="<?=htmlentities($_GET['produit'])?>"
                required>

            <label for="note">Note</label>
            <input type="range" 
                name="note" 
                id="note"
                minlength="0"
                maxlength="5"
                required>

            <label for="titre">Titre</label>
            <input type="text" 
                name="titre" 
                id="titre">

            <label for="description">Description</label>
            <input type="text" 
                name="description" 
                id="description">

            <label for="image">Image</label>
            <input type="image" 
                name="image"
                src="image" 
                alt="image">

            <input type="submit" value="créer l'avis">
        </form>
    </main>

</body>
</html>
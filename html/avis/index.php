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

    if (isset($_FILES['image'])) {
        $fichier = $_SESSION['id_compte'] . '_'. time();
        move_uploaded_file($_FILES['image']['tmp_name'], HOME_GIT . "ressources/avis/" . $fichier);
    }

    require_once HOME_GIT . 'fonction_avis.php';
    require_once HOME_GIT . 'fonction_produit.php';
    echo 1;
    // verifie si le produit existe et si l'avis existe pas déjà, et recuper les info du produit
    if (isset($_GET['produit'])){
        if (check_avis_existe($_GET['produit'], $_SESSION['id_compte'])){
            $erreur['avis'] = EXISTE;
        }
        else{
            echo 1.5;
            $sql_produit = detail_produit_image($_GET['produit']);
            if ($sql_produit == null){
                $erreur['produit'] = EXISTE_PAS;   
            }
        }
    }
    //echo 2;
    else if (isset($_POST) && (!isset($erreur))){
        if (!isset($_POST['produit'])) $_POST['note'] = null;
        if (!isset($_POST['note'])) $_POST['note'] = null;
        if (!isset($_POST['titre'])) $_POST['titre'] = null;
        if (!isset($_POST['description'])) $_POST['description'] = null;
        if (!isset($_FILES['image'])) $image = null ?? $image = 'ressources/avis/'.$_GET['produit'].'_'.$_SESSION['id_compte'].'png';
        echo 3;

        if (condition_avis()){
            echo 4;
            if ($image != null){
                rename('ressources/avis/' . $fichier, $image);
            }
            echo 5;
            print_r($_POST);
            cree_avis($_SESSION['id_compte'], $_GET['produit'], $_POST['note'], $_POST['titre'], $_POST['description'], $image);
            $succes = true;
        }        
    }
    else{
        echo 6;
        $_GET['produit'] = null;
        $erreur['produit'] = EXISTE_PAS; 
    }

    
    function condition_avis(){
        if (!(1 <= $_POST['note'] && $_POST['note'] <= 5)){
            return false;
        }
        if (isset($_POST['description']) && !isset($_POST['titre'])){
            return false;
        }
        $sql_produit = detail_produit_image($_POST['produit']);
        if ($sql_produit == null){
            $erreur['produit'] = EXISTE_PAS;
            return false; 
        }
        return true;
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alizon - Avis</title>
    <?php include HOME_SITE . "link_head.php"; ?>
</head>
<body>
    <?php include HOME_SITE . "header.php"; ?>
    <main>
<?php
    if (isset($succes)){
?>
    <h1>Vous avis a été enregister</h1>
<?php
    }
    if (isset($erreur['avis'])){
?>
        <h1>Vous avez déjà donner votre avis</h1>
<?php
    }
    else if (isset($erreur['produit'])){
?>
        <h1>Le produit n'existe pas</h1>
<?php
    }
    else{
    /**<img src="<?=HOME_SITE . htmlentities($sql_produit['image_pricipale_url'])?>" alt="<?=htmlentities($sql_produit['image_pricipale_alt'])?>" title="<?=htmlentities($sql_produit['image_pricipale_tilte'])?>"> */
    //print_r($sql_produit)
?>
        <article>
            <h3><?=htmlentities($sql_produit['nom_public'])?></h3>
            
        </article>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" 
                value="<?=htmlentities(trim($_GET['produit']))?>"
                name="produit"
                id="produit">

            <label for="note">Note</label>
            <input type="range" 
                name="note" 
                id="note"
                min="1"
                max="5"
                step="1"
                value="5"
                oninput="output.value = this.value"
                required>
            <output id="output">5</output>

            <label for="titre">Titre</label>
            <input type="text" 
                name="titre" 
                id="titre">

            <label for="description">Description</label>
            <input type="text" 
                name="description" 
                id="description">

            <label for="image">Image</label>
            <input type="file" 
                name="image" 
                alt="image"
                accept=".png">

            <input type="submit" value="créer l'avis">
        </form>
<?php
    }
?>
    </main>

</body>
</html>
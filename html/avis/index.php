<?php
    define('HOME_GIT', '../../');
    define('HOME_SITE', '../');

    const EXISTE = "Existe déjà";
    const EXISTE_PAS = "Existe pas";
    const VIDE = "Veuillez renseigner ce champ";
    const DEPASSE = "Dépassement de champ";
    const FORMAT = "Le format est invalide";

    const TAILLE_TITRE = 100;
    const TAILLE_DESCIRPTION = 1000;
    const TAILLE_IMAGE = 5000000;

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

    //met le limage avec les autre pour éviter de la perdre
    if (isset($_FILES['image'])) {
        $fichier = $_SESSION['id_compte'] . '_'. time();
        move_uploaded_file($_FILES['image']['tmp_name'], HOME_SITE . "ressources/avis/" . $fichier);
    }

    require_once HOME_GIT . 'fonction_avis.php';
    require_once HOME_GIT . 'fonction_produit.php';
    
    $succes = false;
    // si le post a été envoyer
    if ($_POST != null){
        //print_r($_POST);
        //echo 1;
        if (!isset($_POST['produit'])) $_POST['produit'] = null;
        if (!isset($_POST['note'])) $_POST['note'] = null;
        if (!isset($_POST['titre'])) $_POST['titre'] = null;
        if (!isset($_POST['description'])) $_POST['description'] = null;
        //print_r($_FILES['image']);
        if ($_FILES['image'] == null){
            $image = null;
        }
        else{
            $image = 'ressources/avis/'.$_GET['id_produit'].'_'.$_SESSION['id_compte'].'.png';
        }
        if (($res = condition_avis()) == true){
            if ($image != null){
                rename('../ressources/avis/' . $fichier, '../' . $image);
            }
            
            //print_r($_POST);
            try{
                cree_avis($_SESSION['id_compte'], $_GET['id_produit'], $_POST['note'], $_POST['titre'], $_POST['description'], $image);
                $succes = true;
            }
            catch (PDOException $e){
                $erreur['fatal'] = true;
            }
        }        
    }
    else if (isset($_GET['id_produit'])){
        //echo 2;
        if (check_avis_existe($_GET['id_produit'], $_SESSION['id_compte'])){
            $erreur['avis'] = EXISTE;
        }
        else{
            $sql_produit = detail_produit_image($_GET['id_produit']);
            if ($sql_produit == null){
                $erreur['produit'] = EXISTE_PAS;   
            }
        }
    }    
    else{
        //echo 3;
        $_GET['id_produit'] = null;
        $erreur['produit'] = EXISTE_PAS; 
    }

    //supprimer l'image si la saugarede ne sai pas passer
    if ($succes != true && isset($_FILES['image'])){
        unlink('../ressources/avis/' . $fichier);
        unlink('../' . $image);
    }

    // fonction qui verife 
    function condition_avis(){
        $res = true;

        if (!isset($_POST['note'])){
            $res['note'] = VIDE;
        }
        else if (!(1 <= $_POST['note'] && $_POST['note'] <= 5)){
            $res['note'] = FORMAT;
        }

        if (isset($_POST['description']) && !isset($_POST['titre'])){
            $res['titre'] = "Une description a besoin d'un titre";
        }
        else if (strlen($_POST['titre']) > TAILLE_TITRE){
            $res['titre'] = DEPASSE;
        }

        if (strlen($_POST['description']) > TAILLE_DESCRIPTION){
            $res['description'] = DEPASSE;
        }

        if (preg_match("/png/",$_FILES['image']['type'])){
            $res['image'] = "Type de l'image";
        }
        else if ($_FILES['image']['size'] > TAILLE_IMAGE){
            $res['image'] = "Image trop lourd";
        }

        $sql_produit = detail_produit_image($_POST['produit']);
        if ($sql_produit == null){
            $res['produit'] = EXISTE_PAS;
        }

        return $res;
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
    if ($succes == true){
?>
    <h1>Vous avis a été enregister</h1>
<?php
    }
    else if (isset($erreur['fatal'])){
?>
        <h1>Nous tencontron des problème serveur</h1>
<?php
    }
    else if (isset($erreur['avis'])){
?>
        <h1>Vous avez déjà donner votre avis</h1>
<?php
    }
    else if (isset($erreur['produit']) || isset($res['produit'])){
?>
        <h1>Le produit n'existe pas</h1>
<?php
    }
    else{
    //print_r($sql_produit)
?>
        <a href="../produit?id_produit=<?=htmlentities($_GET['id_produit'])?>">
            <article>
                <h3><?=htmlentities($sql_produit['nom_public'])?></h3>
                <img src="<?=HOME_SITE . htmlentities($sql_produit['image_pricipale_url'])?>" alt="<?=htmlentities($sql_produit['image_pricipale_alt'])?>" title="<?=htmlentities($sql_produit['image_pricipale_tilte'])?>">
            </article>
        </a>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" 
                value="<?=htmlentities(trim($_GET['id_produit']))?>"
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
            <p class="error"><?=$res['note']?></p>

            <label for="titre">Titre</label>
            <input type="text" 
                name="titre" 
                id="titre">
            <p class="error"><?=$res['titre']?></p>

            <label for="description">Description</label>
            <input type="text" 
                name="description" 
                id="description">
            <p class="error"><?=$res['Description']?></p>

            <label for="image">Image</label>
            <input type="file" 
                name="image" 
                alt="image"
                accept=".png">
            <p class="error"><?=$res['Image']?></p>

            <input type="submit" value="créer l'avis">
        </form>
<?php
    }
?>
    </main>

</body>
</html>
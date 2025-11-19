<?php
    define('HOME_GIT', '../../');
    define('HOME_SITE', '../');

    define('EXISTE', 'Existe déjà');
    define('EXISTE_PAS', 'Existe pas');
    define('VIDE', 'Veuillez renseigner ce champ');
    define('DEPASSE', 'Dépassement de champ');
    define('FORMAT', 'Le format est invalide');

    define('TAILLE_TITRE', '100');
    define('TAILLE_DESCRIPTION', '1000');
    define('TAILLE_IMAGE', '5000000');

    ob_start();

    // verifie qu'il est connecter et est un compte client
    if (!isset($_SESSION)) {
        session_start();

        if(isset($_SESSION['raison_sociale'])){
            ob_end_flush();
            header('location: '. HOME_SITE .'/vendeur/stock/');
        }
        else if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] === false) {
            ob_end_flush();
            header('location: '. HOME_SITE);
        }
    }

    //met le limage avec les autre pour éviter de la perdre
    if ($_FILES['image']['size'] > 0) {
        $fichier = $_SESSION['id_compte'] . '_'. time();
        move_uploaded_file($_FILES['image']['tmp_name'], HOME_SITE . "ressources/avis/" . $fichier);
    }

    require_once HOME_GIT . 'fonction_avis.php';
    require_once HOME_GIT . 'fonction_produit.php';
    
    $succes = false;
    if (check_avis_existe($_GET['id_produit'], $_SESSION['id_compte'])){
        $erreur['avis'] = EXISTE;
    }

    else if ($_POST != null){
        if (!isset($_POST['produit'])) $_POST['produit'] = null;
        if (!isset($_POST['note'])) $_POST['note'] = null;
        if (!isset($_POST['titre'])) $_POST['titre'] = null;
        if (!isset($_POST['description'])) $_POST['description'] = null;

        if ($_FILES['image']['size'] == 0){
            $image = null;
        }
        else{
            $image = 'ressources/avis/'.$_GET['id_produit'].'_'.$_SESSION['id_compte'].'.png';
        }

        if (($erreur = condition_avis()) === []){
            if ($image != null){
                rename('../ressources/avis/' . $fichier, '../' . $image);
            }
            
            try {
                cree_avis($_SESSION['id_compte'], $_GET['id_produit'], $_POST['note'], $_POST['titre'], $_POST['description'], $image);
                $succes = true;
            }
            catch (PDOException $e){
                $erreur['fatal'] = true;
            }
        }        
    }
    else if (isset($_GET['id_produit'])){
        $sql_produit = detail_produit_image($_GET['id_produit']);

        if ($sql_produit == null){
            $erreur['produit'] = EXISTE_PAS;   
        }
    }    
    else{
        $_GET['id_produit'] = null;
        $erreur['produit'] = EXISTE_PAS; 
    }

    if ($succes === true) {
        ob_end_flush();
        header('location: ' . HOME_SITE . 'produit/?id_produit=' . $_GET['id_produit']);
    }

    // Supprimer l'image si la sauvegarde ne s'est pas passée
    /*
    if ($succes !== true && isset($_FILES['image']['name'])){
        unlink('../ressources/avis/' . $fichier);
        unlink('../' . $image);
    }
        */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alizon - Avis</title>
    <?php include HOME_SITE . "link_head.php"; ?>
</head>
<body class="form_client">
    <?php include HOME_SITE . "header.php"; ?>
    <main>
        <?php if ($succes == true) { ?>
            <h1>Votre avis a été enregistré</h1>
        <?php } else if (isset($erreur['fatal'])) { ?>
            <h1>Désolé, nous rencontrons des problèmes serveur</h1>
        <?php } else if (isset($erreur['avis'])) { ?>
            <h1>Vous avez déjà donné votre avis sur ce produit</h1>
        <?php } else if (isset($erreur['produit'])) { ?>
            <h1>Le produit n'existe pas</h1>
        <?php } else{ ?>
            <section>
                <a href="../produit?id_produit=<?=htmlentities($_GET['id_produit'])?>">
                    <article>
                        <h3><?= htmlentities($sql_produit['nom_public'] ?? '') ?></h3>
                        <img src="<?=HOME_SITE . htmlentities($sql_produit['image_principale_url'] ?? '')?>" alt="<?=htmlentities($sql_produit['image_principale_alt'] ?? '')?>" title="<?=htmlentities($sql_produit['image_principale_titre'] ?? '')?>">
                    </article>
                </a>
            </section>
            <section>
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" 
                        value="<?=htmlentities(trim($_GET['id_produit'] ?? ''))?>"
                        name="produit"
                        id="produit">

                    <label for="note">Note : <output id="output">5</output></label>
                    
                    <input type="range" 
                        name="note" 
                        id="note"
                        min="1"
                        max="5"
                        step="1"
                        value="5"
                        oninput="output.value = this.value"
                        required
                        class="champ">

                    <?php if (isset($erreur['note'])) { ?>
                        <p class="error">
                            <?="Erreur : ".$erreur['note']?>
                        </p>
                    <?php } ?>

                    <label for="titre">Titre</label>
                    <input type="text" 
                        name="titre" 
                        id="titre"
                        class="champ">

                    <?php if (isset($erreur['titre'])) { ?>
                        <p class="error">
                            <?="Erreur : ".$erreur['titre']?>
                        </p>
                    <?php } ?>

                    <label for="description">Description</label>
                    <input type="text" 
                        name="description" 
                        id="description"
                        class="champ text">

                    <?php if (isset($erreur['description'])) { ?>
                        <p class="error">
                            <?="Erreur : ".$erreur['description']?>
                        </p>
                    <?php } ?>

                    <label for="image">Image</label>
                    <input type="file" 
                        name="image" 
                        alt="image"
                        accept=".png">

                    <?php if (isset($erreur['image'])) { ?>
                        <p class="error">
                            <?="Erreur : ".$erreur['image']?>
                        </p>
                    <?php } ?>

                    <input type="submit" value="Créer l'avis" class="bouton">
                </form>
            </section>
        <?php } ?>
    </main>
</body>
</html>
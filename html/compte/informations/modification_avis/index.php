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

    //ob_start();

    // verifie qu'il est connecter et est un compte client
    if (!isset($_SESSION)) {
        session_start();

        if(isset($_SESSION['raison_sociale'])){
            //ob_end_flush();
            header('location: '. HOME_SITE .'/vendeur/stock/');
        }
        else if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] === false) {
            //ob_end_flush();
            header('location: '. HOME_SITE . 'compte/connexion');
        }
    }

    require_once HOME_GIT . 'fonction_avis.php';
    require_once HOME_GIT . 'fonction_produit.php';

    $erreur = [];
    $editMode = false;
    $avis_actuel = null;

    // Si un id_avis est fourni, on passe en mode modification
    if (isset($_GET['id_avis'])) {
        $_GET['id_avis'] = intval($_GET['id_avis']);
        if ($_GET['id_avis'] > 0) {
            try {
                $avis_actuel = obtenir_avis_par_id($_GET['id_avis']);
            } catch (PDOException $e) {
                $erreur['fatal'] = true;
            }

            if (!$avis_actuel) {
                $erreur['avis_introuvable'] = true;
            } else if (!isset($_SESSION['id_compte']) || intval($avis_actuel['id_client']) !== intval($_SESSION['id_compte'])) {
                $erreur['non_autorise'] = true;
            } else {
                $editMode = true;
                // Uniformiser: réutiliser la logique existante basée sur $_GET['produit']
                $_GET['produit'] = $avis_actuel['id_produit'];
            }
        } else {
            $erreur['avis_introuvable'] = true;
        }
    }

    if (isset($_GET['produit'])) {
        $_GET['produit'] = htmlentities(trim($_GET['produit']));
        $sql_produit = detail_produit_image($_GET['produit']);

        if ($sql_produit == null){
            $erreur['produit'] = EXISTE_PAS;

            //ob_end_flush();
            header('location: '. HOME_SITE);
        }
    }

    //met le limage avec les autre pour éviter de la perdre
    if (isset($_FILES['image']) && isset($_FILES['image']['name'])) {
        $fichier = $_SESSION['id_compte'] . '_'. time();
        move_uploaded_file($_FILES['image']['tmp_name'], HOME_SITE . "ressources/avis/" . $fichier);
    }

    
    
    $succes = false;
    // En création uniquement: empêcher un doublon
    if (!$editMode && check_avis_existe($_GET['produit'], $_SESSION['id_compte'])){
        $erreur['avis'] = EXISTE;
    }

    else if ($_POST != null){
        if (!isset($_POST['produit'])) $_POST['produit'] = null;
        if (!isset($_POST['note'])) $_POST['note'] = null;
        if (!isset($_POST['titre'])) $_POST['titre'] = null;
        if (!isset($_POST['description'])) $_POST['description'] = null;

        if ($_FILES['image']['size'] == 0){
            // Pas de nouvelle image: en modification on tente de conserver l'existante
            if ($editMode) {
                $chemin_existant = 'ressources/avis/'.$_GET['produit'].'_'.$_SESSION['id_compte'].'.png';
                if (file_exists('../' . $chemin_existant)) {
                    $image = $chemin_existant;
                } else {
                    $image = null;
                }
            } else {
                $image = null;
            }
        }
        else{
            $image = 'ressources/avis/'.$_GET['produit'].'_'.$_SESSION['id_compte'].'.png';
        }

        $erreur = condition_avis();
        if ($erreur === []) {
            if ($image != null){
                rename('../ressources/avis/' . $fichier, '../' . $image);
            }
            
            try {
                if ($editMode) {
                    // Remplacer l'avis: suppression puis recréation avec les nouvelles valeurs
                    supprimer_avis($_GET['produit'], $_SESSION['id_compte']);
                    cree_avis($_SESSION['id_compte'], $_GET['produit'], $_POST['note'], $_POST['titre'], $_POST['description'], $image);
                } else {
                    cree_avis($_SESSION['id_compte'], $_GET['produit'], $_POST['note'], $_POST['titre'], $_POST['description'], $image);
                }
                $succes = true;
            }
            catch (PDOException $e){
                echo $e->getMessage();
                $erreur['fatal'] = true;
            }
        }        
    }

    /*
    if ($succes === true) {
        header('location: ' . HOME_SITE . 'produit/?produit=' . $_GET['produit']);
    }*/

    // Supprimer l'image si la sauvegarde ne s'est pas passée
    
    if ($succes !== true && isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        if (file_exists('../ressources/avis/' . $fichier)){
            unlink('../ressources/avis/' . $fichier);
        }
        else if (file_exists(HOME_SITE . $image)){
            unlink(HOME_SITE . $image);
        }
        
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
<body class="form_client" id="create_avis">
    <?php include HOME_SITE . "header.php"; ?>
    <main class = "content_avis_client">
        <?php if ($succes == true) { ?>
            <div class="fini">
                <h1><?= $editMode ? 'Votre avis a été modifié' : 'Votre avis a été enregistré' ?></h1>                
                <br>
                <a href="../produit?produit=<?=htmlentities($_GET['produit'])?>">Retour au produit</a>
            </div>
        <?php } else if (isset($erreur['fatal'])) { ?>
            <div class="fini">
                <h1>Désolé, nous rencontrons des problèmes serveur</h1>
                <br>
                <a href="../produit?produit=<?=htmlentities($_GET['produit'])?>">Retour au produit</a>
            </div>
        <?php } else if (isset($erreur['avis_introuvable']) || isset($erreur['non_autorise'])) { ?>
            <div class="fini">
                <h1><?= isset($erreur['non_autorise']) ? "Vous n'êtes pas autorisé à modifier cet avis" : "L'avis demandé est introuvable" ?></h1>
                <br>
                <a href="../">Retour à mon compte</a>
            </div>
        <?php } else if (isset($erreur['avis'])) { ?>
            <div class="fini">
                <h1>Vous avez déjà donné votre avis sur ce produit</h1>
                <br>
                <a href="../produit?produit=<?=htmlentities($_GET['produit'])?>">Retour au produit</a>
            </div>
        <?php } else if (isset($erreur['produit'])) { ?>
            <h1>Le produit n'existe pas</h1>
        <?php } else{ ?>
            <section>
                <a href="../produit?produit=<?=htmlentities($_GET['produit'])?>">
                    <article>
                        <h3><?= htmlentities($sql_produit['nom_public'] ?? '') ?></h3>
                        <img src="<?=HOME_SITE . htmlentities($sql_produit['image_principale_url'] ?? '')?>" alt="<?=htmlentities($sql_produit['image_principale_alt'] ?? '')?>" title="<?=htmlentities($sql_produit['image_principale_titre'] ?? '')?>">
                    </article>
                </a>
            </section>
            <section>
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" 
                        value="<?=htmlentities(trim($_GET['produit'] ?? ''))?>"
                        name="produit"
                        id="produit">
                    <?php 
                        $pref_note = isset($_POST['note']) ? $_POST['note'] : ($editMode ? ($avis_actuel['note'] ?? 5) : 5);
                        $pref_titre = isset($_POST['titre']) ? $_POST['titre'] : ($editMode ? ($avis_actuel['titre'] ?? '') : '');
                        $pref_desc = isset($_POST['description']) ? $_POST['description'] : ($editMode ? ($avis_actuel['commentaire'] ?? ($avis_actuel['description'] ?? '')) : '');
                    ?>

                    <label for="note">Note : <output id="output"><?= htmlentities($pref_note) ?></output></label>
                    
                    <input type="range" 
                        name="note" 
                        id="note"
                        min="1"
                        max="5"
                        step="1"
                        value="<?= htmlentities($pref_note) ?>"
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
                        value="<?= htmlentities($pref_titre) ?>"
                        class="champ">

                    <?php if (isset($erreur['titre'])) { ?>
                        <p class="error">
                            <?="Erreur : ".$erreur['titre']?>
                        </p>
                    <?php } ?>

                    <label for="description">Description</label>
                    <textarea class="champ text" name="description" id="description" class="champ text"><?php
                        echo trim($pref_desc);
                    ?></textarea>

                    <?php if (isset($erreur['description'])) { ?>
                        <p class="error">
                            <?="Erreur : ".$erreur['description']?>
                        </p>
                    <?php } ?>
                    
                        <label for="image" class="image_bouton">Ajouter une image 
                            <p id="image-name">Aucun fichier choisi</p>
                        </label>
                    <input id="image"
                        type="file" 
                        name="image" 
                        alt="image"
                        accept="image/png"
                        hidden>

                    <?php if (isset($erreur['image'])) { ?>
                        <p class="error">
                            <?="Erreur : ".$erreur['image']?>
                        </p>
                    <?php } ?>

                    <input type="submit" value="<?= $editMode ? "Modifier l'avis" : "Créer l'avis" ?>" class="bouton">
                </form>
            </section>
        <?php } ?>
    </main>
    <?php include HOME_SITE . "footer.php" ?>
    <script>
        const fileInput = document.getElementById("image");
        const fileName = document.getElementById("image-name");

        fileInput.addEventListener("change", () => {
            fileName.textContent = fileInput.files.length > 0 
            ? fileInput.files[0].name 
            : "Aucun fichier choisi";
        });
    </script>
</body>
</html>
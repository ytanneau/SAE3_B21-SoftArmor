<?php
//racine
define('HOME_GIT', "../../../");
define('HOME_SITE', '../../');

//taille max de pdp
define('MAX_SIZE', 2 * 1024 * 1024);

// lance la session et si il n'est pas connecté est renvoyé a la page d'accueil
if (!isset($_SESSION)) {
    session_start();
    if(isset($_SESSION['raison_sociale'])){
        header('location: '.HOME_GIT.'vendeur/stock/');
    }
    if (!isset($_SESSION['logged_in'])) {
        header('location: ../../');
        exit;
    }
}

require_once (HOME_GIT . '.config.php');
require_once (HOME_GIT . 'fonction_produit.php');
require_once (HOME_GIT . 'fonction_compte.php');
require_once (HOME_GIT . 'fonction_global.php');
require_once (HOME_GIT . 'fonction_avis.php');

//requete pour recuperer mot de passe cryptée et id adresse
//$sql = "SELECT mdp,id_adresse FROM compte_client WHERE id_compte = {$_SESSION['id_compte']};";

$mot_de_passe= sql_get_infos_randoms($_SESSION['id_compte']);

//requete pour recuperer informations du compte sans l'adresse
//$sql = "SELECT * FROM compte_client LEFT JOIN compte_image_profil ON compte_client.id_compte = compte_image_profil.id_compte WHERE compte_client.id_compte = {$_SESSION['id_compte']};";    

$info_compte = sql_get_info_compte($_SESSION['id_compte']);

//requete pour recuperer l'adresse du compte
//$sql = "SELECT * FROM client_adresse WHERE client_adresse.id_compte = {$_SESSION['id_compte']};";

$adresse_compte = sql_get_adresse_compte($_SESSION['id_compte']);

//recuperer les avis du compte
$avis = tout_avis_client($_SESSION['id_compte']);


//recupere le mdp crypté et l'id de l'adresse du client
foreach ($mot_de_passe as $row){
    $mdp_cryptee = $row['mdp'];
    $id_adresse = $row['id_adresse'];
}

//requete pour savoir si il y a une image de profil
//$sql ="SELECT * FROM _image inner join _compte on _image.id_image = _compte.id_image_profil where _compte.id_compte = {$_SESSION['id_compte']};";

$possede_image = sql_get_img_profil($_SESSION['id_compte']);

//traitement de la modification des informations
if ($_POST != NULL){

    //initialise les vaiables a ""
    if (!isset($_POST['pseudo'])) $_POST['pseudo'] = "";
    if (!isset($_POST['nom'])) $_POST['nom'] = "";
    if (!isset($_POST['prenom'])) $_POST['prenom'] = "";
    if (!isset($_POST['email'])) $_POST['email'] = "";
    if (!isset($_POST['adresse'])) $_POST['adresse'] = "";
    if (!isset($_POST['code_postal'])) $_POST['code_postal'] = "";
    if (!isset($_POST['complement_adresse'])) $_POST['complement_adresse'] = "";
    if (!isset($_POST['mdp'])) $_POST['mdp'] = "";
    if (!isset($_POST['n_mdp'])) $_POST['n_mdp'] = "";
    if (!isset($_POST['n_mdpc'])) $_POST['n_mdpc'] = "";

    //initialise une date valide car je ne veut pas refaire une fonction identique ou on ne verifie pas la date de naissance
    $date='01-01-2000';
    
    //check les erreur de saisies
    $erreur = check_erreur_client($_POST['nom'], $_POST['prenom'], $_POST['pseudo'], $_POST['email'],$date, $_POST['n_mdp'], $_POST['n_mdpc'], $_POST['adresse'], $_POST['code_postal']);
    
    //verifie si email n'est pas changé
    $ancien_mail=sql_get_email($_SESSION['id_compte']);

    if($_POST['email'] == $ancien_mail[0]['email']){
        $erreur['email']=NULL;
    }

    //verifie que les condition de l'insertin sont remplies
    if((check_crypte_MDP($_POST['mdp'] ,$mdp_cryptee) 
        && !check_vide($_POST['mdp'])) 
        && !(empty($erreur['code_postal']) xor empty($erreur['adresse'])) 
        && !(empty($erreur['mdp']) xor empty($erreur['mdpc'])) 
        && (!isset($erreur['nom'])) 
        && (!isset($erreur['prenom']))
        && (!isset($erreur['email']))
        && (!isset($erreur['pseudo']))){

        //update la BDD
        sql_update_client($pdo ,$_POST['nom'],$_POST['prenom'],$_POST['pseudo'],$_POST['email'],$_POST['adresse'],$_POST['code_postal'],$_POST['complement_adresse'],$_POST['n_mdp'], $_SESSION['id_compte'],$id_adresse);
        
        //modifie la photo de profil
        $id=$_SESSION['id_compte'];
        $ext=".png";
        $dossier= "../../ressources/client/";
        $chemin = "'ressources/client/".$id.$ext."'";
        $file_name = $dossier.$id . $ext;
        $titre="'Image de Profil'";
        $alt="'Image de Profil'";
        if ($_FILES!=NULL) {
            if(!$_FILES["pdp"]["error"]){
                if ($_FILES["pdp"]["size"] < MAX_SIZE) {
                    move_uploaded_file($_FILES["pdp"]["tmp_name"],$dossier.$id.$ext);

                    $est_entre_img= false;
                    
                    foreach ($possede_image as $row){ 
                        $est_entre_img=true;
                    }
                    if($est_entre_img){
                        //met a jour les données de l'image de profil
                        $sql="UPDATE _compte INNER JOIN _image ON _compte.id_image_profil = _image.id_image SET url_image={$chemin}, alt={$alt}, titre={$titre} WHERE _compte.id_compte = {$_SESSION['id_compte']};";
                    }
                    else {
                        //insere l'image de profil dans _image
                        $sql="INSERT INTO _image (url_image,titre,alt) VALUES ({$chemin},{$titre},{$alt});";
                        $pdo->query($sql);

                        //recupere l'id de l'image inséré
                        $sql="SELECT id_image FROM _image WHERE url_image = {$chemin}";
                        $recup_id_image = $pdo->query($sql);

                        foreach ($recup_id_image as $row){ 
                            $id_image = $row['id_image'];
                        }

                        //met a jour _compte pour dire quil y a une image de profil
                        $sql="UPDATE _compte SET id_image_profil = {$id_image} WHERE id_compte={$id}";
                    }
                    $pdo->query($sql);
                }
            }
        }
        //actualise la session
        $_SESSION['pseudo'] = $_POST['pseudo'];

        //vide les variables globales
        $_POST = null;
        $_FILES = null;
        
        //refresh la page pour afficher les infos
        header("Refresh:0");
    
    }
}
// Fermer la connexion
unset($pdo);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informations Compte</title>
    <?php include HOME_SITE . 'link_head.php'; ?>
    <script src="confirmation.js"></script>
</head>
<body class="infos">
    <?php include HOME_SITE . 'header.php'; ?>

    <h1>Mon Profil</h1>
    <main>
        <section>
            <?php
                //affichage des info du compte
                foreach ($info_compte as $row){  
            ?>


            <form action="" method="post" id="donnee" enctype="multipart/form-data">
                
                <article>
                    <img src="<?= htmlentities("../../".$row['url_image'] ?? 'url')?>" alt="<?= htmlentities($row['alt_image'] ?? '')?>" title="<?= htmlentities($row['titre_image'] ?? '')?>">
                    <label for="pdp">Modifier Image de Profil</label>
                    <input type="file" name="pdp" accept=".png">
                </article>

                <article>
                    <label for="pseudo">Pseudonyme</label>
                    <input type="text" name="pseudo" value="<?= htmlentities($row['pseudo'] ?? '')?>" placeholder="À renseigner" class="champ">

                    <!--Erreur pseudo-->
                    <?php
                        if (isset($erreur['pseudo'])){
                    ?>
                        <p class="error">
                            <?="Erreur : ".$erreur['pseudo']?>
                        </p>
                    <?php
                        }
                    ?>

                    <label for="nom">Nom</label>
                    <input required type="text" name="nom" value="<?= htmlentities($row['nom'] ?? '')?>" placeholder="À renseigner" class="champ">

                    <!--Erreur nom-->
                    <?php
                        if (isset($erreur['nom'])){
                    ?>
                        <p class="error">
                            <?="Erreur : ".$erreur['nom']?>
                        </p>
                    <?php
                        }
                    ?>

                    <label for="prenom">Prenom</label>
                    <input required type="text" name="prenom" value="<?= htmlentities($row['prenom'] ?? '')?>" placeholder="À renseigner" class="champ">

                    <!--Erreur prenom-->
                    <?php
                        if (isset($erreur['prenom'])){
                    ?>
                        <p class="error">
                            <?="Erreur : ".$erreur['prenom']?>
                        </p>
                    <?php
                        }
                    ?>

                    <label for="date">Date de Naissance</label>
                    <label name="date"><?= date("m/d/Y", strtotime(htmlentities($row['date_naissance'] )?? ''))?></label>

                    <label for="mail">Mail</label>
                    <input required type="email" name="email" value="<?= htmlentities($row['email'] ?? '')?>" placeholder="À renseigner" class="champ">
                    
                    <!--Erreur mail-->
                    <?php
                        if (isset($erreur['email'])){
                    ?>
                        <p class="error">
                            <?="Erreur : ".$erreur['email']?>
                        </p>
                    <?php
                        }
                    }
                    ?>

                    <label for="adresse">Adresse</label>

                    <?php
                    //affichage des info du compte
                    $est_entre = false;
                    foreach ($adresse_compte as $row){  
                        $est_entre = true;
                    ?>
                    
                    <input type="text" name="adresse" value="<?= htmlentities($row['adresse'] ?? '')?>" placeholder="À renseigner" class="champ">

                    <!--Erreur adresse-->
                    <?php
                        if (isset($erreur['adresse']) && $erreur['adresse'] != "Veuillez renseigner ce champ"){
                    ?>
                        <p class="error">
                            <?="Erreur : ".$erreur['adresse']?>
                        </p>
                    <?php
                        }
                    ?>

                    <label for="complement_adresse">Complement Adresse</label>
                    <input type="text" name="complement_adresse" value="<?= htmlentities($row['complement_adresse'] ?? '')?>" placeholder="À renseigner" class="champ text">
                    
                    <label for="code_postal">Code Postal</label>
                    <input type="text" name="code_postal" value="<?= htmlentities($row['code_postal'] ?? '')?>" placeholder="À renseigner" class="petit champ">
                    
                    <!--Erreur code postal-->
                    <?php
                        if (isset($erreur['code_postal']) && $erreur['code_postal'] != "Veuillez renseigner ce champ"){
                    ?>
                        <p class="error">
                            <?="Erreur : ".$erreur['code_postal']?>
                        </p>
                    <?php
                        }
                    }
                    if (!$est_entre) {
                        ?>
                    
                    <input type="text" name="adresse" placeholder="À renseigner" class="champ">

                    <!--Erreur adresse-->
                    <?php
                        
                        if (isset($erreur['adresse']) && $erreur['adresse'] != "Veuillez renseigner ce champ"){
                            
                    ?>  
                        <p class="error">
                            <?="Erreur : ".$erreur['adresse']?>
                        </p>
                    <?php
                        }
                    ?>

                    <label for="complement_adresse">Complement Adresse</label>
                    <input type="text" name="complement_adresse" placeholder="À renseigner" class="champ text">

                    <label for="code_postal">Code Postal</label>
                    <input type="text" name="code_postal" placeholder="À renseigner" class="petit champ">

                    <!--Erreur code postal-->
                    <?php
                        if (isset($erreur['code_postal']) && $erreur['code_postal'] != "Veuillez renseigner ce champ"){
                    ?>
                                <p class="error">   
                                    <?="Erreur : ".$erreur['code_postal']?>
                                </p>
                    <?php
                        }
                    }
                    if (empty($erreur['code_postal']) xor empty($erreur['adresse'])){
                    ?>
                        <p class="error">
                            <?= "Les deux champs Adresse et Code Postal doivent être valides" ?>
                        </p>
                    <?php
                        }
                    ?>
                    
                    <label for="mdp">Mot de Passe</label>
                    <input type="password" name="mdp" placeholder="À renseigner" class="champ">
                    <!--Erreur mot de passe-->
                    <?php
                        
                        if (isset($erreur['mdp'])){
                            
                    ?>  
                        <p class="error">
                            <?="Erreur : ".$erreur['mdp']?>
                        </p>
                    <?php
                        }
                    ?>
                
                    <label for="n_mdp">Nouveau Mot de Passe</label>
                    <input type="password" name="n_mdp" placeholder="À renseigner" class="champ">

                    <!--Erreur nouveau mot de passe-->
                    <?php
                        if (isset($erreur['mdp']) && $erreur['mdp'] != "Veuillez renseigner ce champ"){
                    ?>
                                <p class="error">
                                    <?="Erreur : ".$erreur['mdp']?>
                                </p>
                    <?php
                        }
                    ?>

                    <label for="n_mdpc">Confirmer Nouveau Mot de Passe</label>
                    <input type="password" name="n_mdpc" placeholder="À renseigner" class="champ">

                    <!--Erreur confirmation nouveau mot de passe-->
                    <?php
                        if (isset($erreur['mdpc']) && $erreur['mdpc'] != "Veuillez renseigner ce champ"){
                    ?>
                                <p class="error">
                                    <?="Erreur : ".$erreur['mdpc']?>
                                </p>
                    <?php
                        }
                    if (empty($erreur['mdp']) xor empty($erreur['mdpc'])){
                    ?>
                                <p class="error">
                                    <?= "Les deux champs Nouveau Mot de Passe doivent être valides" ?>
                                </p>
                    <?php
                        }
                    ?>

                    <button type="submit" class="bouton">Modifier mes informations</button>
                    <button class="bouton grave"><a href="anonymisation_client/index.php">Désactiver mon compte</a></button>
                </article>
            </form>

        </section>
            
        <section>
            <h2>Vos Avis</h2>
            <ul>
                <?php
                //boucle afficher les avis du comte dans un <li>
                foreach ($avis as $row){  
                ?>
                <li class="avis">
                    <a href="/produit/index.php?id_produit=<?= $row['id_produit']?>">
                        <table>
                            <tr>
                            <td><img src="<?= HOME_SITE.$row['url_pdp'];?>" alt="<?= htmlentities($row['alt_pdp'] ?? '')?>" title="<?= htmlentities($row['titre_pdp'] ?? '')?>">
                            <p><?= htmlentities($row['pseudo'] ?? '')?></p></td>
                            <td><?php afficher_moyenne_note($row['note']);?></td>
                            <td><p><?= htmlentities($row['titre'] ?? '')?></p>  </td>
                            <td colspan="2" rowspan="2"><img src="<?= HOME_SITE.$row['url_img']?>" alt="<?= $row['alt_img']?>" title="<?= $row['titre_img']?>"></td>
                            </tr>

                            <tr>
                            <td><p><?= "Avis publié le " .  date("m/d/Y", strtotime(htmlentities($row['date_avis'] )?? ''))?></p></td>
                            <td colspan="2"><p><?= htmlentities($row['commentaire'] ?? '')?></p></td>
                            </tr>
                        </table>
                    </a>
                </li>
                <?php } ?>
            </ul>
        </section>
    </main>
</body>
</html>
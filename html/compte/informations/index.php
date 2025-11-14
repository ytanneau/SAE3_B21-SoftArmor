<?php
//racine
define('HOME_GIT', "../../../");
define('HOME_SITE', '../../');

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

//requete pour recuperer mot de passe cryptée
$sql = "SELECT mdp,id_adresse FROM compte_client WHERE id_compte = {$_SESSION['id_compte']};";

$mot_de_passe= $pdo->query($sql);

//requete pour recuperer informations du compte sans l'adresse
$sql = "SELECT * FROM compte_client LEFT JOIN compte_image_profil ON compte_client.id_compte = compte_image_profil.id_compte WHERE compte_client.id_compte = {$_SESSION['id_compte']};";    

$info_compte = $pdo->query($sql);

//requete pour recuperer l'adresse du compte
$sql = "SELECT * FROM client_adresse WHERE client_adresse.id_compte = {$_SESSION['id_compte']};";

$adresse_compte = $pdo->query($sql);

//rrecuperer les avis du compte
$avis = tout_avis_client($_SESSION['id_compte']);


//recupere le mdp crypté et l'id de l'adresse du client
foreach ($mot_de_passe as $row){
    $mdp_cryptee = $row['mdp'];
    $id_adresse = $row['id_adresse'];
}

//requete pour savoir si il y a une image de profil
$sql ="SELECT * FROM _image inner join _compte on _image.id_image = _compte.id_image_profil where _compte.id_compte = {$_SESSION['id_compte']};";

$possede_image = $pdo->query($sql);

//traitement de la modification des informations
if ($_POST != null){

    //initialise les vaiables a ""
    if (!isset($_POST['pseudo'])) $_POST['pseudo'] = "";
    if (!isset($_POST['nom'])) $_POST['nom'] = "";
    if (!isset($_POST['prenom'])) $_POST['prenom'] = "";
    if (!isset($_POST['email'])) $_POST['email'] = "";
    if (!isset($_POST['date'])) $_POST['date'] = "";
    if (!isset($_POST['adresse'])) $_POST['adresse'] = "";
    if (!isset($_POST['code_postal'])) $_POST['code_postal'] = "";
    if (!isset($_POST['complement_adresse'])) $_POST['complement_adresse'] = "";
    if (!isset($_POST['mdp'])) $_POST['mdp'] = "";
    if (!isset($_POST['n_mdp'])) $_POST['n_mdp'] = "";
    if (!isset($_POST['n_mdpc'])) $_POST['n_mdpc'] = "";

    //check les erreur de saisies
    $erreur = check_erreur_client($_POST['nom'], $_POST['prenom'], $_POST['pseudo'], $_POST['email'],$_POST['date'], $_POST['n_mdp'], $_POST['n_mdpc'], $_POST['adresse'], $_POST['code_postal']);
    
    //verifie que les condition de l'insertin sont remplies
    if((check_crypte_MDP($_POST['mdp'] ,$mdp_cryptee) 
        && !check_vide($_POST['mdp'])) 
        && !(empty($erreur['code_postal']) xor empty($erreur['adresse'])) 
        && !(empty($erreur['mdp']) xor empty($erreur['mdpc'])) 
        && (!isset($erreur['nom'])) 
        && (!isset($erreur['prenom']))
        && (!isset($erreur['email']))
        && (!isset($erreur['pseudo']))
        && (!isset($erreur['date_naiss']))){

        //update la BDD
        sql_update_client($pdo ,$_POST['nom'],$_POST['prenom'],$_POST['pseudo'],$_POST['email'],$_POST['date'],$_POST['adresse'],$_POST['code_postal'],$_POST['complement_adresse'],$_POST['n_mdp'], $_SESSION['id_compte'],$id_adresse);
        
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
                    $sql="INSERT INTO _image VALUES ({$chemin},{$titre},{$alt});";
                    $pdo->query($sql);

                    //recupere l'id de l'image inséré
                    $sql="SELECT id_image FROM _image WHERE url_image = {$chemin}";
                    $recup_id_image = $pdo->query($sql);

                    foreach ($recup_id_image as $row){ 
                       $id_image = $row['id_image'];
                    }

                    //met a jour _compte pour dire quil y a une image de profil
                    $sql="UPDATE _compte SET id_image_profil = {$id_image}";
                }
                $pdo->query($sql);
            }
        }

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
<body>
    <?php include HOME_SITE . 'header.php'; ?>
    
    <main>
        <h1>Mon Profil</h1>
        <div>
            <?php
                //affichage des info du compte
                foreach ($info_compte as $row){  
            ?>

            <img src="<?= pset("../../".$row['url_image'])?>" alt="<?= pset(['alt_image'])?>" title="<?= pset(['titre_image'])?>">

            <form action="" method="post" id="donnee" enctype="multipart/form-data">
                
                <label for="pdp">Modifier Image de Profil</label>
                <input type="file" name="pdp" accept=".png">

                <label for="pseudo">Pseudonyme</label>
                <input type="text" name="pseudo" value="<?= pset(['pseudo'])?>" placeholder="À renseigner">

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
                <input required type="text" name="nom" value="<?= pset(['nom'])?>" placeholder="À renseigner">

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
                <input required type="text" name="prenom" value="<?= pset(['prenom'])?>" placeholder="À renseigner">

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
                <input required type="date" name="date" value="<?= pset(['date_naissance'])?>" placeholder="À renseigner">
                
                <!--Erreur Date-->
                <?php
                    if (isset($erreur['date_naiss'])){
                ?>
                    <p class="error">
                        <?="Erreur : ".$erreur['date_naiss']?>
                    </p>
                <?php
                    }
                ?>

                <label for="mail">Mail</label>
                <input required type="email" name="email" value="<?= pset(['email'])?>" placeholder="À renseigner">
                
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
                
                <input type="text" name="adresse" value="<?= pset(['adresse'])?>" placeholder="À renseigner">

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
                <input type="text" name="complement_adresse" value="<?= pset(['complement_adresse'])?>" placeholder="À renseigner">
                
                <label for="code_postal">Code Postal</label>
                <input type="text" name="code_postal" value="<?= pset(['code_postal'])?>" placeholder="À renseigner">
                
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
                
                <input type="text" name="adresse" placeholder="À renseigner">

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
                <input type="text" name="complement_adresse" placeholder="À renseigner">

                <label for="code_postal">Code Postal</label>
                <input type="text" name="code_postal" placeholder="À renseigner">

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
                <input type="password" name="mdp" placeholder="À renseigner">
            
                <label for="n_mdp">Nouveau Mot de Passe</label>
                <input type="password" name="n_mdp" placeholder="À renseigner">

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
                <input type="password" name="n_mdpc" placeholder="À renseigner">

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

                <button type="submit">Modifier mes informations</button>
            </form>
            
        </div>
        <div>
            <h2>Vos Avis</h2>
            <div>
                <ul>
                    <?php
                    //boucle afficher les avis du comte dans un <li>
                    foreach ($avis as $row){  
                    ?>
                    <li>
                        <div>
                            <div>
                                <img src="<?= "../../".$row['url_image'];?>" alt="<?= pset(['alt_image'])?>" title="<?= pset(['titre_image'])?>">
                                <p><?= pset(['pseudo'])?></p>
                                <?php afficher_moyenne_note($row['note']);?>
                            </div>
                            <div>
                                <p><?= pset($row['titre'])?></p>  
                                <p><?= pset($row['commentaire'])?></p>
                                <p><?= pset("Avis publié le " . $row['date_avis'])?></p>
                            </div>
                        </div>
                    </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </main>
</body>
</html>
<?php

define('HOME_GIT', "../../../");
// lance la session et si il n'est pas connecté est renvoyé a la page d'accueil
if (!isset($_SESSION)) {
    session_start();
    if (!isset($_SESSION['logged_in'])) {
        header('location: ../../' );
        exit;
    }
}

require_once (HOME_GIT . '.config.php');
require_once (HOME_GIT . 'fonction_produit.php');
require_once (HOME_GIT . 'fonction_compte.php');
//requete pour recuperer mot de passe cryptée
$sql = "SELECT mdp,id_adresse FROM compte_client WHERE id_compte = {$_SESSION['id_compte']};";

$mot_de_passe= $pdo->query($sql);

//requete pour recuperer informations du compte sans l'adresse
$sql = "SELECT * FROM compte_client LEFT JOIN compte_image_profil ON compte_client.id_compte = compte_image_profil.id_compte WHERE compte_client.id_compte = {$_SESSION['id_compte']};";    

$info_compte = $pdo->query($sql);

//requete pour recuperer l'adresse du compte
$sql = "SELECT * FROM client_adresse WHERE client_adresse.id_compte = {$_SESSION['id_compte']};";

$adresse_compte = $pdo->query($sql);

//requete pour recuperer les avis du compte
$sql="SELECT pseudo,date_avis,note,titre,commentaire,url_image,titre_image,alt_image FROM compte_client INNER JOIN _avis ON compte_client.id_compte = _avis.id_client LEFT JOIN compte_image_profil ON compte_client.id_compte = compte_image_profil.id_compte WHERE compte_client.id_compte = {$_SESSION['id_compte']}";

$avis = $pdo->query($sql);



foreach ($mot_de_passe as $row){
$mdp_cryptee = $row['mdp'];
$id_adresse = $row['id_adresse'];
}

//traitement de la modification des informations
if ($_POST != null){
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


    $verif = check_erreur_client($_POST['nom'], $_POST['prenom'], $_POST['pseudo'], $_POST['email'],$_POST['date'], $_POST['n_mdp'], $_POST['n_mdpc'], $_POST['adresse'], $_POST['code_postal']);

    if(check_crypte_MDP($_POST['mdp'] ,$mdp_cryptee) && !empty($verif) && !(empty($verif['code_postal']) xor empty($verif['rue'])) && !(empty($verif['mdp']) xor empty($verif['mdpc']))){
        sql_update_client($pdo ,$_POST['nom'],$_POST['prenom'],$_POST['pseudo'],$_POST['email'],$_POST['date'],$_POST['adresse'],$_POST['code_postal'],$_POST['complement_adresse'],crypte_v2($_POST['n_mdp']), $_SESSION['id_compte'],$id_adresse);
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
    <script src="confirmation.js"></script>
</head>
<body>
    <a href="../../deconnexion/">se deconnecter</a>
    <h1>Mon Profil</h1>
    <div>
        <?php
            //affichage des info du compte
            foreach ($info_compte as $row){  
        ?>
        <img src="<?php echo "../../".$row['url_image'];?>" alt="<?php echo $row['alt_image'];?>" title="<?php echo $row['titre_image'];?>">

        <form action="" method="post" id="donnee">
            <label for="pseudo">Pseudonyme</label>
            <input type="text" name="pseudo" value="<?php echo $row['pseudo'];?>" placeholder="À renseigner">
            <!--Erreur pseudo-->
            <?php
                if (isset($verif['pseudo'])){
            ?>
                        <p class="error">
                            <?="Erreur : ".$verif['pseudo']?>
                        </p>
            <?php
                }
            ?>
            <label for="nom">Nom</label>
            <input required type="text" name="nom" value="<?php echo $row['nom'];?>" placeholder="À renseigner">
            <!--Erreur nom-->
            <?php
                if (isset($verif['nom'])){
            ?>
                        <p class="error">
                            <?="Erreur : ".$verif['nom']?>
                        </p>
            <?php
                }
            ?>
            <label for="prenom">Prenom</label>
            <input required type="text" name="prenom" value="<?php echo $row['prenom'];?>" placeholder="À renseigner">
            <!--Erreur prenom-->
            <?php
                if (isset($verif['prenom'])){
            ?>
                        <p class="error">
                            <?="Erreur : ".$verif['prenom']?>
                        </p>
            <?php
                }
            ?>
            <label for="date">Date de Naissance</label>
            <input required type="date" name="date" value="<?php echo $row['date_naissance'];?>" placeholder="À renseigner">
            <!--Erreur Date-->
            <?php
                if (isset($verif['date'])){
            ?>
                        <p class="error">
                            <?="Erreur : ".$verif['date']?>
                        </p>
            <?php
                }
            ?>
            <label for="mail">Mail</label>
            <input required type="email" name="email" value="<?php echo $row['email'];?>" placeholder="À renseigner">
            <!--Erreur mail-->
            <?php
                if (isset($verif['email'])){
            ?>
                        <p class="error">
                            <?="Erreur : ".$verif['email']?>
                        </p>
            <?php
                }
            ?>
            <?php } ?>
            <label for="adresse">Adresse</label>
            <?php
            //affichage des info du compte
            $est_entre = false;
            foreach ($adresse_compte as $row){  
                $est_entre = true;
            ?>


            <input type="text" name="adresse" value="<?php echo $row['adresse'];?>" placeholder="À renseigner">
            <!--Erreur adresse-->
            <?php
                if (isset($verif['rue']) && $verif['rue'] != "Champ est vide"){
            ?>
                        <p class="error">
                            <?="Erreur : ".$verif['rue']?>
                        </p>
            <?php
                }
            ?>
            <label for="complement_adresse">Complement Adresse</label>
            <input type="text" name="complement_adresse" value="<?php echo $row['complement_adresse'];?>" placeholder="À renseigner">
            <label for="code_postal">Code Postal</label>
            <input type="text" name="code_postal" value="<?php echo $row['code_postal'];?>" placeholder="À renseigner">
            <!--Erreur code postal-->
            <?php
                if (isset($verif['code_postal']) && $verif['code_postal'] != "Champ est vide"){
            ?>
                        <p class="error">
                            <?="Erreur : ".$verif['code_postal']?>
                        </p>
            <?php
                }
            ?>
            <?php }
            if (!$est_entre) {
                ?>

            <input type="text" name="adresse" placeholder="À renseigner">
            <!--Erreur adresse-->
            <?php
                echo "test";
                if (isset($verif['rue']) && $verif['rue'] != "Champ est vide"){
                    echo "test";
            ?>  
                        <p class="error">
                            <?="Erreur : ".$verif['rue']?>
                        </p>
            <?php
                }
                echo "test";
            ?>
            <label for="complement_adresse">Complement Adresse</label>
            <input type="text" name="complement_adresse" placeholder="À renseigner">
            <label for="code_postal">Code Postal</label>
            <input type="text" name="code_postal" placeholder="À renseigner">
            <!--Erreur code postal-->
            <?php
                if (isset($verif['code_postal']) && $verif['code_postal'] != "Champ est vide"){
            ?>
                        <p class="error">
                            <?="Erreur : ".$verif['code_postal']?>
                        </p>
            <?php
                }
            ?>


                <?php
            } ?>
            <?php
                if (empty($verif['code_postal']) xor empty($verif['rue'])){
            ?>
                        <p class="error">
                            <?= "Remplissez les deux champs Adresse et Code Postal" ?>
                        </p>
            <?php
                }
            ?>

            <label for="mdp">Mot de Passe</label>
            <input type="password" name="mdp" placeholder="À renseigner">

            <label for="n_mdp">Nouveau Mot de Passe</label>
            <input type="password" name="n_mdp" placeholder="À renseigner">

            <label for="n_mdpc">Confirmer Nouveau Mot de Passe</label>
            <input type="password" name="n_mdpc" placeholder="À renseigner">

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
                            <img src="<?php echo "../../".$row['url_image'];?>" alt="<?php echo $row['alt_image'];?>" title="<?php echo $row['titre_image'];?>">
                            <p><?php echo $row['pseudo'];?></p>
                            <?php afficher_moyenne_note($row['note']);?>
                        </div>
                        <div>
                            <p><?php echo $row['titre'];?></p>  
                            <p><?php echo $row['commentaire'];?></p>
                            <p><?php echo "Avis publié le " . $row['date_avis'];?></p>
                        </div>
                    </div>
                </li>
                <?php } ?>
            </ul>
        </div>
    </div>

</body>
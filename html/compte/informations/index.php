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

//requete pour recuperer informations du compte sans l'adresse
$sql = "SELECT * FROM compte_client LEFT JOIN compte_image_profil ON compte_client.id_compte = compte_image_profil.id_compte WHERE compte_client.id_compte = 8;";    

$info_compte = $pdo->query($sql);

//requete pour recuperer l'adresse du compte
$sql = "SELECT * FROM client_adresse WHERE client_adresse.id_compte = 8;";

$adresse_compte = $pdo->query($sql);

//requete pour recuperer les avis du compte
$sql="SELECT pseudo,date_avis,note,titre,commentaire,url_image,titre_image,alt_image FROM compte_client INNER JOIN _avis ON compte_client.id_compte = _avis.id_client LEFT JOIN compte_image_profil ON compte_client.id_compte = compte_image_profil.id_compte WHERE compte_client.id_compte = 8";

$avis = $pdo->query($sql);

// Fermer la connexion
unset($pdo);

//traitement de la modification des informations
if ($_POST != null){
    if (!isset($_POST['nom'])) $_POST['nom'] = "";
    if (!isset($_POST['prenom'])) $_POST['prenom'] = "";
    if (!isset($_POST['mail'])) $_POST['mail'] = "";
    if (!isset($_POST['date'])) $_POST['date'] = "";
    if (!isset($_POST['rue'])) $_POST['rue'] = "";
    if (!isset($_POST['code_postal'])) $_POST['code_postal'] = "";

    $verif = check_erreur_client($_POST['nom'], $_POST['prenom'], $pseudo = null,$_POST['mail'],$_POST['date'], $_POST['rue'], $_POST['code_postal']);
    print_r($verif);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informations Compte</title>
</head>
<body>
    <a href="fin_session.php">se deconnecter</a>
    <h1>Mon Profil</h1>
    <div>
        <?php
            //affichage des info du compte
            foreach ($info_compte as $row){  
        ?>
        <img src="<?php echo "../../".$row['url_image'];?>" alt="<?php echo $row['alt_image'];?>" title="<?php echo $row['titre_image'];?>">

        <form action="" method="post">
            
            <label for="nom">Nom</label>
            <input required type="text" name="nom" value="<?php echo $row['nom'];?>">
            <label for="prenom">Prenom</label>
            <input required type="text" name="prenom" value="<?php echo $row['prenom'];?>">
            <label for="date">Date de Naissance</label>
            <input required type="date" name="date" value="<?php echo $row['date_naissance'];?>" >
            <label for="mail">Mail</label>
            <input required type="email" name="mail" value="<?php echo $row['email'];?>">
            <?php } ?>
            <label for="adresse">Adresse</label>
            <?php
            //affichage des info du compte
            $est_entre = false;
            foreach ($adresse_compte as $row){  
                $est_entre = true;
            ?>
            <label for="rue">Rue</label>
            <input type="text" name="rue" value="<?php echo $row['adresse'];?>">
            <label for="complement_adresse">complement_adresse</label>
            <input type="text" name="complement_adresse" value="<?php if(isset($row['complement_adresse'])){echo $row['complement_adresse']} else{echo "placeholder=\"À renseigner\"";}  ;?>">
            <label for="code_postal">Code Postal</label>
            <input type="text" name="code_postal" value="<?php echo $row['code_postal'];?>">
            <?php }
            if (!$est_entre) {
                ?>
            <label for="rue">Rue</label>
            <input type="text" name="rue" placeholder="À renseigner">
            <label for="complement_adresse">complement_adresse</label>
            <input type="text" name="complement_adresse" placeholder="À renseigner">
            <label for="code_postal">Code Postal</label>
            <input type="text" name="code_postal" placeholder="À renseigner">
                <?php
            } ?>
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
</html>
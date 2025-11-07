<?php

define('HOME_GIT', "../../../");

if (!isset($_SESSION)) {
    session_start();
}
else {
    // Retour à la page d'accueil
    header('location: ' . HOME_GIT);
}

require_once (HOME_GIT . '.config.php');
require_once (HOME_GIT . 'fonctions_php/fonction_produit.php');

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
            
            <label for="">Nom</label>
            <input type="text" name="nom" value="<?php echo $row['nom'];?>">
            <label for="">Prenom</label>
            <input type="text" name="prenom" value="<?php echo $row['prenom'];?>">
            <label for="">Date de Naissance</label>
            <input type="date" name="date" value="<?php echo $row['date_naissance'];?>" >
            <label for="">Mail</label>
            <input type="email" name="mail" value="<?php echo $row['email'];?>">
            <?php } ?>
            <label for="">Adresse</label>
            <?php
            //affichage des info du compte
            foreach ($adresse_compte as $row){  
                if($row != null){

                
            ?>
            <label for="">Rue</label>
            <input type="text" name="nom" value="<?php echo $row['adresse'];?>">
            <label for="">Code Postal</label>
            <input type="text" name="nom" value="<?php echo $row['code_postal'];?>">
            <?php }
            else {
                ?>
            <label for="">Rue</label>
            <input type="text" name="nom" placeholder="À renseigner">
            <label for="">Code Postal</label>
            <input type="text" name="nom" placeholder="À renseigner">
                <?php
            }} ?>
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
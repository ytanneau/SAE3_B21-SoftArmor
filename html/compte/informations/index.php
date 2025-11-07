<?php
define('HOME_GIT', "../../../");
session_start();

require_once (HOME_GIT . '.config.php');
require_once (HOME_GIT . 'fonctions_php/fonction_produit.php');

//requete pour recuperer informations du compte
$sql = "SELECT * FROM compte_client LEFT JOIN compte_image_profil ON compte_client.id_compte = compte_image_profil.id_compte INNER JOIN client_adresse ON compte_client.id_compte = client_adresse.id_compte WHERE compte_client.id_compte = {$_SESSION['id_compte']};";

$info_compte = $pdo->query();

//requete pour recuperer les avis du compte
$sql="SELECT pseudo,date_avis,note,titre,commentaire,url_image,titre_image,alt_image FROM compte_client INNER JOIN _avis ON compte_client.id_compte = _avis.id_client LEFT JOIN compte_image_profil ON compte_client.id_compte = compte_image_profil.id_compte WHERE compte_client.id_compte = {$id_compte}";

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
    <h1>Mon Profil</h1>
    <div>
        <?php
        //afficher les info compte 
        foreach ($produit_recent as $row){  
        ?>
        <form action="" method="post">
            
            <label for="">Nom</label>
            <input type="text" name="nom" value="<?php echo $row['nom'];?>">
            <label for="">Prenom</label>
            <input type="text" name="prenom" value="<?php echo $row['prenom'];?>">
            <label for="">Date de Naissance</label>
            <input type="date" name="date" value="<?php echo $row['date_naissance'];?>" >
            <label for="">Mail</label>
            <input type="email" name="mail" value="<?php echo $row['email'];?>">

            <label for="">Adresse</label>

            <label for="">Rue</label>
            <input type="text" name="nom" value="<?php echo $row['adresse'];?>">
            <label for="">Code Postal</label>
            <input type="text" name="nom" value="<?php echo $row['code_postal'];?>">
            
            <button type="submit">Modifier mes informations</button>
        </form>
        <img src="<?php echo $row['url_image'];?>" alt="<?php echo $row['alt_image'];?>" title="<?php echo $row['titre_image'];?>">
        <?php } ?>
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
                            <img src="<?php echo $row['url_image'];?>" alt="<?php echo $row['alt_image'];?>" title="<?php echo $row['titre_image'];?>">
                            <p><?php echo $row['pseudo'];?></p>
                            <?php afficher_moyenne_note();?>
                        </div>
                        <div>
                            <p><?php echo $row['titre'];?></p>
                            <p><?php echo $row['commentaire'];?></p>
                            <p><?php echo "Avis publié le" $row['date'];?></p>
                        </div>
                    </div>
                </li>
                <?php } ?>
            </ul>
        </div>
    </div>
    
</body>
</html>
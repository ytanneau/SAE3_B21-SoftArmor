<?php

session_start();

require_once("../fonctions_php/fonction_produit.php");

require_once ('../../.config.php');

// requete pour recuperer le nom public, le prix , la moyenne des notes et les informations de l'image de chaque produit
$query= "SELECT nom_public,prix,url_image,alt,_image.titre,note_moy AS moyenne FROM produit_visible INNER JOIN _images_produit ON produit_visible.id_produit = _images_produit.id_produit INNER JOIN _image ON _images_produit.id_image_principale = _image.id_image INNER JOIN produit_note ON produit_note.id_produit = produit_visible.id_produit WHERE produit_note.id_produit = produit_visible.id_produit;";

$produit_catalogue = $pdo->query($query);

// requete pour recuperer le nom public, le prix , la moyenne des notes et les informations de l'image des produit alimentaire
$query= "SELECT nom_public,prix,url_image,alt,_image.titre,note_moy AS moyenne FROM produit_visible INNER JOIN _images_produit ON produit_visible.id_produit = _images_produit.id_produit INNER JOIN _image ON _images_produit.id_image_principale = _image.id_image INNER JOIN produit_note ON produit_note.id_produit = produit_visible.id_produit INNER JOIN _produit_dans_categorie ON produit_visible.id_produit = _produit_dans_categorie.id_produit WHERE produit_note.id_produit = produit_visible.id_produit AND _produit_dans_categorie.nom_categorie = \"Alimentaire\";";

$produit_alimentaire = $pdo->query($query);

// requete pour recuperer le nom public, le prix , la moyenne des notes et les informations de l'image des produit les plus récent
$query= "SELECT nom_public,prix,url_image,alt,_image.titre,note_moy AS moyenne FROM produit_visible INNER JOIN _images_produit ON produit_visible.id_produit = _images_produit.id_produit INNER JOIN _image ON _images_produit.id_image_principale = _image.id_image INNER JOIN produit_note ON produit_note.id_produit = produit_visible.id_produit INNER JOIN _produit_dans_categorie ON produit_visible.id_produit = _produit_dans_categorie.id_produit WHERE produit_note.id_produit = produit_visible.id_produit ORDER BY date_creation DESC;";

$produit_recent = $pdo->query($query);

// requete pour recuperer le nom public, le prix , la moyenne des notes et les informations de l'image des produit en reduction
$query= "SELECT nom_public,prix,url_image,alt,_image.titre,note_moy AS moyenne,TRUNCATE((prix - prix*reduction*0.01),2) AS prix_reduit FROM produit_visible INNER JOIN _images_produit ON produit_visible.id_produit = _images_produit.id_produit INNER JOIN _image ON _images_produit.id_image_principale = _image.id_image INNER JOIN produit_note ON produit_note.id_produit = produit_visible.id_produit INNER JOIN _produit_dans_categorie ON produit_visible.id_produit = _produit_dans_categorie.id_produit INNER JOIN _promotion ON produit_visible.id_produit = _promotion.id_produit WHERE produit_note.id_produit = produit_visible.id_produit;";

$produit_reduit = $pdo->query($query);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="Page D'Accueil" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
</head>
<body>
    <?php if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] === false) { ?>
        <a href="compte/connexion">Se connecter</a>
    <?php } else { ?>
        <h1>Bienvenue <?= $_SESSION['pseudo'] ?></h1>
    <?php } ?>
<!--header-->
<!--Produit Banniere au jour (possiblement a faire)-->
<!--Vedette de la journée (possiblement a faire)(pour telephone)-->
<!--Produit Ajoutés Récemment-->
    <div>
        <h1>Produit Ajoutés Récemment</h1>
        <ul>
            <?php
            //boucle pour ajouter un produit dans un <li> 
            foreach ($produit_recent as $row){  
            ?>
            <li>
                <a href="produit/index.php">
                    <img src="<?= $row['url_image'];?>" title="<?= $row['titre'];?>" alt="<?= $row['alt'];?>">
                    
                    <h3><?= $row['nom_public']; ?></h3>

                    <div>
                        <?php 

                            if($row['moyenne']==null){
                                ?><p>Produit Non Noté</p><?php
                            }
                            else{
                                $moy = $row['moyenne'];
                                afficher_moyenne_note($moy);
                            }
                        ?>
                    </div>
                    <p><?php echo $row['prix'];?> €</p>
                </a>
            </li>
            <?php
            }
            ?>
        </ul>
    </div>    

<!--Produit en reduction-->
    <div>
        <h1>Produit En Réduction</h1>
        <ul>
            <?php
            //boucle pour ajouter un produit dans un <li> 
            foreach ($produit_reduit as $row){  
            ?>
            <li>
                <a href="produit/index.php">
                    <img src="<?= $row['url_image'];?>" title="<?= $row['titre'];?>" alt="<?= $row['alt'];?>">
                    
                    <h3><?= $row['nom_public']; ?></h3>

                    <div>
                        <?php 

                            if($row['moyenne']==null){
                                ?><p>Produit Non Noté</p><?php
                            }
                            else{
                                $moy = $row['moyenne'];
                                afficher_moyenne_note($moy);
                            }
                        ?>
                    </div>
                    <p><?php echo $row['prix'];?> €</p>
                    <p><?php echo $row['prix_reduit'];?> €</p>
                </a>
            </li>
            <?php
            }
            ?>
        </ul>
    </div>

<!--Produit alimentaire-->
    <div>
        <h1>Produit Alimentaire</h1>
        <ul>
            <?php
            //boucle pour ajouter un produit dans un <li> 
            foreach ($produit_alimentaire as $row){  
            ?>
            <li>
                <a href="produit/index.php">
                    <img src="<?= $row['url_image'];?>" title="<?= $row['titre'];?>" alt="<?= $row['alt'];?>">
                    
                    <h3><?= $row['nom_public']; ?></h3>

                    <div>
                        <?php 

                            if($row['moyenne']==null){
                                ?><p>Produit Non Noté</p><?php
                            }
                            else{
                                $moy = $row['moyenne'];
                                afficher_moyenne_note($moy);
                            }
                        ?>
                    </div>
                    <p><?php echo $row['prix'];?> €</p>
                </a>
            </li>
            <?php
            }
            ?>
        </ul>
    </div>

<!--Tout les produits du catalogue-->
    <div>
        <h1>Produit du catalogue</h1>
        <ul>
            <?php
            //boucle pour ajouter un produit dans un <li> 
            foreach ($produit_catalogue as $row){  
            ?>
            <li>
                <a href="produit/index.php">
                    <img src="<?= $row['url_image'];?>" title="<?= $row['titre'];?>" alt="<?= $row['alt'];?>">
                    
                    <h3><?= $row['nom_public']; ?></h3>

                    <div>
                        <?php 

                            if($row['moyenne']==null){
                                ?><p>Produit Non Noté</p><?php
                            }
                            else{
                                $moy = $row['moyenne'];
                                afficher_moyenne_note($moy);
                            }
                        ?>
                    </div>
                    <p><?php echo $row['prix'];?> €</p>
                </a>
            </li>
            <?php
            }
            ?>
        </ul>
    </div>

<!--Navigation (pour telephone)-->
    <div>
        <a href=""><img src="images/home.svg" title="Acceder à la page d'Accueil" alt="logo page d'accueil"></a>
        <a href="#"><img src="images/panier.svg" title="Acceder au Panier" alt="logo page panier"></a>
        <a href="#"><img src="images/favori.svg" title="Acceder aux favoris" alt="logo page favoris"></a>
        <a href="#"><img src="images/notification.svg" title="Acceder aux notifications" alt="logo page notifications"></a>
    </div>
<!--footer-->
</body>
</html>
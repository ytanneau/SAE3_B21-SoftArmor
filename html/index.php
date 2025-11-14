<?php
define('HOME_GIT', "../" );
define('HOME_SITE', "./" );

if (!isset($_SESSION)) {
    session_start();

    if(isset($_SESSION['raison_sociale'])){
        header('location: /vendeur/stock/');
    }
}

require_once (HOME_GIT . '.config.php');
require_once (HOME_GIT . 'fonction_produit.php');

// Nom public, prix, moyenne des notes et informations de l'image de chaque produit
$query= "SELECT produit_visible.id_produit,nom_public,prix,url_image,alt,_image.titre,note_moy AS moyenne FROM produit_visible INNER JOIN _images_produit ON produit_visible.id_produit = _images_produit.id_produit INNER JOIN _image ON _images_produit.id_image_principale = _image.id_image INNER JOIN produit_note ON produit_note.id_produit = produit_visible.id_produit WHERE produit_note.id_produit = produit_visible.id_produit;";
$produit_catalogue = $pdo->query($query);

// Nom public, prix, moyenne des notes et informations de l'image des produits alimentaires
$query= "SELECT produit_visible.id_produit,nom_public,prix,url_image,alt,_image.titre,note_moy AS moyenne FROM produit_visible INNER JOIN _images_produit ON produit_visible.id_produit = _images_produit.id_produit INNER JOIN _image ON _images_produit.id_image_principale = _image.id_image INNER JOIN produit_note ON produit_note.id_produit = produit_visible.id_produit INNER JOIN _produit_dans_categorie ON produit_visible.id_produit = _produit_dans_categorie.id_produit WHERE produit_note.id_produit = produit_visible.id_produit AND _produit_dans_categorie.nom_categorie = \"Alimentaire\";";
$produit_alimentaire = $pdo->query($query);

// Nom public, prix, moyenne des notes et informations de l'image des produits les plus récents
$query= "SELECT produit_visible.id_produit,nom_public,prix,url_image,alt,_image.titre,note_moy AS moyenne FROM produit_visible INNER JOIN _images_produit ON produit_visible.id_produit = _images_produit.id_produit INNER JOIN _image ON _images_produit.id_image_principale = _image.id_image INNER JOIN produit_note ON produit_note.id_produit = produit_visible.id_produit INNER JOIN _produit_dans_categorie ON produit_visible.id_produit = _produit_dans_categorie.id_produit WHERE produit_note.id_produit = produit_visible.id_produit ORDER BY date_creation DESC;";
$produit_recent = $pdo->query($query);

// Nom public, prix, moyenne des notes et informations de l'image des produits en réduction
$query= "SELECT produit_visible.id_produit,nom_public,prix,url_image,alt,_image.titre,note_moy AS moyenne,TRUNCATE((prix - prix*reduction*0.01),2) AS prix_reduit FROM produit_visible INNER JOIN _images_produit ON produit_visible.id_produit = _images_produit.id_produit INNER JOIN _image ON _images_produit.id_image_principale = _image.id_image INNER JOIN produit_note ON produit_note.id_produit = produit_visible.id_produit INNER JOIN _produit_dans_categorie ON produit_visible.id_produit = _produit_dans_categorie.id_produit INNER JOIN _promotion ON produit_visible.id_produit = _promotion.id_produit WHERE produit_note.id_produit = produit_visible.id_produit;";
$produit_reduit = $pdo->query($query);

// Fermer la connexion
unset($pdo);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="Page D'Accueil" content="width=device-width, initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <title>Accueil</title>
</head>
<body>
    <?php include HOME_SITE . "header.php"; ?>

    <main>
    
    <!--Produit Banniere au jour (possiblement a faire)-->
    <!--Vedette de la journée (possiblement a faire)(pour telephone)-->

    <!--Produit Ajoutés Récemment-->

    <div>
        <h1>Produits ajoutés récemment</h1>
        <ul>
            <?php
            // Boucle pour ajouter un produit dans un <li> 
            foreach ($produit_recent as $row) { ?>
                <li>
                    <a href="/produit/index.php?id_produit=<?php echo $row['id_produit'];?>"> 
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
            <?php } ?>
        </ul>
    </div>    
    <hr>


    <!-- Produits en réduction -->
    <div>
        <h1>Produits en réduction</h1>
        <ul>
            <?php
            //boucle pour ajouter un produit dans un <li> 
            foreach ($produit_reduit as $row){  
            ?>
            <li>
                <a href="/produit/index.php?id_produit=<?php echo $row['id_produit'];?>"> 
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
    <hr>
    

    <!-- Produits alimentaires -->
    <div>
        <h1>Produits alimentaires</h1>
        <ul>
            <?php
            // Boucle pour ajouter un produit dans un <li> 
            foreach ($produit_alimentaire as $row) { ?>
                <li>
                    <a href="/produit/index.php?id_produit=<?php echo $row['id_produit'];?>"> 
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
            <?php } ?>
        </ul>
    </div>
    <hr>

    <!-- Tous les produits du catalogue -->
    <div>
        <h1>Produits du catalogue</h1>
        <ul>
            <?php
            // Boucle pour ajouter un produit dans un <li> 
            foreach ($produit_catalogue as $row) { ?>
                <li>
                    <a href="/produit/index.php?id_produit=<?php echo $row['id_produit'];?>"> 
                        <img src="<?= $row['url_image'];?>" title="<?= $row['titre'];?>" alt="<?= $row['alt'];?>">
                        
                        <h3><?= $row['nom_public']; ?></h3>

                        <div>
                            <?php 
                                if($row['moyenne']==null) {
                                    ?><p>Produit Non Noté</p><?php
                                } else {
                                    $moy = $row['moyenne'];
                                    afficher_moyenne_note($moy);
                                }
                            ?>
                        </div>

                        <p><?= $row['prix'];?> €</p>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>

    <!-- Navigation (pour teléphone) -->
    <div>
        <a href=""><img src="image/home.svg" title="Acceder à la page d'Accueil" alt="logo page d'accueil"></a>
        <a href="#"><img src="image/panier.svg" title="Acceder au Panier" alt="logo page panier"></a>
        <a href="#"><img src="image/favori.svg" title="Acceder aux favoris" alt="logo page favoris"></a>
        <a href="#"><img src="image/notification.svg" title="Acceder aux notifications" alt="logo page notifications"></a>
    </div>

    </main>

    <footer>
        <?php //include HOME_SITE . 'footer.php' ?>
    </footer>
</body>
</html>
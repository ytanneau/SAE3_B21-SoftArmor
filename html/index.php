<?php
require_once("/docker/data/web/fonctions_php/fonction_produit.php");
$pdo = new PDO("mysql:dbname=saedb;host=mariadb","sae","dbsae3dunyles");
// requete pour recuperer le nom public, le prix , la moyenne des notes de chaque produit
$test= "SELECT nom_public,prix,url_image,alt,_image.titre,AVG(note) as moyenne FROM _produit INNER JOIN _images_produit ON _produit.id_produit = _images_produit.id_produit INNER JOIN _image ON _images_produit.id_image_principale = _image.id_image INNER JOIN _avis ON _avis.id_produit = _produit.id_produit where _avis.id_produit = _produit.id_produit;";
$result = $pdo->query($test);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="Page D'Accueil" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
</head>
<body>
    
    
    <div>
        <h1>PROMO RENTRÉ</h1>
        <ul>
            <?php
            //boucle pour ajouter un produit dans un <li> 
        foreach ($result as $row){
            
        ?>
            <li>
                <div>
                    <img src="images/<?php echo $row['url_image'];?>" title="<?php echo $row['titre'];?>" alt="<?php echo $row['alt'];?>">
                    <h3><?php echo $row['nom_public']; ?></h3>
                    <div>
                        <?php 
                            $moy = $row['moyenne'];
                            afficher_moyenne_note($moy); 
                        ?>

                    </div>
                    <p><?php echo $row['prix']; ?> €</p>
                </div>
            </li>
            <?php
            }
            ?>
        </ul>
        </div>
    
</body>
</html>
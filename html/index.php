<?php

require_once("../fonctions_php/fonction_produit.php");
require_once("../.config.php");

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="Page D'Accueil" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
</head>
<body>
    
    <a href="compte/connexion">Se connecter</a>
    
    <div>
        <h1>PROMO RENTRÉ</h1>
        <ul>
            <?php
            //boucle pour ajouter un produit dans un <li> 
        foreach ($result as $row){
            
        ?>
            <li>
                <div>
                    <img src="images/<?= $row['url_image'];?>" title="<?= $row['titre'];?>" alt="<?= $row['alt'];?>">
                    
                    <h3><?= $row['nom_public']; ?></h3>

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
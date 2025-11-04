<?php
$pdo = new PDO("mysql:dbname=saedb;host=db","sae","dbsae3dunyles");
$test= "SELECT * FROM _produit";
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
    <?php
        foreach ($result as $row){
            echo $row['nom_stock'];
        }
    ?>
    <section>
        <h1>PROMO RENTRÉ</h1>
        <ul>
            <?php

            ?>
            <li>
                <div>
                    
                </div>
            </li>
        </ul>
    </section>
    
</body>
</html>
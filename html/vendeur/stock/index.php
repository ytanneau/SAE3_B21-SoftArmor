<!doctype html>
<html lang="fr">
    <head>
    <meta charset="utf-8">
    <title>Alizon</title>
    <link rel="stylesheet" href="style.css">
    </head>
        <body>

<?php
require_once '.config.php';
$sql = 'select nom_stock from _produit where id_vendeur = :id_vendeur';
$nom_stock = initialize($sql);

$sql2 = 'select quantite from _produit where id_vendeur = :id_vendeur';
$quantite = initialize($sql2);


if ($nom_stock->execute()) {
    if($quantite->execute()){
        if ($stmt->rowCount() > 0) {
            atrapperNom($nom_stock, $quantite);
        } else {
            echo "Vous n'avez pas de produit.";
        }

        } else {
            echo "Il y a eu un problème. Veuillez réessayer plus tard.";
        }
}

unset($stmt);


function initialize($sql){
    global $pdo;
    $compte = 1;     
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":id_vendeur", $compte);
    }
    return $stmt;
} 




function atrapperNom($nom_stock, $quantite){
    $row = $nom_stock->fetch(PDO::FETCH_ASSOC);
    $row2 = $quantite->fetch(PDO::FETCH_ASSOC);
    while($row && $row2){
        ?>
        <a href= "html/vendeur/produit/<?php $nom_stock ?>">
        <table>
            <tr>
                <td><img src="MenuBurger.png" alt="Menu Burger"> </td>
                <td><?= $row['nom_stock']?> </td>
                <td><img src="eyeclose.png" alt=""> </td>
                <td><img src="promotion.png" alt=""> </td>
                <td><img src="Fleche.png" alt=""> </td>
                <td> | </td>
                <td><?php $row2 ?></td>
            </tr>            
        </table>
        </a>
        <?php
    }
}
?>

    </body>
</html>



<!-- Warning: Undefined variable $pdo in /var/www/html/vendeur/stock/index.php on line 38

Fatal error: Uncaught Error: Call to a member function prepare() on null in /var/www/html/vendeur/stock/index.php:38 Stack trace: #0 /var/www/html/vendeur/stock/index.php(15): initialize('select quantite...') #1 {main} thrown in /var/www/html/vendeur/stock/index.php on line 38 -->
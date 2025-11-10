<!doctype html>
<html lang="fr">
    <head>
    <meta charset="utf-8">
    <title>Alizon</title>
    <link rel="stylesheet" href="style.css">
    </head>
        <body>

<?php
require_once '../../../.config.php';
$sql = 'select nom_stock, quantite from produit_visible where id_vendeur = :id_vendeur';
$stmt = initialize($sql);

unset($quantite);


function initialize($sql){
    global $pdo;
    $compte = 1;     
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":id_vendeur", $compte);
    }
    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            ecrire_nom($stmt);
        } else {
            echo "Vous n'avez pas de produit.";
        }

        } else {
            echo "Il y a eu un problème. Veuillez réessayer plus tard.";
        }
    return $stmt;
} 




function ecrire_nom($nom_stock){
    $rows = $nom_stock->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row){
        ?>
        <a href= "html/vendeur/produit/<?php $row['nom_stock'] ?>">
        <table>
            <tr>
                <td><img src="MenuBurger.png" alt=> </td>
                <td><?= $row['nom_stock']?> </td>
                <td><img src="eyeclose.png" alt=""> </td>
                <td><img src="promotion.png" alt=""> </td>
                <td><img src="Fleche.png" alt=""> </td>
                <td> | </td>
                <td><?php echo $row['quantite'] ?></td>
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
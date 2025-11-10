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
        
        <table>
            <tr>
                <td><img src="MenuBurger.png" alt=> </td>
                <td> 
                    <a href= "../produit/index.php?produit=<?php echo $row['nom_stock'] ?>"> <?= $row['nom_stock']?> 
                    </a>
                </td>
                <td><img src="eyeclose.png" alt=""> </td>
                <td><img src="promotion.png" alt=""> </td>
                <td><img src="Fleche.png" alt=""> </td>
                <td> | </td>
                <td><?php echo $row['quantite'] ?></td>
            </tr>
        </table>
        <?php
    }
}
?>

    </body>
</html>



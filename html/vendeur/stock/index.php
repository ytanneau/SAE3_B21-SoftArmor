<?php
require_once('../../../.config.php');
// $sql = "select id_produit from _produit where id_compte = :id_compte";

// $stmt = $pdo->prepare($sql);
// $stmt->execute([':id_compte => 1']); // a remplacer par dollars session
// echo $stmt;

$sql = 'select id_produit from _produit where id_vendeur = :id_vendeur';

// essai n2 :
#$req = mysql_query($sql) or die('erreur sql ! <br />' .mysql_error());
$compte = 1;     
        // Si la requête a pu être préparée

        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":id_vendeur", $compte);

            // Si la requête a pu être exécutée
            
            if ($stmt->execute()) {

                // Si l'utilisateur existe (1 enregistrement trouvé)
            
                if ($stmt->rowCount() > 0) {

                    // Si il y a !0 ligne

                    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        print_r($row);
                    }
                } else {
                    print("Vous n'avez pas de produit.");
                }
            } else {
                echo "Il y a eu un problème. Veuillez réessayer plus tard.";
            }
            unset($stmt);
        }

?>

<?php

function getAllproduit(){

}

function getVendeur(){

}

function getProduit(){

}
?>
 
 
<!doctype html>
<html lang="fr">
    <head>
    <meta charset="utf-8">
    <title>Alizon</title>
    <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <a href= "html/vendeur/produit/">
        <table>
            <tr>
                <td><img src="MenuBurger.png" alt="Menu Burger"> </td>
                <td><?php $nomProduit?> </td>
                <td><img src="eyeclose.png" alt=""> </td>
                <td><img src="promotion.png" alt=""> </td>
                <td><img src="Fleche.png" alt=""> </td>
                <td>trait </td>
                <td><?php $nombreStock ?></td>
            </tr>            
        </table>
        </a>
    </body>
</html>
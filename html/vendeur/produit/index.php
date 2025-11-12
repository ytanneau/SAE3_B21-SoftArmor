<?php
//permet d'utiliser le fichier config.php
require_once '../../../.config.php';
define("HOME_GIT", "../");
if (!isset($_SESSION)) {
    session_start();
}
//verifie si quelqun est connecté
// if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
//     header('location: ' . HOME_GIT);
//     exit;
// }
?>
<!doctype html>
<html lang="fr">
    <head>
    <meta charset="utf-8">
    <title>Alizon</title>
    <link rel="stylesheet" href="style.css">
    </head>
        <body>

<?php


//commande qui permet de séléctionner l'id du produit, son nom et sa quantité en stock
$sql = 'select nom_stock, quantite, nom_public, description, description_detaillee, tva, poids, prix, volume from _produit where id_produit = :id_produit';

//initialise la variable qui porte la commande sql 
$stmt = initialize($sql);

unset($quantite);

//fonction qui execute la commande et gere les cas d'erreur
function initialize($sql){
    global $pdo;
    print_r(value: $_SESSION);
    $produit = 1;

    //prepare la commande et verifie si elle est pas vide
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":id_produit", $produit);
    }
    //regarde si la commande est executable
    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            ecrire_nom($stmt);
        } else {
            echo "Vous n'avez pas de produit.";
        }
        //verif si le serveur marche
        } else {
            echo "Il y a eu un problème. Veuillez réessayer plus tard.";
        }
    return $stmt;
} 



// écris le tableau avec les valeurs a l'interieur
function ecrire_nom($nom_stock){
    $rows = $nom_stock->fetchAll(PDO::FETCH_ASSOC);
    print_r($rows);
        ?>
        
        <table>
            <tr>
                <th>nom en stock </th>
                <th>nom public </th>
                <th>Prix actuelle </th>
                <th>taux TVA </th>
                <th>Poids </th>
                <th>Volume </th>
            </tr>
            <tr>
                <td><?php echo $rows['nom_stock']?> </td>
                <td><?php echo $rows['nom_public']?>  </td>
                <td><?php echo $rows['prix']?>  </td>
                <td><?php echo $rows['tva']?>  </td>
                <td><?php echo $rows['poids']?>  </td>
                <td><?php echo $rows['volume'] ?></td>
            </tr>
        </table>
        <?php
    }
?>

    </body>
</html>



<?php
//permet d'utiliser le fichier config.php
require_once '../../../.config.php';
define("HOME_GIT", "../");
if (!isset($_SESSION)) {
    session_start();
}
//verifie si quelqun est connecté
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('location: ' . HOME_GIT);
    exit;
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

<?php


//commande qui permet de séléctionner les caractéristiques du produit pour les réutiliser dans le document
$sql = 'select nom_stock, quantite, nom_public, description, description_detaillee, tva, poids, prix, volume from _produit where id_produit = :id_produit';

//initialise la variable qui porte la commande sql 
$stmt = initialize($sql);

unset($quantite);

//fonction qui execute la commande et gere les cas d'erreur
function initialize($sql){
    global $pdo;
    $produit = $_GET['produit'];

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



// écris le tableau avec les valeurs a l'interieur en utilisant rows['le nom donnéé dans la commande sql']
function ecrire_nom($nom_stock){
    $rows = $nom_stock->fetchAll(PDO::FETCH_ASSOC);
    $rows = $rows[0];
        ?>
<!-- tableau a mettre en haut a droite -->
        <table>
            <tr>
                <th>nom en stock </th>
                <td><?php echo $rows['nom_stock']?> </td>
            </tr>
                <th>nom public </th>
                <td><?php echo $rows['nom_public']?>  </td>
            </tr>
                <th>Prix actuelle </th>
                <td><?php echo $rows['prix']?>  </td>
            </tr>
                <th>taux TVA </th>
                <td><?php echo $rows['tva']?>  </td>
            </tr>
                <th>Poids </th>
                <td><?php echo $rows['poids']?>  </td>
            </tr>
                <th>Volume </th>
                <td><?php echo $rows['volume'] ?></td>
            </tr>
            <!-- div a mettre en dessous du tableau -->
            <div>
                <?php echo $rows['description'] ?>
            </div>
<!-- A mettre encore en dessous -->
            <div>
                <?php echo $rows['description_detaillee'] ?>
            </div>
                
            <div>
                <table>
                    <tr>
                        <td>
                            <?php echo $rows['quantite'] ?>
                        </td>
                    </tr>
                </table>
            </div>
        </table>
        <?php
    }
?>

    </body>
</html>



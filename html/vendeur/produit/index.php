<?php
//permet d'utiliser le fichier config.php
define("HOME_GIT", "../../../");

require_once HOME_GIT . '.config.php';

if (!isset($_SESSION)) {
    session_start();
}
//verifie si quelqun est connecté
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] === false) {
    header('location: ../');
    exit;
}

//commande qui permet de séléctionner les caractéristiques du produit pour les réutiliser dans le document
$sql = 'SELECT nom_stock, quantite, nom_public, description, description_detaillee, tva, poids, prix, volume from _produit where id_produit = :id_produit';
$sql2 = 'SELECT * FROM `_images_produit` WHERE  id_produit = :id_produit';
$sqlverif = 'SELECT id_produit from `_produit` where id_vendeur = :id_vendeur AND id_produit = :id_produit';

if ($sqlverif == NULL) {
    echo "Ce produit est introuvable.";
} else {
    //initialise la variable qui porte la commande sql 
    $stmt = initialize($sql, $sql2);
}




//fonction qui execute la commande et gere les cas d'erreur
function initialize($sql, $sql2){
    global $pdo;
    $produit = $_GET['produit'];

    //prepare la commande et verifie si elle est pas vide
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":id_produit", $produit);
    }
    if ($stmt2 = $pdo->prepare($sql2)) {
        $stmt2->bindParam(":id_produit", $produit);
    }
    //regarde si la commande est executable
    if ($stmt->execute() && $stmt2->execute()) {
        if ($stmt->rowCount() > 0) {
            ecrire_nom($stmt, $stmt2);
        } else {
            echo "Vous n'avez pas de produit.";
        }
        //verif si le serveur marche
        } else {
            echo "Il y a eu un problème. Veuillez réessayer plus tard.";
        }
    return $stmt;
} 

function ecrire_nom($nom_stock, $image){
    global $produit;
    $rows = $nom_stock->fetchAll(PDO::FETCH_ASSOC);
    $rows = $rows[0];
    $rows2 = $image->fetchAll(PDO::FETCH_ASSOC);
    $rows2 = $rows2[0];
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
    </table>
    <div>
        <?php
        if ($rows2['id_image_principale'] != NULL) {
            # code...
        }
        ?>
        <img src= "../ressource/produit/<?php $produit ?>">
    </div>
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

<?php

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
        <main>               
        </main>
    </body>
</html>




<?php
//permet d'utiliser le fichier config.php
require_once '../../../.config.php';

if (!isset($_SESSION)) {
    session_start();
}

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

//commande qui permet de séléctionner l'id du produit, son nom et sa quantité en stock
$sql = 'select id_produit, nom_stock, quantite from produit_visible where id_vendeur = :id_vendeur';

//initialise la variable qui porte la commande sql 
$stmt = initialize($sql);

unset($quantite);

//fonction qui execute la commande et gere les cas d'erreur
function initialize($sql){
    global $pdo;
    $compte = $_SESSION['id_compte'];

    //prepare la commande et verifie si elle est pas vide
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":id_vendeur", $compte);
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
    foreach ($rows as $row){
        ?>
        
        <table>
            <tr>
                <td><img src="MenuBurger.png" alt=> </td>
                <td> 
                    <!-- le nom du produit (nom_stock) avec le lien qui est l'id du produit (id_produit) -->
                    <a href= "../produit/index.php?produit=<?php echo $row['id_produit'] ?>"> <?= $row['nom_stock']?> 
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



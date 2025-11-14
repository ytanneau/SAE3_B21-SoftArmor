<?php
    define('HOME_GIT', '../../../');
    define('HOME_SITE', '../../');

    require_once HOME_GIT . 'fonction_avis.php';

    if (!isset($_SESSION)) {
        session_start();
    }

    //verifie si quelqun est connecté
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] === false) {
        header('location: ../');
        exit;
    }

    if (isset($_GET['produit'])) {
        $data = avis_client_produit($_GET['produit']);
    }
    else{
        $data = NULL;
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alizon avis</title>
</head>
<body>
    <main>
<?php
    if ($data === NULL){
?>
    <h1>Désoler se produit existe pas</h1>
<?php        
    }
    else{
        //print_r($data);
?>
    <ul>
<?php
        foreach($data as $row){
?>
        <li>
            <table>
                <tr>
                    <td><?=htmlentities($row['pseudo'])?></td>
                    <td><?=htmlentities($row['note'])?></td>
                    <td><?=htmlentities($row['titre'])?></td>
                    <td rowspan="2"><img src="<?=HOME_SITE . "ressources/avis/" . htmlentities($row['url_image'])?>" alt="<?=htmlentities($row['alt_image'])?>" tilte="<?=htmlentities($row['titre_image'])?>"></td>
                </tr>
                <tr>
                    <td><?=htmlentities($row['date_avis'])?></td>
                    <td colspan="2"><?=htmlentities($row['commentaire'])?></td>
                </tr>
            </table>
        </li>
    </ul>
<?php
        }
    }
?>
    </main>
</body>
</html>
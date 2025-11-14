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
<?php
    if ($data === NULL){
?>
    <h1>Désoler se produit existe pas</h1>
<?php        
    }
    else{
        print_r($data);
    }
?>
</body>
</html>
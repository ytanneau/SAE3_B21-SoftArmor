<?php
    session_start();
    define('HOME_GIT', '../../');
    define('HOME_SITE', '../');
    $_SESSION['raison_sociale'] = "dédé le vendeur";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link rel="stylesheet" href="<?= HOME_SITE . "style.css" ?>">
</head>
<body>
    <?php
    require_once('header.php');
    ?>
</body>
</html>
    
    


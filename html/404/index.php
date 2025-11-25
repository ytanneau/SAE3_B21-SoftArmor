<?php
const HOME_GIT = "../../";
const HOME_SITE = "../";

$images = HOME_SITE . "image/"
?>


<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Alizon</title>
        <link rel="stylesheet" href="style.css">
        <?php include HOME_SITE . 'link_head.php' ?>
    </head>
    <body id = "body404">
        <main class = "le404">
            <?php
                http_response_code(404);
                ?>
                <div class ="all_404">
                <h1 class="titre_404">Page inexistante</h1>
                <h2>Oups on dirait que cette page n’existe pas !</h2>
                <a href= <?= HOME_SITE?> class = "lien404">
                    <img src="<?= $images . 'Alizon_blanc.png' ?>" alt="Logo Alizon" class="logo">
                </a>
                <a href=<?= HOME_SITE ?>>
                    <button class="bouton404" type="button">Revenir à l'accueil</button>
                </a>
                </div>
        </main>
    </body>
</html>
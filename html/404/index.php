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
    </head>
    <body class = "body404">
        <main class = "le404">
            <?php
                http_response_code(404);
                ?>
                <div class ="all_404">
                <h1 class="titre_404">404</h1>
                <p>Oups on dirait que cette page n’existe pas !</p>
                <a href= <?= HOME_SITE?> >
                    <img src="<?= $images . 'Alizon_noir.png' ?>" alt="Logo Alizon" class="logo">
                </a>
                <a href= <?= HOME_SITE?>>Retour à l'accueil</a>
                </div>
        </main>
    </body>
    <style>
        .body404{
            height: 100vh;
            width: 100vw;
            background: linear-gradient(to right, #CBCCEA, #3157A3);
        }
        .le404 {
            display: flex;
            justify-content: center;
            flex-direction: column;
        }
        .all_404{
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        .all_404 h1{
            justify-self: center;
        }
        .logo{
            width: 400px;
        }
    </style>
</html>
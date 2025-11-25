<?php
const HOME_GIT = "../../";
const HOME_SITE = "../";
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
            <?php
                http_response_code(404);
                ?>
                <h1>404 - Page introuvable</h1>
                <p>La page demandée n'existe pas.</p>
                <a href= <?= HOME_SITE?>>Retour à l'accueil</a>
        </main>
    </body>
</html>
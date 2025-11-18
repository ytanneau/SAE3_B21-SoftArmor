<?php

if (!isset($_SESSION)) {
    session_start();
}

define('HOME_GIT', '../../../');
define('HOME_SITE', '../../');

// Si l'utilisateur est déjà connecté

if ($_POST != null) {
    require_once (HOME_GIT . 'fonction_compte.php');

    // Premier formulaire (après email)
    if (!isset($_POST['reponse'])) {
        $question = sql_email_question(htmlentities(trim($_POST['email'] ?? '')));
        echo $question;

        if (!isset($question) || empty($question)) {
            echo "Aucune question, réinitialisation impossible";
        }
    // Deuxième formulaire (après réponse)
    } else {
        echo $_POST['mdp'];
    }
}

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    if (isset($_GET['id_produit'])) {
        // Si l'utilisateur se connecte après avoir essayé d'acheter un produit sans se connecter, alors il est redirigé vers ce produit après connexion
        header('Location: ' . HOME_SITE . "produit/index.php?id_produit=" . htmlentities($_GET['id_produit']));
    } else {
        // Sinon, retour accueil
        header('location: ' . HOME_SITE);
    }
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alizon - Réinitialiser le mot de passe</title>
</head>
<body>
    <?php if (!isset($question) || empty($question)) { ?>
        <form action="" method="post">
            <label for="email">Votre adresse e-mail</label>
            <input type="text" id="email" name="email" placeholder="abc@xyz.fr">

            <input type="submit" value="Confirmer">
        </form>
    <?php } else { ?>
        <p><?= $question ?? '' ?></p>
        <form action="" method="post">
            <label for="reponse">Votre réponse</label>
            <input type="text" id="reponse" name="reponse">

            <input type="submit" value="Confirmer">
        </form>
    <?php } ?>
</body>
</html>
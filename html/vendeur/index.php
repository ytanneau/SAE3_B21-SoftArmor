<?php

if (!isset($_SESSION)) {
    session_start();
}
    define('HOME_GIT', '../../');

// Si l'utilisateur est déjà connecté

if ($_POST != null){
    require_once (HOME_GIT . 'fonction_compte.php');
    $res = connect_compte($_POST['email'], $_POST['mdp'], 'vendeur', HOME_GIT);
}

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && $res['correcte']) {
    header('location: stock');
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alizon Vendeur Connexion</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body id="connect_vendeur">
    <main>
        <?php if (isset($res['fatal'])) { ?>
                <h1 class="fatale">Désolé, nous rencontrons des problèmes serveur</h1>
        <?php } else { ?>
                <form action="" method="post">
                    <legend>S'identifier</legend>
                    
                    <?php if (isset($res['connect'])) { ?>
                        <h3 class="error"><?=$res['connect']?></h1>
                    <?php } ?>

                    <!-- Adresse e-mail -->
                    <br>
                    <label for="email">Email</label>
                    <input type="email"
                        name="email"
                        id="email"
                        value="<?php if (isset($_POST['email'])) echo htmlentities($_POST['email'])?>"
                        required
                        class="champ">

                    <?php if (isset($res['email'])) { ?>
                        <p class="error">
                            <?="Erreur : ".$res['email']?>
                        </p>
                    <?php } ?>

                    <!-- Mot de passe -->
                    <label for="mdp">Mot de passe</label>
                    <input type="password" 
                        name="mdp"
                        id="mdp"
                        required
                        class="champ">

                    <?php if (isset($res['mdp'])) { ?>
                        <p class="error">
                            <?="Erreur : ".$res['mdp']?>
                        </p>
                    <?php } ?>
                    
                    <input type="submit" value="Se connecter" class="boutton">            
                </form>

                <!-- Lien vers la page d'inscription vendeur -->
                <p>Pas de compte ? <a href="inscription/">S'inscrire</a></p>
        <?php
            }
        ?>
    </main>
</body>
</html>
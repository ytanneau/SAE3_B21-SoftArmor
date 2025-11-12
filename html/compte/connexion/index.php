<?php

if (!isset($_SESSION)) {
    session_start();
}

define('HOME_GIT', '../../../');

// Si l'utilisateur est déjà connecté

if ($_POST != null){
    require_once (HOME_GIT . 'fonction_compte.php');
    $erreurs = connect_compte($_POST['email'], $_POST['mdp'], 'client', HOME_GIT);
}

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('location: ' . HOME_GIT);
    exit;
}

?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alizon Connexion</title>
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link rel="stylesheet" href="<?=HOME_GIT?>style.css">
</head>
<body id="connect_client">
    <main>
        <img src="" alt="">
        <a href="../">
            <img src="<?=HOME_GIT?>image/Alizon_noir.png" alt="logo alizon" title="logo alizon">
        </a>
        <h2>S’identifier</h2>

        <form action="" method="post">

            <!-- Adresse e-mail -->
            <br>
            <label for="email">Email</label>
            <input type="text"
                name="email"
                id="email"
                value="<?php if (isset($_POST['email'])) echo $_POST['email']?>"
                class="champ">

            <?php if (isset($erreurs['email'])) { ?>
                <p style="color: red"><?= $erreurs['email'] ?></p>
            <?php } ?>

                <!-- Mot de passe -->
            <label for="mdp">Mot de passe</label>
            <input type="password" 
                name="mdp"
                id="mdp"
                value=""
                class="champ">
            
            <p class="error"><?php if (isset($erreurs['mdp'])) { $erreurs['mdp']; } ?></p>
        
            <p class="error"><?php if (isset($erreurs['connecte'])) { echo $erreurs['connecte']; } ?></p>
            
            <input type="submit" value="Se connecter" class="bouton"> 
        </form>
        <p>Pas de compte ? <a href="../inscription/">S'inscrire</a></p>
    </main>

    <!--
    <script>
        const champEmail        = document.getElementById("email");
        const champMdp          = document.getElementById("mdp");

        const msgErreurEmail    = document.getElementById("msgErreurEmail");
        const msgErreurMdp      = document.getElementById("msgErreurMdp");

        champEmail.addEventListener('input', () => {
            if (champEmail.value === "") {
                msgErreurEmail.textContent = "L'adresse e-mail ne doit pas être vide";
                msgErreurEmail.style.display = "block";
                event.preventDefault();
            } else if (!champEmail.value.match(/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+.[a-zA-Z]{2,}$/)) {
                msgErreurEmail.textContent = "L'adresse e-mail est invalide";
                msgErreurEmail.style.display = "block";
                event.preventDefault();
            } else {
                msgErreurEmail.style.display = "none";
            }
        })

        champMdp.addEventListener('input', () => {
            if (champMdp.value === "") {
                msgErreurMdp.style.display = "block";
                event.preventDefault();
            } else {
                msgErreurMdp.style.display = "none";
            }
        })
    </script>
    -->
</body>
</html>
<?php

if (!isset($_SESSION)) {
    session_start();
}

define('HOME_GIT', '../../../');

// Si l'utilisateur est déjà connecté

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    require_once (HOME_GIT . 'fonction_compte.php');
    $res = connect_compte($_POST['email'], $_POST['mdp'], 'client', HOME_GIT);
}

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    echo "Vous avez réussi enfin !";
    header('location: ' . HOME_GIT . "html");
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
</head>
<body>
    <main>
        <form action="" method="post">
            <legend>Informations</legend>

            <!-- Adresse e-mail -->
            <br>
            <label for="email">Email</label>
            <input type="email"
                name="email"
                id="email"
                value="<?php if (isset($_POST['email'])) echo $_POST['email']?>"
                required>

                <!-- Mot de passe -->
            <label for="mdp">Mot de passe</label>
            <label for="mdp">Mot de passe</label>
            <input type="password" 
                name="mdp"
                id="mdp"
                value=""
                required>
            
            <input type="submit" value="Se connecter">            
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
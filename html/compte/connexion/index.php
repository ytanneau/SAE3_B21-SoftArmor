<?php

require "config.php";

// Si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // SELECT 1 FROM clients (username, password) WHERE username = :username AND password = :password
    // Si cette ligne existe, le login est valide
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
    <a href="../../../index.php">Retour à l'accueil</a>
    
    <form action="" method="post">
        <fieldset>
            <legend>Informations</legend>

            <!-- Adresse e-mail -->
            <label for="email">E-mail</label>
            <input type="text" id="email" name="email" required>

            <!-- Mot de passe -->
            <label for="mdp">Mot de passe</label>
            <input type="text" id="mdp" name="mdp" required>
        </fieldset>
        
        <button type="submit">Se connecter</button>
    </form>
</body>
</html>
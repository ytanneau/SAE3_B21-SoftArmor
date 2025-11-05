<?php

session_start();

// Si l'utilisateur est déjà connecté
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('location: ../../');
    exit;
}

require_once "config.php";
require_once "_fonction_compte.php";

$erreurs = [
    "email" => "L'email est vide"
    ""
]

// Initialiser les variables
$email = $mdp = "";
$erreur_email = $erreur_mdp = "";

// Si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier si l'e-mail est vide
    if (empty(trim($_POST['email']))) {
        $erreur_email = "E-mail vide";
    }

    // Vérifier si le mot de passe est vide
    if (empty(trim($_POST['mdp'])))

    // Si erreur e-mail ou MDP

    // Si la requête n'a pas pu être préparée

    // Si la requête n'a pas pu être exécutée

    // Si l'utilisateur n'existe pas (aucun enregistrement trouvé)

    // Si je n'ai pas pu récupérer la ligne

    // Si le mot de passe est correct




    // SELECT 1 FROM clients (username, password) WHERE username = :username AND password = :password
    // Si cette ligne existe, le login est valide
}

// Fermer la connexion
unset($pdo);

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

        <p>Pas de compte ? <a href="compte/inscription">S'inscrire</a></p>
    </form>
</body>
</html>
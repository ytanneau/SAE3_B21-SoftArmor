<?php

if (!isset($_SESSION)) {
    session_start();
}

// Si l'utilisateur est déjà connecté
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('location: ../../');
    exit;
}

require_once ('../.config.php');
require_once ("../fonction_compte.php");

// Initialiser les variables
$email = $mdp = "";
$erreur_email = $erreur_mdp = "";

// Si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Vérifier si l'e-mail est vide, sinon l'enregistrer

    if (empty(trim($_POST['email']))) {
        $erreur_email = "E-mail vide";
    } else {
        $email = trim($_POST['email']);
    }

    // Vérifier si le mot de passe est vide, sinon l'enregistrer

    if (empty(trim($_POST['mdp']))) {
        $erreur_mdp = "Mot de passe vide";
    } else {
        $mdp = trim($_POST['mdp']);
    }

    // Si pas d'erreur e-mail ou MDP

    if (empty($erreur_email) && empty($erreur_mdp)) {
        $sql = "SELECT * FROM compte_client WHERE email = :email LIMIT 1";
        
        // Si la requête a pu être préparée

        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":email", $email);

            // Si la requête a pu être exécutée

            if ($stmt->execute()) {

                // Si l'utilisateur existe (1 enregistrement trouvé)

                if ($stmt->rowCount() == 1) {

                    // Si j'ai pu récupérer la ligne

                    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $pseudo = $row['pseudo'];
                        $id_compte = $row['id_compte'];
                        $mdp_hash = $row['mdp'];

                        // if (password_verify($mdp, $mdp_hash)) {
                        if (check_same_MDP($mdp, $mdp_hash)) {
                            $_SESSION['logged_in'] = true;
                            $_SESSION['pseudo'] = $pseudo;
                            $_SESSION['id_compte'] = $id_compte;
                            $_SESSION['email'] = $email;                            
                            
                            // Retour à la page d'accueil
                            header('location: ../../');
                            exit;
                        } else {
                            echo "L'e-mail ou le mot de passe est incorrect. <br>";
                        }
                    }
                } else {
                    echo "L'e-mail ou le mot de passe est incorrect. <br>";
                }
            } else {
                echo "Il y a eu un problème. Veuillez réessayer plus tard.";
            }

            unset($stmt);
        }
    }

    // Fermer la connexion
    unset($pdo);
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
    <a href="<?= "../../" ?>">Retour à l'accueil</a>
    
    <form action="" method="post">
        <fieldset>
            <legend>Informations</legend>

            <!-- Adresse e-mail -->
            <label for="email">E-mail</label>
            <input type="text" id="email" name="email" required>

            <!-- Mot de passe -->
            <label for="mdp">Mot de passe</label>
            <input type="password" id="mdp" name="mdp" required>
        </fieldset>
        
        <button type="submit">Se connecter</button>

        <p>Pas de compte ? <a href="../inscription/">S'inscrire</a></p>
    </form>
</body>
</html>
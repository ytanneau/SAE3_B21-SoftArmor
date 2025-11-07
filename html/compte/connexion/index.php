<?php

define('HOME_GIT', '../../..');

if (!isset($_SESSION)) {
    session_start();
}

// Si l'utilisateur est déjà connecté
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('location: ' . HOME_GIT);
    exit;
}

require_once (HOME_GIT . '/.config.php');
require_once (HOME_GIT . '/fonction_compte.php');

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

        try {
            $sql = "SELECT * FROM compte_client WHERE email = :email LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            // Si on a bien une seule ligne en résultat

            if ($stmt->rowCount() == 1) {

                // Si on a bien pu récupérer la ligne

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
                        header('location: ' . HOME_GIT);
                        exit;
                    } else {
                        echo "L'e-mail ou le mot de passe est incorrect. <br>";
                    }
                }
            } else {
                echo "L'e-mail ou le mot de passe est incorrect. <br>";
            }

        } catch (PDOException $e) {
            unset($stmt);

            $fichierLog = "erreurs.log";
            $date = date("Y-m-d H:i:s");
            file_put_contents($fichierLog, $date . " Failed SQL request\n", FILE_APPEND);
            throw $e;
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
    <a href="<?= HOME_GIT ?>">Retour à l'accueil</a>
    
    <form action="" method="post">
        <fieldset>
            <legend>Informations</legend>

            <!-- Adresse e-mail -->
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" required>
            <p style="display: none; color: red;" id="msgErreurEmail"></p>

            <!-- Mot de passe -->
            <label for="mdp">Mot de passe</label>
            <input type="password" id="mdp" name="mdp" required>
            <p style="display: none; color: red;" id="msgErreurMdp">Le mot de passe ne doit pas être vide</p>
        </fieldset>
        
        <button type="submit">Se connecter</button>

        <p>Pas de compte ? <a href="<?= HOME_GIT . '/html/compte/inscription' ?>">S'inscrire</a></p>
    </form>

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
            if (champEmail.value === "") {
                msgErreurMdp.style.display = "block";
                event.preventDefault();
            }
        })
    </script>
</body>
</html>
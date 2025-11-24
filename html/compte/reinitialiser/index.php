<?php

if (!isset($_SESSION)) {
    session_start();
}

define('HOME_GIT', '../../../');
define('HOME_SITE', '../../');

$erreurs = [];
$etape = 0;

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    if (isset($_GET['produit'])) {
        // Si l'utilisateur se connecte après avoir essayé d'acheter un produit sans se connecter, alors il est redirigé vers ce produit après connexion
        header('Location: ' . HOME_SITE . "produit/index.php?produit=" . htmlentities($_GET['produit']));
    } else {
        // Sinon, retour accueil
        header('location: ' . HOME_SITE);
    }
    exit;
}

if ($_POST == null) {
    $etape = 1;
} else {
    require_once HOME_GIT . 'fonction_compte.php';

    $email = htmlentities(trim($_POST['email'] ?? ''));
    $reponse = htmlentities(trim($_POST['reponse'] ?? ''));
    $mdp = htmlentities(trim($_POST['mdp'] ?? ''));
    $mdpc = htmlentities(trim($_POST['mdpc'] ?? ''));

    $row = sql_email_question($email);
}

// Premier formulaire (après saisie adresse)
if (isset($_POST['etape']) && $_POST['etape'] === 'etape_adresse') {
    // Vérifier les erreurs d'e-mail
    if (check_vide($email)) {
        $erreurs['email'] = VIDE;
    } else if (!check_email($email)) {
        $erreurs['email'] = FORMAT;
    }
    
    // Si pas d'erreur sur l'e-mail
    if (!isset($erreurs['email'])) {
        if (!isset($row['question'])) {
            // Si pas de question associée à cet e-mail
            $erreurs['final'] = "L'adresse e-mail est incorrecte ou aucune question n'a été renseignée pour ce compte";
            $etape = 1;
        } else {
            $etape = 2;
        }
    } else {
        $etape = 1;
    }
}

// Deuxième formulaire (après saisie réponse)
if (isset($_POST['etape']) && $_POST['etape'] === 'etape_reponse') {
    // Si l'e-mail n'est plus dans le POST, il y a eu une modification intentionnelle de la page
    if (check_vide($email)) {
        die("Erreur lors de la récupération de l'adresse e-mail");
    }

    if (empty($reponse)) {
        $erreurs['reponse'] = VIDE;
    } 
    
    // Si pas d'erreur sur la réponse
    if (!isset($erreurs['reponse'])) {
        $reponse_valide = sql_check_reponse($email, $reponse);

        if (!$reponse_valide) {
            // Si pas de question associée à cet e-mail
            $erreurs['reponse'] = "La réponse est incorrecte";
            $etape = 2;
        } else {
            $etape = 3;
        }
    } else {
        $etape = 2;
    }
}

// Troisième formulaire (après saisie MDP et MDPC)
if (isset($_POST['etape']) && $_POST['etape'] === 'etape_mdp') {
    // Si l'e-mail n'est plus dans le POST, il y a eu une modification intentionnelle de la page
    if (check_vide($email)) {
        die("Erreur lors de la récupération de l'adresse e-mail");
    }
    
    // Vérifier les erreurs du MDP
    if (check_vide($mdp)){
        $erreurs['mdp'] = VIDE;
    }
    else if (!check_taille($mdp, TAILLE_MDP)){
        $erreurs['mdp'] = DEPASSE;
    }
    else if (!check_mot_de_passe($mdp)){
        $erreurs['mdp'] = FORMAT;
    }

    // Vérifier les erreurs du MDPC
    if (check_vide($mdpc)){
        $erreurs['mdpc'] = VIDE;
    }
    
    // Si pas d'erreur sur les mots de passe
    if (!isset($erreurs['mdp']) && !isset($erreurs['mdpc'])) {
        if ($mdp !== $mdpc) {
            $erreurs['final'] = CORRESPOND_PAS;
            $etape = 3;
        } else {
            // Hasher et update le mot de passe associé à l'email en POST
            $res = sql_change_mdp($email, $mdp);

            if ($res === false) {
                die('Erreur lors de la mise à jour du mot de passe');
            }

            // Rediriger vers la page de connexion
            header('location: ' . HOME_SITE . 'compte/connexion');
        }
    } else {
        $etape = 3;
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include HOME_SITE . 'link_head.php'; ?>
    <title>Alizon - Réinitialiser le mot de passe</title>
</head>
<body class="form_client">
    <?php include HOME_SITE . 'header.php'; ?>

    <main>
        <h1>Réinitialisation du mot de passe</h1>
        
        <!-- Premier formulaire (saisir l'email) -->
        <?php if ($etape === 1) { ?>
            <form action="" method="post">
                <input type="hidden" name="etape" value="etape_adresse">

                <label for="email">Votre adresse e-mail</label>
                <input class="champ" type="text" id="email" name="email" placeholder="abc@domaine.fr">

                <p class="error">
                    <?php
                        if (isset($erreurs['email'])) {
                            $message = $erreurs['email'];
                            
                            if ($erreurs['email'] === FORMAT) {
                                $message .= ". Exemple : abc@domaine.fr"; 
                            }

                            echo $message;
                        }
                    ?>
                </p>

                <p class="error">
                    <?= isset($erreurs['final']) ? $erreurs['final'] : '' ?>
                </p>

                <input class="bouton" type="submit" value="Confirmer">
            </form>

        <!-- Deuxième formulaire (saisir la réponse à la question) -->
        <?php } else if ($etape === 2) { ?>
            <p><?= $row['question'] ?? '' ?></p>

            <form action="" method="post">
                <input type="hidden" name="etape" value="etape_reponse">
                <input type="hidden" name="email" value="<?= $_POST['email'] ?>">

                <label for="reponse">Votre réponse</label>
                <input class="champ" type="password" id="reponse" name="reponse">

                <p class="error">
                    <?= isset($erreurs['reponse']) ? $erreurs['reponse'] : ''; ?>
                </p>

                <input class="bouton" type="submit" value="Confirmer">
            </form>

        <!-- Troisième formulaire (saisir le nouveau MDP) -->
        <?php } else if ($etape === 3) { ?>
            <form action="" method="post">
                <input type="hidden" name="etape" value="etape_mdp">
                <input type="hidden" name="email" value="<?= $_POST['email'] ?>">

                <label for="mdp">Nouveau mot de passe</label>
                <input class="champ" type="password" id="mdp" name="mdp">

                <p class="error">
                    <?= isset($erreurs['mdp']) ? $erreurs['mdp'] : ''; ?>
                </p>

                <label for="mdp">Confirmation du nouveau mot de passe</label>
                <input class="champ" type="password" id="mdpc" name="mdpc">

                <p class="error">
                    <?= isset($erreurs['mdpc']) ? $erreurs['mdpc'] : ''; ?>
                </p>

                <p class="error">
                    <?= isset($erreurs['final']) ? $erreurs['final'] : ''; ?>
                </p>

                <input class="bouton" type="submit" value="Confirmer">
            </form>
        <?php } ?>
    </main>
</body>
</html>
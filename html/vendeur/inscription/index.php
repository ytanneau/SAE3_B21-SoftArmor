<?php
    define('HOME_GIT', '../../../');
    define('HOME_SITE', '../../');

    if (!isset($_SESSION)) {
        session_start();
    }

    // Si connecté en vendeur, rediriger vers le stock, si connecté en client, rediriger vers l'accueil
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        header(isset($_SESSION['raison_sociale']) ? 'location: ../stock' : 'location: ' . HOME_SITE);
    }

    if ($_POST != null) {
        $erreurs = [];
        $fichier = HOME_GIT . 'fonction_compte.php';
        
        if (file_exists($fichier)) {
            require_once $fichier;
            $erreurs = create_profile_vendeur($_POST['raisonSocial'], $_POST['numSiret'], $_POST['numCobrec'], $_POST['email'], $_POST['adresse'], $_POST['compAdresse'], $_POST['codePostal'], $_POST['mdp'], $_POST['mdpc'], HOME_GIT);
        } else {
            $erreurs['fatal'] = true;
        }
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include HOME_SITE . 'link_head.php'; ?>
    <title>Alizon - Inscription</title>
</head>
<body id="inscription_vendeur">
    <main>
<?php
    if (isset($erreurs) && $erreurs == []) {
?>
        <h1>Félicitations, vous avez créé votre compte</h1>
        <a href="../">Connectez-vous</a>
<?php
    }
    else if (isset($erreurs['fatal'])){
?>
        <h1 class="fatale">Désolé, nous rencontrons des problèmes serveur</h1>
<?php
    }
    else{
?>
        <img src=""  alt="">
        <a href="../">
            <img src="<?=HOME_SITE?>image/Alizon_vendeur_noir.png" alt="logo alizon" title="logo alizon">
        </a>
        <h2>S'inscrire</h2>

        <form action="" method="post">
            <label for="raisonSocial">Raison sociale</label>
            <input type="text" 
                name="raisonSocial"
                id="raisonSocial"
                minlength="3"
                maxlength="60"
                placeholder="ARMOR LUX SAS"
                value="<?php if (isset($_POST['raisonSocial'])) echo htmlentities($_POST['raisonSocial'])?>"
                required
                class="champ">
            <p class="contrainte">Nom puis statut juridique</p>
<?php
    if (isset($erreurs['raison_sociale'])){
?>
            <p class="error">
                <?="Erreur : ".$erreurs['raison_sociale']?>
            </p>
<?php
    }
?>
            
            <label for="numSiret">Numéro de SIRET</label>
            <input type="text" 
                name="numSiret"
                id="numSiret"
                minlenght="14"
                placeholder="362 521 879 00034"
                value="<?php if (isset($_POST['numSiret'])) echo htmlentities($_POST['numSiret'])?>"
                required
                class="champ">
            <p class="contrainte">Numéro à 14 chiffres</p>
<?php
    if (isset($erreurs['numero_siret'])){
?>
            <p class="error">
                <?="Erreur : ".$erreurs['numero_siret']?>
            </p>
<?php
    }
?>

            
            <label for="numCobrec">Clé de la COBREC</label>
            <input type="text" 
                name="numCobrec"
                id="numCobrec"
                minlenght="15"
                placeholder="12345-12345-12345"
                value="<?php if (isset($_POST['numCobrec'])) echo htmlentities($_POST['numCobrec'])?>"
                required
                class="champ">
            <p class="contrainte">Numéro a 15 chiffres donné par la COBREC</p>
<?php
    if (isset($erreurs['numero_cobrec'])){
?>
            <p class="error">
                <?="Erreur : ".$erreurs['numero_cobrec']?>
            </p>
<?php
    }
?>

            
            <label for="email">Email</label>
            <input type="email"
                name="email"
                id="email"
                placeholder="exemple@email.com"
                value="<?php if (isset($_POST['email'])) echo htmlentities($_POST['email'])?>"
                required
                class="champ">
            <p class="contrainte"></p>
<?php
    if (isset($erreurs['email'])){
?>
            <p class="error">
                <?="Erreur : ".$erreurs['email']?>
            </p>
<?php
    }
?>


            
            <label for="adresse">Adresse</label>
            <input type="text"
                name="adresse"
                id="adresse"
                value="<?php if (isset($_POST['adresse'])) echo htmlentities($_POST['adresse'])?>"
                required
                class="champ">
            <p class="contrainte">Numéro, nom de la voie, commune</p>
<?php
    if (isset($erreurs['adresse'])){
?>
            <p class="error">
                <?="Erreur : ".$erreurs['adresse']?>
            </p>
<?php
    }
?>

            
            <label for="compAdresse">Complément d'adresse</label>
            <textarea type="text"
                name="compAdresse"
                id="compAdresse"
                class="champ text"><?php 
                if (isset($_POST['compAdresse'])) echo trim(htmlentities($_POST['compAdresse']))
            ?></textarea>
            
            <p class="contrainte">Informations complémentaires</p>

            
            <label for="codePostal">Code postal</label>
            <input type="number" 
                name="codePostal"
                id="codePostal"
                size="5"
                value="<?php if (isset($_POST['codePostal'])) echo htmlentities($_POST['codePostal'])?>"
                required
                class="champ">
            <p class="contrainte">Nombre à 5 chiffres</p>
<?php
    if (isset($erreurs['code_postal'])){
?>
            <p class="error">
                <?="Erreur : ".$erreurs['code_postal']?>
            </p>
<?php
    }
?>

            
            <label for="mdp">Mot de passe</label>
            <input type="password" 
                name="mdp"
                id="mdp"
                minlength="12"
                maxlength="100"
                required
                class="champ">
            <p class="contrainte">Minimum 12 caractères</p>
<?php
    if (isset($erreurs['mdp'])){
?>
            <p class="error">
                <?="Erreur : ".$erreurs['mdp']?>
            </p>
<?php
    }
?>

            
            <label for="mdpc">Mot de passe de confirmation</label>
            <input type="password" 
                name="mdpc"
                id="mdpc"
                minlength="12"
                maxlength="100"
                required
                class="champ">
            <p class="contrainte"></p>
<?php
    if (isset($erreurs['mdpc'])){
?>
            <p class="error">
                <?="Erreur : ".$erreurs['mdpc']?>
            </p>
<?php
    }
?>

            <input type="submit" value="S'inscrire" class="bouton">
        </form>
        <p style="text-align:center;">Déjà inscrit ? <a href="../">Se connecter</a>
        <br>
        <a href="../">Retourner au côté client</a></p>
<?php
    }
?>
    </main>
</body>
</html>
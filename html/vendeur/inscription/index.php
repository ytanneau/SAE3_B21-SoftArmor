<?php
    define('HOME_GIT', '../../../');
    //echo HOME_GIT . 'fonction_sql.php';
    if ($_POST != null){
        //echo "présence d'un post";
        //print_r($_ENV);
        $erreurs = [];
        $fichier = HOME_GIT . '/fonction_compte.php';
        if (file_exists($fichier)) {
            require_once $fichier;
            $erreurs = create_profile_vendeur($_POST['raisonSocial'], $_POST['numSiret'], $_POST['numCobrec'], $_POST['email'], $_POST['adresse'], $_POST['compAdresse'], $_POST['codePostal'], $_POST['mdp'], $_POST['mdpc'], HOME_GIT);

        } else {
            $erreurs['fatal'] = true;
            $fichierLog = __DIR__ . "/erreurs.log";
            $date = date("Y-m-d H:i:s");
            file_put_contents($fichierLog, "[$date] Failed find : require_once $fichier;\n", FILE_APPEND);
        }
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alizon Vendeur - Inscription</title>
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link rel="stylesheet" href="<?=HOME_GIT?>html/style.css">
</head>
<body id="inscription_vendeur">
    <main>
<?php
    if (isset($erreurs) && $erreurs == []) {
?>
        <h1>Félicitations vous avez crée votre compte</h1>
<?php
    }
    else if (isset($erreurs['fatal'])){
?>
        <h1 class="fatale">Désolé nous rencontrons des problèmes serveur</h1>
<?php
    }
    else{
?>
        <img src=""  alt="">
        <a href="../">
            <img src="<?=HOME_GIT?>html/image/Alizon_vendeur_noir.png" alt="logo alizon" title="logo alizon">
        </a>
        <h2>S'inscrire</h2>

        <form action="" method="post">
            <label for="raisonSocial">Raison Social</label>
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
            <br>
            <label for="numSiret">Numero de siret</label>
            <input type="text" 
                name="numSiret"
                id="numSiret"
                minlenght="14"
                placeholder="362 521 879 00034"
                value="<?php if (isset($_POST['numSiret'])) echo htmlentities($_POST['numSiret'])?>"
                required
                class="champ">
            <p class="contrainte">Numero a 14 chiffres</p>
<?php
    if (isset($erreurs['numero_siret'])){
?>
            <p class="error">
                <?="Erreur : ".$erreurs['numero_siret']?>
            </p>
<?php
    }
?>

            <br>
            <label for="numCobrec">Clé de la COBREC</label>
            <input type="text" 
                name="numCobrec"
                id="numCobrec"
                minlenght="15"
                placeholder="12345-12345-12345"
                value="<?php if (isset($_POST['numCobrec'])) echo htmlentities($_POST['numCobrec'])?>"
                required
                class="champ">
            <p class="contrainte">Numero a 15 chiffres donnée par la COBREC</p>
<?php
    if (isset($erreurs['numero_cobrec'])){
?>
            <p class="error">
                <?="Erreur : ".$erreurs['numero_cobrec']?>
            </p>
<?php
    }
?>

            <br>
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


            <br>
            <label for="adresse">Adresse</label>
            <input type="text"
                name="adresse"
                id="adresse"
                value="<?php if (isset($_POST['adresse'])) echo htmlentities($_POST['adresse'])?>"
                required
                class="champ">
            <p class="contrainte">Numero nom rue commune</p>
<?php
    if (isset($erreurs['adresse'])){
?>
            <p class="error">
                <?="Erreur : ".$erreurs['adresse']?>
            </p>
<?php
    }
?>

            <br>
            <label for="compAdresse">Compément adresse</label>
            <input type="text"
                name="compAdresse"
                id="compAdresse"
                value="<?php if (isset($_POST['compAdresse'])) echo htmlentities($_POST['compAdresse'])?>"
                class="champ">
            <p class="contrainte">information compémentaire</p>

            <br>
            <label for="codePostal">Code postal</label>
            <input type="number" 
                name="codePostal"
                id="codePostal"
                size="5"
                value="<?php if (isset($_POST['codePostal'])) echo htmlentities($_POST['codePostal'])?>"
                required
                class="champ">
            <p class="contrainte">Nombre a 5 chiffres</p>
<?php
    if (isset($erreurs['code_postal'])){
?>
            <p class="error">
                <?="Erreur : ".$erreurs['code_postal']?>
            </p>
<?php
    }
?>

            <br>
            <label for="mdp">Mot de passe</label>
            <input type="password" 
                name="mdp"
                id="mdp"
                minlength="12"
                maxlength="100"
                required
                class="champ">
            <p class="contrainte">minum 12 caractères</p>
<?php
    if (isset($erreurs['mdp'])){
?>
            <p class="error">
                <?="Erreur : ".$erreurs['mdp']?>
            </p>
<?php
    }
?>

            <br>
            <label for="mdpc">Mot de passe de comfirmation</label>
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
        <p>Déjà inscrit ? <a href="../">Se connecter</a></p>
<?php
    }
?>
    </main>
</body>
</html>
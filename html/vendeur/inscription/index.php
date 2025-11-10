<?php
    define('HOME_GIT', '../../../');
    $res = [];
    //echo HOME_GIT . 'fonction_sql.php';
    if ($_POST != null){
        //echo "présence d'un post";
        //print_r($_ENV);
        $fichier = getenv('HOME_GIT') . '/fonction_compte.php';
        if (file_exists($fichier)) {
            require_once $fichier;
            $res = create_profile_vendeur($_POST['raisonSocial'], $_POST['numSiret'], $_POST['numCobrec'], $_POST['email'], $_POST['adresse'], $_POST['compAdresse'], $_POST['codePostal'], $_POST['mdp'], $_POST['mdpc']);

        } else {
            echo "erreur 1";
            $res['fatal'] = true;
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
    <title>Inscription Vendeur</title>
</head>
<body>
    <main>
<?php
    if (isset($res['correcte']) && $res['correcte']) {
?>
        <h1>Félicitations vous avez crée votre compte</h1>
<?php
    }
    else if (isset($res['fatal'])){
?>
        <h1 class="fatale">Désolé nous rencontrons des problèmes serveur</h1>
<?php
    }
    else{
?>
        <form action="" method="post">
            <label for="raisonSocial">Raison Social</label>
            <input type="text" 
                name="raisonSocial"
                id="raisonSocial"
                minlength="3"
                maxlength="60"
                placeholder="ARMOR LUX SAS"
                value="<?php if (isset($_POST['raisonSocial'])) echo $_POST['raisonSocial']?>"
                required>
            <p class="contrainte">Nom puis statut juridique</p>
<?php
    if (isset($res['taison_sociale'])){
?>
            <p class="error">
                <?="Erreur : ".$res['taison_sociale']?>
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
                value="<?php if (isset($_POST['numSiret'])) echo $_POST['numSiret']?>"
                required>
            <p class="contrainte">Numero a 14 chiffres</p>
<?php
    if (isset($res['numero_siret'])){
?>
            <p class="error">
                <?="Erreur : ".$res['numero_siret']?>
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
                value="<?php if (isset($_POST['numCobrec'])) echo $_POST['numCobrec']?>"
                required>
            <p class="contrainte">Numero a 15 chiffres donnée par la COBREC</p>
<?php
    if (isset($res['numero_cobrec'])){
?>
            <p class="error">
                <?="Erreur : ".$res['numero_cobrec']?>
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
                value="<?php if (isset($_POST['email'])) echo $_POST['email']?>"
                required>
            <p class="contrainte"></p>
<?php
    if (isset($res['email'])){
?>
            <p class="error">
                <?="Erreur : ".$res['email']?>
            </p>
<?php
    }
?>


            <br>
            <label for="adresse">Adresse</label>
            <input type="text"
                name="adresse"
                id="adresse"
                value="<?php if (isset($_POST['adresse'])) echo $_POST['adresse']?>"
                required>
            <p class="contrainte">Numero nom rue commune</p>
<?php
    if (isset($res['adresse'])){
?>
            <p class="error">
                <?="Erreur : ".$res['adresse']?>
            </p>
<?php
    }
?>

            <br>
            <label for="compAdresse">Compément adresse</label>
            <input type="text"
                name="compAdresse"
                id="compAdresse"
                value="<?php if (isset($_POST['compAdresse'])) echo $_POST['compAdresse']?>">
            <p class="contrainte">information compémentaire</p>
<?php
    if (isset($res['adresse'])){
?>
            <p class="error">
                <?="Erreur : ".$res['adresse']?>
            </p>
<?php
    }
?>

            <br>
            <label for="codePostal">Code postal</label>
            <input type="number" 
                name="codePostal"
                id="codePostal"
                size="5"
                value="<?php if (isset($_POST['codePostal'])) echo $_POST['codePostal']?>"
                required>
            <p class="contrainte">Nombre a 5 chiffres</p>
<?php
    if (isset($res['code_postal'])){
?>
            <p class="error">
                <?="Erreur : ".$res['code_postal']?>
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
                required>
            <p class="contrainte">minum 12 caractères</p>
<?php
    if (isset($res['mdp'])){
?>
            <p class="error">
                <?="Erreur : ".$res['mdp']?>
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
                required>
            <p class="contrainte"></p>
<?php
    if (isset($res['mdpc'])){
?>
            <p class="error">
                <?="Erreur : ".$res['mdpc']?>
            </p>
<?php
    }
?>

            <input type="submit" value="S'inscrire">
            <p>Déjà inscrit ? <a href="../">Se connecter</a></p>
        </form>
<?php
    }
?>
    </main>
</body>
</html>
<?php
    $res = [];
    if ($_POST != null){
        require_once "../../../fonction_compte.php";
        echo "présence d'un post";

        $res = create_profile_vendeur($_POST['raisonSocial'], $_POST['numSiret'], $_POST['numCobrec'], $_POST['email'], $_POST['adresse'], $_POST['codePostal'], $_POST['mdp'], $_POST['mdpc'], '../../../');
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
    if ($res === true) {
?>
        <h1>Félisitation vous avez crée votre compte</h1>
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
    if (isset($res['RS'])){
?>
            <p class="error">
                <?="Erreur : ".$res['RS']?>
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
    if (isset($res['NS'])){
?>
            <p class="error">
                <?="Erreur : ".$res['NS']?>
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
    if (isset($res['NC'])){
?>
            <p class="error">
                <?="Erreur : ".$res['NC']?>
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
    if (isset($res['EM'])){
?>
            <p class="error">
                <?="Erreur : ".$res['EM']?>
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
            <p class="contrainte">Numero rue commune</p>
<?php
    if (isset($res['AD'])){
?>
            <p class="error">
                <?="Erreur : ".$res['AD']?>
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
            <p class="contrainte"></p>
<?php
    if (isset($res['CP'])){
?>
            <p class="error">
                <?="Erreur : ".$res['CP']?>
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
                value="<?php if (isset($_POST['mdp'])) echo $_POST['mdp']?>"
                required>
            <p class="contrainte"></p>
<?php
    if (isset($res['MDP'])){
?>
            <p class="error">
                <?="Erreur : ".$res['MDP']?>
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
                value="<?php if (isset($_POST['mdpc'])) echo $_POST['mdpc']?>"
                required>
            <p class="contrainte"></p>
<?php
    if (isset($res['MDPC'])){
?>
            <p class="error">
                <?="Erreur : ".$res['MDPC']?>
            </p>
<?php
    }
?>

            <input type="submit" value="Crée mon compte">
        </form>
<?php
    }
?>
    </main>
</body>
</html>
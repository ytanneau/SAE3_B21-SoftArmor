<?php
    const HOME_GIT = '../../../';
    
    if ($_POST != null){
        if (!isset($_POST['nom'])) $_POST['nom'] = "";
        if (!isset($_POST['prenom'])) $_POST['prenom'] = "";
        if (!isset($_POST['pseudo'])) $_POST['pseudo'] = "";
        if (!isset($_POST['email'])) $_POST['email'] = "";
        if (!isset($_POST['date_naissance'])) $_POST['date_naissance'] = "";
        if (!isset($_POST['mdp'])) $_POST['mdp'] = "";
        if (!isset($_POST['mdpc'])) $_POST['mdpc'] = "";

        $erreurs = [];

        //echo "présence d'un post";
        //print_r($_ENV);
        $fichier = HOME_GIT . 'fonction_compte.php';
        if (file_exists($fichier)) {
            require_once $fichier;
            $erreurs = create_profile_client($_POST['email'], $_POST['nom'], $_POST['prenom'], $_POST['pseudo'], $_POST['date_naissance'], $_POST['mdp'], $_POST['mdpc']);
        } else {
            // echo "erreur 1";

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
    <title>Alizon - Inscription</title>
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link rel="stylesheet" href="<?=HOME_GIT?>style.css">
</head>
<body id="inscription_client">
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
        <img src="" alt="">
        <a href="../">
            <img src="<?=HOME_GIT?>image/Alizon_noir.png" alt="logo alizon" title="logo alizon">
        </a>
        <h2>S'inscrire</h2>
        
        <form action="" method="post">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" maxlength="40" value="<?php if (isset($_POST["nom"])) echo $_POST["nom"]?>"
            class="champ">
            <p class="contrainte"></p>
<?php
    if (isset($erreurs['nom'])){
?>
            <p class="error">
                <?="Erreur : ".$erreurs['nom']?>
            </p>
<?php
    }
?>

            <br>
            <label for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom" maxlength="40" value="<?php if (isset($_POST["prenom"])) echo $_POST["prenom"]?>"
            class="champ">
            <p class="contrainte"></p>
<?php
    if (isset($erreurs['prenom'])){
?>
            <p class="error">
                <?="Erreur : ".$erreurs['prenom']?>
            </p>
<?php
    }
?>

            <br>
            <label for="pseudo">Pseudo</label>
            <input type="text" id="pseudo" name="pseudo" maxlength="40" value="<?php if (isset($_POST["pseudo"])) echo $_POST["pseudo"]?>"
            class="champ">
            <p class="contrainte"></p>
<?php
    if (isset($erreurs['pseudo'])){
?>
            <p class="error">
                <?="Erreur : ".$erreurs['pseudo']?>
            </p>
<?php
    }
?>

            <br>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" maxlength="80" value="<?php if (isset($_POST["email"])) echo $_POST["email"]?>"
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
            <label for="date_naissance">Date de naissance</label>
            <input type="date" id="date_naissance" name="date_naissance"
            class="champ">
            <p class="contrainte"></p>
<?php
    if (isset($erreurs['date_naiss'])){
?>
            <p class="error">
                <?="Erreur : ".$erreurs['date_naiss']?>
            </p>
<?php
    }
?>

            <br>
            <label for="mdp">Mot de passe</label>
            <input type="password" name="mdp" id="mdp" minlength="12" maxlength="100" required
            class="champ">
            <p class="contrainte">minimum 12 caractères</p>
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
            <label for="mdpc">Mot de passe de confirmation</label>
            <input type="password" name="mdpc" id="mdpc" minlength="12" maxlength="100" required
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

            <input type="submit" value="Crée mon compte" class="bouton">
        </form>
<?php
    }
?>
    </main>
</body>
</html>
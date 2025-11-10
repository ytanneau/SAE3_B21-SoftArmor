<?php
    const HOME_GIT = '../../../';
    $res = [];
    
    if ($_POST != null){
        if (!isset($_POST['nom'])) $_POST['nom'] = "";
        if (!isset($_POST['prenom'])) $_POST['prenom'] = "";
        if (!isset($_POST['pseudo'])) $_POST['pseudo'] = "";
        if (!isset($_POST['email'])) $_POST['email'] = "";
        if (!isset($_POST['date_naissance'])) $_POST['date_naissance'] = "";
        if (!isset($_POST['mdp'])) $_POST['mdp'] = "";
        if (!isset($_POST['mdpc'])) $_POST['mdpc'] = "";

        //echo "présence d'un post";
        //print_r($_ENV);
        $fichier = HOME_GIT . 'fonction_compte.php';
        if (file_exists($fichier)) {
            require_once $fichier;
            $res = create_profile_client($_POST['email'], $_POST['nom'], $_POST['prenom'], $_POST['pseudo'], $_POST['date_naissance'], $_POST['mdp'], $_POST['mdpc']);
        } else {
            // echo "erreur 1";

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
    <title>Alizon - Inscription Client</title>
</head>
<body>
    <main>

    <a href=<?= HOME_GIT ?>>Revenir à l'accueil</a>

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
        <h1>Inscription client sur Alizon</h1>
        
        <form action="" method="post">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" maxlength="40" value="<?php if (isset($_POST["nom"])) echo $_POST["nom"]?>">
            <p class="contrainte"></p>
<?php
    if (isset($res['nom'])){
?>
            <p class="error">
                <?="Erreur : ".$res['nom']?>
            </p>
<?php
    }
?>

            <br>
            <label for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom" maxlength="40" value="<?php if (isset($_POST["prenom"])) echo $_POST["prenom"]?>">
            <p class="contrainte"></p>
<?php
    if (isset($res['prenom'])){
?>
            <p class="error">
                <?="Erreur : ".$res['prenom']?>
            </p>
<?php
    }
?>

            <br>
            <label for="pseudo">Pseudo</label>
            <input type="text" id="pseudo" name="pseudo" maxlength="40" value="<?php if (isset($_POST["pseudo"])) echo $_POST["pseudo"]?>">
            <p class="contrainte"></p>
<?php
    if (isset($res['pseudo'])){
?>
            <p class="error">
                <?="Erreur : ".$res['pseudo']?>
            </p>
<?php
    }
?>

            <br>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" maxlength="80" value="<?php if (isset($_POST["email"])) echo $_POST["email"]?>">
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
            <label for="date_naissance">Date de naissance</label>
            <input type="date" id="date_naissance" name="date_naissance">
            <p class="contrainte"></p>
<?php
    if (isset($res['date_naiss'])){
?>
            <p class="error">
                <?="Erreur : ".$res['date_naiss']?>
            </p>
<?php
    }
?>

            <br>
            <label for="mdp">Mot de passe</label>
            <input type="password" name="mdp" id="mdp" minlength="12" maxlength="100" required>
            <p class="contrainte">minimum 12 caractères</p>
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
            <label for="mdpc">Mot de passe de confirmation</label>
            <input type="password" name="mdpc" id="mdpc" minlength="12" maxlength="100" required>
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

            <input type="submit" value="Crée mon compte">
        </form>
<?php
    }
?>
    </main>
</body>
</html>
<?php
    
    define('HOME_SITE', '../../');
    define('HOME_GIT', '../../../');
    
    if ($_POST != null){
        if (!isset($_POST['nom'])) $_POST['nom'] = "";
        if (!isset($_POST['prenom'])) $_POST['prenom'] = "";
        if (!isset($_POST['pseudo'])) $_POST['pseudo'] = "";
        if (!isset($_POST['email'])) $_POST['email'] = "";
        if (!isset($_POST['date_naissance'])) $_POST['date_naissance'] = "";
        if (!isset($_POST['mdp'])) $_POST['mdp'] = "";
        if (!isset($_POST['mdpc'])) $_POST['mdpc'] = "";
        if (!isset($_POST['question'])) $_POST['question'] = "";
        if (!isset($_POST['reponse'])) $_POST['reponse'] = "";

        $erreurs = [];

        $fichier = HOME_GIT . 'fonction_compte.php';
        if (file_exists($fichier)) {
            require_once $fichier;
            $erreurs = create_profile_client($_POST['email'], $_POST['nom'], $_POST['prenom'], $_POST['pseudo'], $_POST['date_naissance'], $_POST['mdp'], $_POST['mdpc'], $_POST['question'], $_POST['reponse']);
        } else {
            $erreurs['fatal'] = true;
            $fichierLog = __DIR__ . "/erreurs.log";
            $date = date("Y-m-d H:i:s");
            file_put_contents($fichierLog, "[$date] Failed find : require_once $fichier;\n", FILE_APPEND);
        }
    }

    
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        $param = "";
        if (isset($_GET['produit'])) {
            if ($_GET['produit'] == 'panier') {
                $page = '../../panier';
            } else {
                $page = '../../produit?produit=' . $_GET['produit'];
            }
        }
        
        // Si l'utilisateur se connecte après avoir essayé d'acheter un produit sans se connecter, alors il est redirigé vers ce produit après connexion
        header('Location: ' . HOME_SITE . $page ?? '');
        exit;
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include HOME_SITE . 'link_head.php' ?>
    <title>Alizon - Inscription</title>
</head>
<body id="inscription_client">
    <main>
        <?php if (isset($erreurs) && $erreurs == []) { ?>
            <h1>Félicitations, vous avez créé votre compte</h1>
            <p>Prochaine étape : <a href="<?=HOME_SITE?>compte/connexion<?php if (isset($_GET['produit'])) echo "?produit=" . $_GET['produit']?>">Se connecter</a></p>

        <?php } else if (isset($erreurs['fatal'])) { ?>
            <h1 class="fatale">Désolé nous rencontrons des problèmes serveur</h1>
        <?php } else { ?>
            <a href="<?= HOME_SITE ?>">
                <img src="<?=HOME_SITE?>image/Alizon_noir.png" alt="logo alizon" title="logo alizon">
            </a>
            
            <h2>S'inscrire</h2>
            
            <form action="" method="post">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" maxlength="40" value="<?= $_POST["nom"] ?? ''?>"
                class="champ">
                <p class="contrainte"></p>

                <?php if (isset($erreurs['nom'])) { ?>
                    <p class="error">
                        <?="Erreur : ".$erreurs['nom']?>
                    </p>
                <?php } ?>

                
                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" maxlength="40" value="<?=$_POST['prenom'] ?? ''?>" class="champ">
                <p class="contrainte"></p>

                <?php if (isset($erreurs['prenom'])) { ?>
                    <p class="error">
                        <?="Erreur : ".$erreurs['prenom']?>
                    </p>
                <?php } ?>

                
                <label for="pseudo">Pseudo</label>
                <input type="text" id="pseudo" name="pseudo" maxlength="40" value="<?=$_POST['pseudo'] ?? ''?>"
                class="champ">
                <p class="contrainte"></p>

                <?php if (isset($erreurs['pseudo'])) { ?>
                    <p class="error">
                        <?="Erreur : ".$erreurs['pseudo']?>
                    </p>
                <?php } ?>

                
                <label for="email">Email</label>
                <input type="email" id="email" name="email" maxlength="80" value="<?=$_POST['email'] ?? ''?>"
                class="champ">
                <p class="contrainte"></p>

                <?php if (isset($erreurs['email'])) { ?>
                    <p class="error">
                        <?="Erreur : ".$erreurs['email']?>
                    </p>
                <?php } ?>

                
                <label for="date_naissance">Date de naissance</label>
                <input type="date" id="date_naissance" name="date_naissance"
                class="champ" value="<?=$_POST['date_naissance'] ?? ''?>">
                <p class="contrainte"></p>

                <?php if (isset($erreurs['date_naiss'])) { ?>
                    <p class="error">
                        <?="Erreur : ".$erreurs['date_naiss']?>
                    </p>
                <?php } ?>

                
                <label for="mdp">Mot de passe</label>
                <input type="password" name="mdp" id="mdp" minlength="12" maxlength="100" required
                class="champ">
                <p class="contrainte">minimum 12 caractères</p>

                <?php if (isset($erreurs['mdp'])){ ?>
                    <p class="error">
                        <?="Erreur : ".$erreurs['mdp']?>
                    </p>
                <?php } ?>

                
                <label for="mdpc">Mot de passe de confirmation</label>
                <input type="password" name="mdpc" id="mdpc" minlength="12" maxlength="100" required
                class="champ">
                <p class="contrainte"></p>

                <?php if (isset($erreurs['mdpc'])) { ?>
                    <p class="error">
                        <?="Erreur : ".$erreurs['mdpc']?>
                    </p>
                <?php } ?>

                
                <label for="question">Question secrète</label>
                <select id="question" name="question" class="champ">
                    <option value="">Sélectionnez une question secrète</option>
                    <option value="mere">Quel était le nom de famille de votre mère ?</option>
                    <option value="animal">Quel était le nom de votre premier animal de compagnie ?</option>
                    <option value="professeur">Quel était le nom de votre professeur préféré ?</option>
                </select>

                <?php if (isset($erreurs['question'])) { ?>
                    <p class="error">
                        <?="Erreur : ".$erreurs['question']?>
                    </p>
                <?php } ?>

                
                <label for="reponse">Votre réponse</label>
                <input type="password" name="reponse" id="reponse" class="champ">
                <p class="contrainte"></p>

                <?php if (isset($erreurs['reponse'])) { ?>
                    <p class="error">
                        <?="Erreur : ".$erreurs['reponse']?>
                    </p>
                <?php } ?>

                <input type="submit" value="Créer mon compte" class="bouton">
            </form>
            
            <p>Déjà inscrit ? <a href="<?=HOME_SITE?>compte/connexion<?php if (isset($_GET['produit'])) echo "?produit=" . $_GET['produit']?>">Se connecter</a></p>
        <?php } ?>

    </main>
</body>
</html>
<?php
const HOME_GIT = "../../";
const HOME_SITE = "../";

if (!isset($_SESSION)) {
    session_start();
}

// Si pas co, alors go page connexion
if (!isset($_SESSION['logged_in'])) {
    header("location: " . HOME_SITE . "compte/connexion");
}


require_once HOME_GIT . "fonction_produit.php";
require_once HOME_GIT . ".config.php";
require_once HOME_GIT . "fonction_global.php";

$numEtape = -1;

// $_POST['form'] = le nom du dernier formulaire envoyé par l'utilisateur
if (!isset($_POST['form'])) {
    $numEtape = 1;

    // si y'a pas de produit dans le lien, ou que c'est pas par le panier qu'on a atteint cette page, alors problème
    if (!isset($_GET['produit'])) {
        header("location: " . HOME_GIT);
    }

    
    // récup les données adresse préenregistrées dans la base de données
    
    $requete = $pdo->prepare("SELECT adresse, code_postal, complement_adresse FROM client_adresse WHERE id_compte = :id_client");
    $requete->bindValue(":id_client", $_SESSION['id_compte'], PDO::PARAM_STR);
    $requete->execute();
    
    $adresse_client = $requete->fetch(PDO::FETCH_ASSOC);
    if ($adresse_client == false) {
        $adresse_client = [];
    }
}





// si dernier formulaire envoyé est 'adresse'
else if ($_POST['form'] == 'adresse') {

    // gestion du POST des données adresse 

    // met chaînes vides aux colonnes au lieu de null pour éviter erreurs
    if (!isset($_POST['adresse'])) $_POST['adresse'] = "";
    if (!isset($_POST['complement_adresse'])) $_POST['complement_adresse'] = "";
    if (!isset($_POST['code_postal'])) $_POST['code_postal'] = "";

    $fichier = HOME_GIT . 'fonction_compte.php';
    if (file_exists($fichier)) {
        require_once $fichier;
        $erreurs = check_coordonnees($_POST['adresse'], $_POST['code_postal']);


        // enregistrer
        if ($erreurs == [] && isset($_POST['enregistrer']) && $_POST['enregistrer']) {
            sql_insert_adresse_client($pdo, $_SESSION['id_compte'], $_POST['adresse'], $_POST['complement_adresse'], $_POST['code_postal']);
        }

    } else {
        // pas d'accès au fichier fonctions

        $erreurs['fatal'] = true;
        // $fichierLog = __DIR__ . "/erreurs.log";
        // $date = date("Y-m-d H:i:s");
        // file_put_contents($fichierLog, "[$date] Failed find : require_once $fichier;\n", FILE_APPEND);
    }

    
    if ($erreurs == []) {
        // si aucune erreur, alors on passe à l'étape suivante
        $numEtape = 2;

    } else {
        // sinon reste sur étape 1
        $numEtape = 1;

        // récup les anciennes valeurs remplies pour préremplir les champs d'adresse
        $adresse_client['adresse'] = $_POST['adresse'];
        $adresse_client['complement_adresse'] = $_POST['complement_adresse'];
        $adresse_client['code_postal'] = $_POST['code_postal'];
    }
}






// si dernier formulaire envoyé est 'bancaire'
else if ($_POST['form'] == 'bancaire') {

    // gestion du POST des données coordonnées bancaires

    if (isset($_POST)) {

        if (!isset($_POST['code_carte'])) $_POST['code_carte'] = "";
        if (!isset($_POST['date_exp'])) $_POST['date_exp'] = "";
        if (!isset($_POST['code_securite'])) $_POST['code_securite'] = "";
    
        $fichier = HOME_GIT . 'fonction_compte.php';
        if (file_exists($fichier)) {
            require_once $fichier;
            $erreurs = check_coordonnees_bancaires($_POST['code_carte'], $_POST['date_exp'], $_POST['code_securite']);
    
    
        } else {
            // pas d'accès au fichier fonctions
    
            $erreurs['fatal'] = true;
            // $fichierLog = __DIR__ . "/erreurs.log";
            // $date = date("Y-m-d H:i:s");
            // file_put_contents($fichierLog, "[$date] Failed find : require_once $fichier;\n", FILE_APPEND);
        }
    
        if ($erreurs == []) {
            // si aucune erreur, alors on passe à l'étape suivante
            $numEtape = 3;
        } else {
            // sinon, reste sur étape 2
            $numEtape = 2;
        }
    }
}


// si le client a bien répondu à tous les formulaire, alors une commande est créée et enregistrée
if ($numEtape == 3) {
    $CHEMIN_FACTURE = HOME_GIT . "html/ressources/facture/";


    $requete = $pdo->prepare("INSERT INTO _commande (id_client, chemin_fichier) VALUES (:id_compte, 'ATTENTE')");
    $requete->bindValue(":id_compte", $_SESSION['id_compte'], PDO::PARAM_INT);
    $requete->execute();

    $requete = $pdo->prepare("SELECT id_commande FROM _commande WHERE id_client = :id_compte AND chemin_fichier = 'ATTENTE'");
    $requete->bindValue(":id_compte", $_SESSION['id_compte'], PDO::PARAM_INT);
    $requete->execute();
    $id_commande = $requete->fetch(PDO::FETCH_ASSOC)['id_commande'];

    $nom_fichier = $_SESSION['id_compte'] . "_" . $id_commande;
    $requete = $pdo->prepare("UPDATE _commande SET chemin_fichier = :chemin");
    $requete->bindValue(":chemin", "$CHEMIN_FACTURE" . $nom_fichier);
    $requete->execute();


    $requete = $pdo->prepare("SELECT nom, prenom, pseudo FROM compte_client WHERE id_compte = :id_compte");
    $requete->bindValue(":id_compte", $_SESSION['id_compte'], PDO::PARAM_INT);
    $requete->execute();
    $client = $requete->fetch(PDO::FETCH_ASSOC);

    
    $contenu_fichier = $client['nom'] . " " . $client['prenom'] . "\n";

    date_default_timezone_set('Europe/Paris'); // met la timezone à Paris pour récup la date
    $contenu_fichier .= "Date d'achat : " . date("l d M Y, H:i:s\n");

    if ($_POST['id_produit'] != 'panier') {
        $stock = detail_produit($_POST['id_produit'])['quantite'];
        if ($stock >= 1) {

            $requete = $pdo->prepare("SELECT nom_public, prix, tva FROM produit WHERE id_produit = :id_produit");
            $requete->bindValue(":id_produit", $_POST['id_produit']);
            $requete->execute();
            $produit = $requete->fetch(PDO::FETCH_ASSOC);

            $contenu_fichier .= "Produit acheté : " . $produit['nom_public'] . "\n";
            $contenu_fichier .= "\tPrix HT \t: " . $produit['prix'] . "€\n";
            $contenu_fichier .= "\tTaux de taxe\t : " . $produit['tva'] . "%\n";
            $contenu_fichier .= "\tPrix TTC \t: " . $produit['prix'] * $produit['tva'] / 100 . "€\n";

            update_stock($_POST['id_produit'], "-1");
        } else {
            echo "Une erreur est survenue ! (le produit n'est plus en stock)";
        }
    } else {
        $requete = $pdo->prepare("SELECT id_produit, nom_public, prix, tva, quantite_panier FROM produit_panier WHERE id_client = :id_compte");
        $requete->bindValue(":id_compte", $_SESSION['id_compte'], PDO::PARAM_INT);
        $requete->execute();
        $liste_produits = $requete->fetchAll(PDO::FETCH_ASSOC);

        $produits_plus_en_stock = [];

        foreach ($liste_produits as $produit) {
            if (detail_produit($produit['id_produit'])['quantite'] < $produit['quantite_panier']) {
                array_push($produits_plus_en_stock, $produit);
            }
        }
        if ($produits_plus_en_stock == []) {
            $contenu_fichier .= "Liste des produits achetés : \n";
            foreach ($liste_produits as $produit) {
                $contenu_fichier .= "-> " . $produit['nom_public'] . " (x" . $produit['quantite_panier'] . ")\n";
                $contenu_fichier .= "\tPrix HT unitaire \t: " . $produit['prix'] . "€\n";
                $contenu_fichier .= "\tTaux de taxe \t\t: " . $produit['tva'] . "%\n";
                $contenu_fichier .= "\tPrix TTC unitaire \t: " . $produit['prix'] * $produit['tva'] / 100 . "€\n";
                $contenu_fichier .= "\tPrix TTC total \t\t: " . ($produit['prix'] * $produit['tva'] / 100) * $produit['quantite_panier'] . "€\n\n";

                update_stock($produit['id_produit'], '-' . $produit['quantite_panier']);
            }
        } else {
            echo "Une erreur est survenue ! (un ou plusieurs produits ne sont plus en stock)";
            foreach ($produits_plus_en_stock as $produit) {
                echo "\n- " . $produit['nom_public'];
            }
        }
    }

    $fichier = fopen($CHEMIN_FACTURE . $nom_fichier, 'w');
    fwrite($fichier, $contenu_fichier);
    fclose($fichier);
    $_POST = [];
}


?>


<!DOCTYPE html>
<html>
    <head>
        <title>Alizon - Achat</title>
        <meta charset="UTF-8">
        <meta lang="fr">
        <?php include HOME_SITE . 'link_head.php' ?>
    </head>

    <body class="form_client">
        <?php include HOME_SITE . 'header.php'; ?>

        <main>
            

<?php


// fomulaire coordonnées physiques (adresse)
if ($numEtape == 1) {
?>

        <form action="" method="post">
            <a href="../"><img src="../image/retour.svg"></a>

            <h2>Entrez votre adresse</h2>

            <label for="adresse">Adresse</label>
            <input type="text" name="adresse" id="adresse" value="<?=htmlentities($adresse_client['adresse'] ?? '')?>" required class="champ">
            <?php
            if (isset($erreurs['adresse'])){
                ?>
            <p class="error">
                <?="Erreur : ".$erreurs['adresse']?>
            </p>
            <?php
            }
            ?>
            <p class="contrainte">ex: 12 rue de la Gare, Paris</p>

            <label for="complement_adresse">Complément adresse</label>
            <textarea type="text" name="complement_adresse" id="complement_adresse" class="champ text"><?=htmlentities($adresse_client['complement_adresse'] ?? '')?></textarea>
            <p class="contrainte">informations complémentaires</p>


            <br>
            <label for="code_postal">Code postal</label>
            <input type="number" name="code_postal" id="code_postal" size="5" value="<?=htmlentities($adresse_client['code_postal'] ?? '')?>" required class="petit champ">
            <?php
            if (isset($erreurs['code_postal'])){
                ?>
            <p class="error">
                <?="Erreur : ".$erreurs['code_postal']?>
            </p>
            <?php
            }
            ?>
            <p class="contrainte">Nombre à 5 chiffres</p>

            <?php if (!isset($adresse_client['adresse'])) { ?>
            <label for="enregistrer">Enregistrer l'adresse
            <input type="checkbox" id="enregistrer" name="enregistrer" ></label>
            <?php } ?>


            <input type="hidden" name="id_produit" id="id_produit" required value="<?php if (isset($_GET['produit'])) {echo htmlentities($_GET['produit']);} else {echo "panier";}?>">
            <input type="hidden" name="form" id="form" required value="adresse">
            
            <input type="submit" value="Continuer l'achat" class="bouton">

        </form>

<?php }





// formulaire coordonnées bancaires
else if ($numEtape == 2) {
?>

        <form action="" method="post">
            <h2>Entrez vos coordonnées bancaires</h2>

            <label for="code_carte">Code de carte bancaire</label>
            <input type="text" name="code_carte" id="code_carte" required class="champ"  maxlength="16">
            <?php
            if (isset($erreurs['code_carte'])){
                ?>
            <p class="error">
                <?="Erreur : ".$erreurs['code_carte']?>
            </p>
            <?php
            }
            ?>
            <p class="contrainte">16 chiffres ex: 1234567891234567</p>


            <label for="date_exp">Date d'expiration de la carte</label>
            <input type="text" name="date_exp" id="date_exp" size="5" required class="petit champ">
            <?php
            if (isset($erreurs['date_exp'])){
                ?>
            <p class="error">
                <?="Erreur : ".$erreurs['date_exp']?>
            </p>
            <?php
            }
            ?>
            <p class="contrainte">MM/YY ex: 12/25, 01/26</p>


            <label for="code_securite">Code de sécurité</label>
            <input type="number" name="code_securite" id="code_securite" min="100" max="999" required class="petit champ">
            <?php
            if (isset($erreurs['code_securite'])){
                ?>
            <p class="error">
                <?="Erreur : ".$erreurs['code_securite']?>
            </p>
            <?php
            }
            ?>
            <p class="contrainte">Nombre à 3 chiffres</p>

            <input type="hidden" type="number" name="id_produit" id="id_produit" required value="<?=$_POST['id_produit']?>">
            <input type="hidden" name="form" id="form" required value="bancaire">
            
            <input type="submit" value="Effectuer l'achat" class="bouton">

        </form>

<?php
}
    else if ($numEtape == 3) {
        echo "<h2>Bravo vous avez réussi à effectuer l'achat !</h2>";
        echo "<a href=<?=HOME_SITE?>>Revenir à l'accueil</a>";
    }

?>
        </main>
    </body>
</html>
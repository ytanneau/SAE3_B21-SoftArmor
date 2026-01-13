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
require_once HOME_GIT . "fonction_commande.php";
require_once HOME_GIT . "fonction_compte.php";

$numEtape = -1;

// $_POST['form'] = le nom du dernier formulaire envoyé par l'utilisateur
if (!isset($_POST['form'])) {
    $numEtape = 1;

    // si y'a pas de produit dans le lien, ou que c'est pas par le panier qu'on a atteint cette page, alors problème
    if (!isset($_GET['produit'])) {
        header("location: " . HOME_GIT);
    }

    
    // récup les données adresse préenregistrées dans la base de données
    
    $infos_client = sql_get_info_compte($_SESSION['id_compte']);
    $adresse_client = isset($infos_client['id_adresse_fac']) ? sql_get_adresse($infos_client['id_adresse_fac']) : [];
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
    $liste_produits = [];
    $achat_reussi = true;

    //connexion au delivraptor
    $conn =false;
    $fd = connexion_socket();
    if($fd){
        $conn = connexion_delivraptor($fd,"root","root");
        //creer bordereau colis
        $bordereau = create_colis($fd);
        deconnexion_socket($fd);
    }

    // Création commande
    $requete = $pdo->prepare("INSERT INTO _commande (id_client,bordereau) VALUES (:id_compte,:bordereau)");
    $requete->bindValue(":id_compte", $_SESSION['id_compte'], PDO::PARAM_INT);
    $requete->bindValue(":bordereau", $bordereau, PDO::PARAM_INT);
    $requete->execute();

    // Récupération de l'id de commande
    $id_commande = $pdo->lastInsertId();

    // Si c'est un produit unique (pas un panier)
    if ($_POST['id_produit'] != 'panier') {
        $produit = detail_produit($_POST['id_produit']);

        if ($produit["quantite"] <= 0) {
            $produits_plus_en_stock = [$produit];
            $achat_reussi = false;

        } else {

            $requete = $pdo->prepare("SELECT prix, nom_public AS nom_produit, raison_sociale AS nom_vendeur
            FROM produit
            INNER JOIN vendeur ON _produit.id_vendeur = _vendeur.id_compte
            WHERE id_produit = :id_produit");
            $requete->bindValue(":id_produit", $_POST['id_produit']);
            $requete->execute();
            $produit = $requete->fetch(PDO::FETCH_ASSOC);

            $liste_produits[] = [
                "id_produit" => $_POST["id_produit"],
                "prix" => $produit["prix"],
                "quantite" => 1,
                "nom_produit" => $produit["nom_produit"],
                "nom_vendeur" => $produit["nom_vendeur"]
            ];

            update_stock($_POST['id_produit'], "-1");
            ajout_commande($id_commande, $liste_produits);
        }

    // Sinon si c'est un panier
    } else {
        $requete = $pdo->prepare("SELECT nom_public AS nom_produit, id_produit, prix, quantite_panier AS quantite, _vendeur.raison_sociale AS nom_vendeur
        FROM produit_panier
        INNER JOIN _vendeur ON produit_panier.id_vendeur = _vendeur.id_compte
        WHERE id_client = :id_compte");
        $requete->bindValue(":id_compte", $_SESSION['id_compte'], PDO::PARAM_INT);
        $requete->execute();
        $liste_produits = $requete->fetchAll(PDO::FETCH_ASSOC);

        $produits_plus_en_stock = [];

        foreach ($liste_produits as $produit) {
            if (detail_produit($produit['id_produit'])['quantite'] < $produit['quantite']) {
                array_push($produits_plus_en_stock, $produit);
                $achat_reussi = false;
            }
        }

        if ($achat_reussi) {
            foreach ($liste_produits as $produit) {
                update_stock($produit['id_produit'], '-' . $produit['quantite']);
            }

            ajout_commande($id_commande, $liste_produits);
            header("location: " . HOME_SITE . "commande/?commande=" . $id_commande);

        }
    }

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

    <body class="<?=($numEtape == 3 && !$achat_reussi) ? 'liste' : 'form_client'?>">
        <?php include HOME_SITE . 'header.php'; ?>

        <main>
            

<?php


// fomulaire coordonnées physiques (adresse)
if ($numEtape == 1) {
?>

        <form action="" method="post">
            <?php if ($_GET['produit'] == 'panier') {
                $redirection = 'panier';
            } else {
                $redirection = 'produit/?produit=' . $_GET['produit'];
            }
            ?>
            
            <a href="<?= HOME_SITE . $redirection?>"><img src="../image/retour.svg"></a>

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
    else if ($numEtape == 3 && !$achat_reussi) {
    ?>
        <div>
            <h2>Une erreur est survenue ! Les produits suivants ne sont plus en stock (ou n'ont pas assez de stock) : </h2>

            <ul>
                <?php foreach ($produits_plus_en_stock as $produit) { ?>
                    <li><?=$produit['nom_produit']?></li>
                <?php } ?>
            </ul>
            <a href="<?=HOME_SITE?>">Revenir à l'accueil</a>
        </div>
    <?php
    }
?>
        </main>
    </body>
</html>
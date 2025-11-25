<?php 
    // appel du fichier de configuration bdd
    define('HOME_GIT', '../../../../');
    define('HOME_SITE', '../../../');

    require_once HOME_GIT . ".config.php";

    define('ANONYMISATION_INT', 0);
    define('ANONYMISATION_STRING', 'xxxxxxxxx');

    if (!isset($_SESSION)) {
        session_start();
    }

    //verifie si quelqun est connecté
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] === false) {
        header('location: ../../../');
        exit;
    }

    $id_compte = $_SESSION['id_compte'];
    // recuperation des informations client
    $stmt = $pdo->prepare("SELECT * FROM _client WHERE id_compte = :id_compte");
    $stmt->execute([':id_compte' => $id_compte]);
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Anonymisation du compte</title>
        <?php include HOME_SITE . 'link_head.php'; ?>
    </head>
    <body class="anonymisation-client">
        <?php include HOME_SITE . "header.php"?>
        <main>
            <form action="" name="formulaireModif" method="post" enctype="multipart/form-data">
                <input type="submit" value="Confirmer la désactivation du compte">
            </form>
            <?php
                if($_SERVER["REQUEST_METHOD"] == "POST"){
                    // récupération des données du formulaire de saisie
                    // _client
                    $modifPseudo = ANONYMISATION_STRING;
                    $modifNom = ANONYMISATION_STRING;
                    $modifPrenom = ANONYMISATION_STRING;
                    $modifDateNaissance = date('0-0-0 0:0:0');
                    $modifDateDerniereModifPanier = NULL;

                    $modifAdresse = ANONYMISATION_STRING;
                    $modifCodePostal = ANONYMISATION_INT;
                    $modifCompelementAdr = ANONYMISATION_STRING;

                    // _compte
                    $modifEmail = bin2hex(random_bytes(10));
                    $modifMdp = ANONYMISATION_STRING;
                    $modifBoolSupprime = 1;
                    $modifIdImageProfil = NULL;
                    $modifDateCreation = date('0-0-0 0:0:0');

                    

                    // Mise à jour des informations dans la base de donnée
                    $stmt = $pdo->prepare("UPDATE _client SET pseudo = :modifPseudo, nom = :modifNom, prenom = :modifPrenom, date_naissance = :modifDateNaissance, date_derniere_modif_panier = :modifDateDerniereModifPanier WHERE id_compte = :id_compte");
                    $stmt->execute([':modifPseudo' => $modifPseudo, ':modifNom' => $modifNom, ':modifPrenom' => $modifPrenom, ':modifDateNaissance' => $modifDateNaissance, ':modifDateDerniereModifPanier' => $modifDateDerniereModifPanier, ':id_compte' => $id_compte]);

                    $stmt = $pdo->prepare("UPDATE _adresse AS a 
                                            JOIN _client AS c 
                                            ON c.id_adresse_fac = a.id_adresse 
                                            SET a.adresse = :adresse, 
                                                a.code_postal = :code_postal,
                                                a.complement_adresse = :complement_adresse 
                                            WHERE c.id_compte = $id_compte;");
                    $stmt->execute([':adresse' => $modifAdresse, ':code_postal' => $modifCodePostal, ':complement_adresse' => $modifCompelementAdr]);

                    $stmt = $pdo->prepare("UPDATE _compte SET email = :modifEmail, mdp = :modifMdp, supprime = :modifBoolSupprime, id_image_profil = :modifIdImageProfil, date_creation = :modifDateCreation WHERE id_compte = :id_compte");
                    $stmt->execute([':modifEmail' => $modifEmail, ':modifMdp' => $modifMdp, ':modifBoolSupprime' => $modifBoolSupprime, ':modifIdImageProfil' => $modifIdImageProfil, ':modifDateCreation' => $modifDateCreation, ':id_compte' => $id_compte]);
                    header('location:' .HOME_SITE.'deconnexion.php');
                }
            ?>
        </main>
        <footer>

        </footer>
    </body>
</html>
<?php 
    // appel du fichier de configuration bdd
    define('HOME_GIT', '../../../../../');
    define('HOME_SITE', '../../../../');

    require_once HOME_GIT . ".config.php";

    define('ANONYMISATION_INT', 0);
    define('ANONYMISATION_STRING', 'Compte désactivé');

    if (!isset($_SESSION)) {
    session_start();
    }
    //verifie si quelqun est connecté
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] === false) {
        header('location: ../../../');
        exit;
    }
    $id_compte = $_SESSION['id_compte'];
    // recuperation des informations vendeur
    $stmt = $pdo->prepare("SELECT * FROM _vendeur WHERE id_compte = :id_compte");
    $stmt->execute([':id_compte' => $id_compte]);

    // decoupage des informations en tableau
    $tabVendeur = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $tabVendeur = $tabVendeur[0];

    // definition des variables suivant les valeurs du tableau
    $raisonSociale = $tabVendeur['raison_sociale'];
    $description = $tabVendeur['description'];
    $numSiret = $tabVendeur['num_siret'];

    $id_adresse = $tabVendeur['id_adresse'];
    // recuperation des informations d'adresse du vendeur
    $stmt = $pdo->prepare("SELECT * FROM _adresse WHERE id_adresse = :id_adresse");
    $stmt->execute([':id_adresse' => $id_adresse]);
    // decoupage des informations en tableau
    $tabAdresseVendeur = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $tabAdresseVendeur = $tabAdresseVendeur[0];
    // définiton de la chaine addresse
    $chaineAdresse = $tabAdresseVendeur['adresse'] . " " . $tabAdresseVendeur['code_postal'] . " " . $tabAdresseVendeur['complement_adresse'];
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Désactivation du compte</title>
    </head>
    <body>
        <?php include "../../../header.php"?>
        <main>
            <form action="" name="formulaireModif" method="post" enctype="multipart/form-data">
                <input type="submit" value="Confirmer la désactivation du compte">
            </form>
            <?php
                if($_SERVER["REQUEST_METHOD"] == "POST"){
                    // récupération des données du formulaire de saisie
                    $modifRaisonSociale = ANONYMISATION_STRING;
                    $modifAdresse = ANONYMISATION_STRING;
                    $modifCodePostal = ANONYMISATION_INT;
                    $modifCompelementAdr = ANONYMISATION_STRING;
                    $modifNumSiret = ANONYMISATION_INT;

                    $modifDescription = ANONYMISATION_STRING;

                    

                    // Mise à jour des informations dans la base de donnée
                    $stmt = $pdo->prepare("UPDATE _vendeur SET raison_sociale = :modifRaisonSociale, num_siret = :modifNumSiret, description = :modifDescription WHERE id_compte = :id_compte");
                    $stmt->execute([':modifRaisonSociale' => $modifRaisonSociale, ':modifNumSiret' => $modifNumSiret, ':modifDescription' => $modifDescription, ':id_compte' => $id_compte]);

                    $stmt = $pdo->prepare("UPDATE _adresse AS a 
                                            JOIN _vendeur AS v 
                                            ON v.id_adresse = a.id_adresse 
                                            SET a.adresse = :adresse, 
                                                a.code_postal = :code_postal,
                                                a.complement_adresse = :complement_adresse 
                                            WHERE v.id_compte = $id_compte;");
                    $stmt->execute([':adresse' => $modifAdresse, ':code_postal' => $modifCodePostal, ':complement_adresse' => $modifCompelementAdr]);
                }
            ?>
        </main>
        <footer>

        </footer>
    </body>
</html>
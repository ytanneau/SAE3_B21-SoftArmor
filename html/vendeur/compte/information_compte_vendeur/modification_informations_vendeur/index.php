<?php 
    // appel du fichier de configuration bdd
    require_once "../../../../../.config.php";

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
        <title>Modifier mes informations</title>
        <link rel="stylesheet" href="">
        <style>
            p{
                display:flex;
                flex-direction:column;
            }
            p>input{
                width: 300px;
            }
            textarea{
                height : 500px;
                width : 500px;
            }
        </style>
    </head>
    <body>
        <header>

        </header>
        <main>
            <h1>Modifier mes informations</h1>
            <em>Pour des raisons de securité, le numero de siret ne peut etre modifé</em>
            <form action="" name="formulaireModif" method="post" enctype="multipart/form-data">
                <p>
                    <label for="raison_sociale">Raison sociale</label>
                    <input type="text" name="raison_sociale" id="id_raison_sociale" value="<?php echo $raisonSociale ?>">

                    <label for="adresse">Adresse</label>
                    <input type="text" name="adresse" id="id_adresse" value="<?php echo $tabAdresseVendeur['adresse'] ?>">
                    <label for="code_postal">Code postal</label>
                    <input type="text" name="code_postal" id="id_code_postal" value="<?php echo $tabAdresseVendeur['code_postal'] ?>">
                    <label for="complementAdr">Complement d'adresse</label>
                    <input type="text" name="complementAdr" id="id_complementAdr" value="<?php echo $tabAdresseVendeur['complement_adresse'] ?>">

                    <label for="description">Description</label>
                    <textarea type="textarea" name="description" id="id_description" value="<?php if($description != null) echo $description ?>"></textarea>
                </p>
                <input type="submit" value="Valider la modification">
            </form>
            <?php
                if($_SERVER["REQUEST_METHOD"] == "POST"){
                    // récupération des données du formulaire de saisie
                    $modifRaisonSociale = $_POST['raison_sociale'];
                    $modifAdresse = $_POST['adresse'];
                    $modifCodePostal = $_POST['code_postal'];
                    $modifCompelementAdr = $_POST['complementAdr'];

                    $modifDescription = $_POST['description'];

                    

                    // Mise à jour des informations dans la base de donnée
                    $stmt = $pdo->prepare("UPDATE _vendeur SET raison_sociale = :modifRaisonSociale, description = :modifDescription WHERE id_compte = :id_compte");
                    $stmt->execute([':modifRaisonSociale' => $modifRaisonSociale, ':modifDescription' => $modifDescription, ':id_compte' => $id_compte]);

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
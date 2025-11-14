<?php
    // importer le fichier de connexion à la bdd
    require_once "../../../../.config.php";
    
    define('HOME_GIT', '../../../../');
    define('HOME_SITE', '../../../');

    if (!isset($_SESSION)) {
    session_start();
    }
    //verifie si quelqun est connecté
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] === false) {
        header('location: ../../');
        exit;
    }
    
    $id_compte = $_SESSION['id_compte'];
    // requete pour recuperer les informations du vendeur
    $stmt = $pdo->prepare("SELECT * FROM _vendeur WHERE id_compte = :id_compte");
    $stmt->execute([':id_compte' => $id_compte]);

    // decoupage des informations en tableau
    $tabVendeur = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $tabVendeur = $tabVendeur[0];

    // assignation des variables aux élements du tableau
    $raisonSociale = $tabVendeur['raison_sociale'];
    $numSiret = $tabVendeur['num_siret'];
    $id_adresse = $tabVendeur['id_adresse'];
    $description = $tabVendeur['description'];

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
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Consulter mes informations</title>
    </head>
    <body>
        <?php $images = HOME_SITE . "image/" ?>
        <header id="header_client">
            <div>
                <a href=<?= HOME_SITE ?>>
                    <img src="<?= $images . 'Alizon_blanc.png' ?>" alt="Logo Alizon" title="Logo Alizon">
                </a>

                <ul>
                    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) { ?>
                        <li>
                            <div class="dropdown">
                                <button onclick="ouvrirMenu()" class="dropdown-button">
                                    <img src="<?= $images . 'compte_blanc.svg' ?>" class="icon">
                                    <?= $_SESSION['pseudo'] ?>
                                </button>

                                <div id="dropdown-compte" class="dropdown-content">
                                    <a href="<?= HOME_SITE . 'compte/informations' ?>"> <img src="<?= $images . 'compte.svg' ?>" class="icon">Mon profil</a> 
                                    <a href="#"> <img src="<?= $images . 'options.svg' ?>" class="icon">Paramètres</a>
                                    <a href="<?= HOME_SITE . 'deconnexion' ?>"> <img src="<?= $images . 'deconnexion.svg' ?>" class="icon">Déconnexion</a>
                                </div>
                            </div>
                        </li>
                    <?php } else { ?>
                        <li> <a href="<?= HOME_SITE . 'compte/inscription' ?>"> <img src="<?= $images . 'compte_blanc.svg' ?>" class="icon">S'inscrire</a> </li>
                        <li> <a href="<?= HOME_SITE . 'compte/connexion' ?>"> <img src="<?= $images . 'connexion_blanc.svg' ?>" class="icon">Se connecter</a> </li>
                    <?php } ?>
                </ul>
            </div>
        </header>

        <script>
            function ouvrirMenu() {
                document.getElementById("dropdown-compte").classList.toggle("show");
            }

            // Ferme le menu si on clique ailleurs
            window.onclick = function(event) {
                if (!event.target.matches('.dropdown-button')) {
                    var dropdowns = document.getElementsByClassName("dropdown-content");
                    var i;

                    for (i = 0; i < dropdowns.length; i++) {
                        var openDropdown = dropdowns[i];
                        if (openDropdown.classList.contains('show')) {
                            openDropdown.classList.remove('show');
                        }
                    }
                }
            }
        </script>
        <main>
            <!-- Zone d'affichage des informations du vendeur -->
            <h1>Mes informations</h1>
            <h3>Raison sociale</h3>
            <p><?php echo $raisonSociale ?></p>
            <h3>Numero de siret</h3>
            <p><?php echo $numSiret ?></p>
            <h3>Adresse</h3>
            <p><?php echo $chaineAdresse ?></p>
            <h3>Description</h3>
            <p>
                <?php
                    if($description === null){
                        echo "Pas de description";
                    } else {
                        echo $description;
                    } 
                ?>
            </p>
            <!-- bouton pour etre rediriger vers la modification des informations du vendeur -->
            <button><a href="modification_informations_vendeur/index.php">Modifier mes informations</a></button>
        </main>
        <footer>

        </footer>
    </body>
</html>
<?php
    // Permet d'utiliser le fichier .config.php
    define("HOME_GIT", "../../../../");
    define("HOME_SITE", "../../../");

    require_once HOME_GIT . 'fonction_produit.php';
    require_once HOME_GIT . '.config.php';

    if (!isset($_SESSION)) {
        session_start();
    }

    // Si je suis connecté mais pas en tant que vendeur, retour à l'accueil client
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && !isset($_SESSION['raison_sociale'])) {
        header('location: ' . HOME_SITE);
        exit;

    // Sinon si je ne suis pas connecté, retour à la page connexion vendeur
    } else if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] === false) {
        header('location: ../');
        exit;
    }

    if ($_GET == NULL || !isset($_GET['produit'])) {
       echo "Produit non trouvé";
       renvoi();
    }

    $_GET['produit'] = htmlentities(trim($_GET['produit'] ?? ''));
    $id_produit = $_GET['produit'];

    if($_SERVER["REQUEST_METHOD"] == "POST"){

        if (isset($_FILES['photoPromotion']) && $_FILES['photoPromotion']['error'] === UPLOAD_ERR_OK){
            $nomImageTemp = $_FILES['photoPromotion'];
            // recupere le nom temporaire du fichier pour le deplacer
            $cheminTemp = $_FILES['photoPromotion']['tmp_name'];
            
            switch($_FILES['photoPromotion']['type']){
                case 'image/jpeg' : 
                    $extension = '.jpeg';
                    break;
                case 'image/webp' :
                    $extension = '.webp';
                    break;
                case 'image/jpg' :
                    $extension = '.jpg';
                    break;
                default :
                    $extension = '.png';
                    break;
            }
            $nomImage = $id_produit . "_promotion" . $extension;
            
            $cheminFinal = HOME_SITE . "ressources/promotion/" . $nomImage;
            // definition des caractéristiques d'une image
            $url = "ressources/promotion/" . $nomImage;
            $altDefault = "Image de promotion";
            if(move_uploaded_file($cheminTemp,$cheminFinal)){
                $id_image_principal = add_image($url,$nomImage, $altDefault);
            }
        } else {
            $id_image_principal = null;
        }
        creer_promotion($id_produit, $_POST['dateDebut'],$_POST['dateFin'],$id_image_principal);
        header("Location: ../?produit=" . $id_produit);
        exit();
    }

?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>Alizon - Démarrer une promotion</title>
        <?php include HOME_SITE . 'link_head.php'; ?>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="<?=HOME_SITE?>style.css">
    </head>
    <body>
        <?php include "../../header.php" ?>
        <main class="main_promo">
            <div class="entete">
                <a href="../index.php?produit=<?=$id_produit?>"><img src="../../../../image/retour.svg" alt="bouton retour en arrière"></a>
                <h1>Démarrer une promotion</h1>
            </div>
            <p style="color:red;">Une promotion à un coût journalier de 26€ par jour</p>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="en_ligne">
                    <div class="en_colonne">
                        <label for="dateDebut">Début de la promotion: </label>
                        <input type="date" id="dateDebut" name="dateDebut" required>
                    </div>
                    <div class="en_colonne">
                        <label for="dateFin">Fin de la promotion: </label>
                        <input type="date" id="dateFin" name="dateFin" required>
                    </div>
                </div>
                
                <p style="display:none;" class="warning" id="warning_date_anterieur">Date de fin antérieur à la date de debut</p>
                <p style="display:none;" class="warning" id="warning_date_passe">Date de debut déjà passé</p>
                
                <div class="en_colonne">
                    <label for="cout">Coût final : </label>
                    <input type="text" id="cout" readonly>
                </div>

                <div class="block_banniere">
                    <label for="photoPromotion">Ajouter une banniere (optionnel)</label>
                    <input type="file" id="photoPromotion" name="photoPromotion" accept="image/png, image/webp, image/jpeg, image/jpg">
                </div>
                <input type="submit" id="valider" value="Valider">
            </form>
        </main>
        <?php include HOME_SITE . "footer.php" ?>    
    </body>
    <script>
        // PROMOTION //
        const PRIX = 26;
        const cout = document.getElementById("cout");
        const dateDebut = document.getElementById("dateDebut");
        const dateFin = document.getElementById("dateFin");
        const valider = document.getElementById("valider");
        const warning_date_anterieur = document.getElementById("warning_date_anterieur");
        const warning_date_passe = document.getElementById("warning_date_passe");
        const dateCourante = new Date();
        dateCourante.setHours(0, 0, 0, 0);

        dateDebut.addEventListener('change', () => {
            if(dateFin.value != ""){
                if(dateDebut.value > dateFin.value) {
                    warning_date_anterieur.style.display = "block";
                } else if(new Date(dateDebut.value).getTime() < dateCourante.getTime()){
                    warning_date_passe.style.display = "block";
                } else {
                    warning_date_passe.style.display = "none";
                    warning_date_passe.style.display = "none";
                    calculP();
                }
            }
        });


        dateFin.addEventListener('change', () => {
            if(dateDebut.value != ""){
                if(dateDebut.value > dateFin.value) {
                    warning_date_anterieur.style.display = "block";
                } else {
                    warning_date_anterieur.style.display = "none";
                    calculP();
                }
            }
        });

        function calculP() {
            const d1 = new Date(dateDebut.value + "T00:00:00");
            const d2 = new Date(dateFin.value + "T00:00:00");

            const diffJours = (d2 - d1) / 86400000;

            if (diffJours < 0) {
                cout.value = "";
                return;
            }

            cout.value = PRIX * diffJours + PRIX + "€";
        }
        
        // VALIDATION DU FORM //
        valider.addEventListener('click', (event) => {
            warning_date_anterieur.style.display = "none";
            warning_date_passe.style.display = "none";

            if (dateDebut.value > dateFin.value) {
                warning_date_anterieur.style.display = "block";
                event.preventDefault();
            }
            
            if(new Date(dateDebut.value).getTime() < dateCourante.getTime()){
                warning_date_passe.style.display = "block";
                event.preventDefault();
            }
        });
        
        let date = new Date()
        let current_string_date

        if(date.getMonth() < 9){
            current_string_date= date.getFullYear() + "-0" + (date.getMonth()+1) + "-" + date.getDate()
        } else {
            current_string_date = date.getFullYear() + "-" + date.getMonth() + "-" + date.getDate()
        }
        dateDebut.value = current_string_date
        dateFin.value = current_string_date
        cout.value = PRIX + "€";
    </script>
</html>
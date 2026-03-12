<?php
    define("HOME_GIT", "../../../../../");
    define("HOME_SITE", "../../../../");
    
    require_once HOME_GIT . '.config.php';
    require_once HOME_GIT . 'fonction_produit.php';

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

    if ($_GET == NULL || !isset($_GET['produit']) || !isset($_GET['idReduc'])) {
       echo "Produit non trouvé";
       renvoi();
    }

    $id_produit = $_GET['produit'];
    $id_reduction = $_GET['idReduc'];
    $prix = get_prix_produit($id_produit);
    $tab_reduction = get_reduction($id_reduction);

    if($tab_reduction == null){
        renvoi();
    };

    if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_POST)){
        update_reduction($id_reduction, $_POST['dateDebut'], $_POST['dateFin'], $_POST['pourcentage']);
        header('location: ../../?produit='.$id_produit);
    }
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>Alizon - Démarrer une réduction</title>
        <?php include HOME_SITE . 'link_head.php'; ?>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="<?=HOME_SITE?>style.css">
    </head>
    <body><?php include "../../../header.php" ?>
        <main class="main_reduction">
            <div class="entete">
                <a href="../../index.php?produit=<?=$id_produit?>"><img src="../../../../../image/retour.svg" alt="bouton retour en arrière"></a>
                <h1>Démarrer une réduction</h1>
            </div>
            <p>Prix actuel : <?=htmlentities($prix)?>€</p>
            <form action="" method="post">
                <div>
                    <label for="dateDebut">Debut de la réduction</label>
                    <input type="date" id="dateDebut" name="dateDebut" required>
                </div>
                <div>
                    <label for="dateFin">Fin de la réduction</label>
                    <input type="date" id="dateFin" name="dateFin" required>
                </div>
                <div>
                    <label for="pourcentage">Pourcentage : </label>
                    <input type="text" id="pourcentage" name="pourcentage" required value="<?=htmlentities($tab_reduction['pourcentage'])?>">
                    <p style="display:none;" id="warning">Le pourcentage ne peut <br>être supérieur à 100</p>
                </div>
                <div>
                    <label for="euro">Remise appliquée : </label>
                    <input type="text" id="euro" readonly>
                </div>
                <div>
                    <label for="prixFinal">Prix final : </label>
                    <input type="text" id="prixFinal" readonly>
                </div>
                <input type="submit" id="btn_confirm_reduc">
            </form>
        </main>
        <?php include HOME_SITE . "footer.php"?>
    </body>
    <script>
        let tab_reduction = <?= json_encode($tab_reduction)?>;

        const dateDebut = document.getElementById("dateDebut")
        const dateFin = document.getElementById("dateFin")
        const warning = document.getElementById("warning")
        const pourcentage = document.getElementById("pourcentage")
        const euro = document.getElementById("euro")
        const prixInitial = <?= json_encode($prix) ?>
        const prixFinal = document.getElementById("prixFinal")
    
        

        dateDebut.value = tab_reduction['dateDebut']
        dateFin.value = tab_reduction['dateFin']
        warning.style.display = "none";
    </script>
</html>

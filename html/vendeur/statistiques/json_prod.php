<?php
    session_start();

    // Si je suis connecté mais pas en tant que vendeur, retour à l'accueil client
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && !isset($_SESSION['raison_sociale'])) {
        header('location: ' . HOME_SITE);
        exit;
    }
    // Sinon si je ne suis pas connecté, retour à la page connexion vendeur
    else if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] === false) {
        header('location: ../');
        exit;
    }

    if($_SESSION['id_compte']!=$_GET['id_compte']){
        header('location: ../');
    }

    define("HOME_GIT", "../../../");
    define("HOME_SITE", "../../");
    require_once HOME_GIT .".config.php";

    //recup les info pour 1 produit
    if(isset($_GET['produit'])){
        $sql= "SELECT 
                id_produit,
                quantite,
                prix,
                date_commande,
                categorie,
                nom_stock,
                id_vendeur FROM stats_par_produit WHERE id_produit = :id_produit AND id_vendeur = :id_vendeur";
        $requete = $pdo->prepare($sql);
        $requete->bindValue(":id_produit", trim($_GET['produit']), PDO::PARAM_INT);
        $requete->bindValue(":id_vendeur", trim($_GET['id_compte']), PDO::PARAM_INT);
        $requete->execute();
        $data= $requete->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($data);
    }
    elseif (isset($_GET['categorie'])) {
        $sql = "SELECT nom_categorie FROM _categorie WHERE nom_categorie_sup IS NULL";
        $requete = $pdo->prepare($sql);
        $requete->execute();
        $data= $requete->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($data);
    }
    elseif (isset($_GET['toutecategorie'])) {
        $sql = "SELECT nom_categorie,nom_categorie_sup FROM _categorie";
        $requete = $pdo->prepare($sql);
        $requete->execute();
        $data= $requete->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($data);
    }
    elseif (isset($_GET['listeprod'])) {
        $sql= "SELECT DISTINCT 
                id_produit,
                nom_stock FROM stats_par_produit WHERE id_vendeur = :id_vendeur";
        $requete = $pdo->prepare($sql);
        $requete->bindValue(":id_vendeur", trim($_GET['id_compte']), PDO::PARAM_INT);
        $requete->execute();
        $data= $requete->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($data);
    }
    else{
        $sql= "SELECT 
                id_produit,
                quantite,
                prix,
                date_commande,
                categorie,
                nom_stock,
                id_vendeur FROM stats_par_produit WHERE id_vendeur = :id_vendeur";
        $requete = $pdo->prepare($sql);
        $requete->bindValue(":id_vendeur", trim($_GET['id_compte']), PDO::PARAM_INT);
        $requete->execute();
        $data= $requete->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($data);
    }
?>
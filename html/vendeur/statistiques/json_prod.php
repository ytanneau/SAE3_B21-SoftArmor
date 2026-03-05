<?php


    define("HOME_GIT", "../../../");
    define("HOME_SITE", "../../");
    require_once HOME_GIT .".config.php";

    //recup les info pour 1 produit
    if(isset($_GET['produit'])){
        $sql= "SELECT * FROM stats_par_produit WHERE id_produit = :id_produit AND id_vendeur = :id_vendeur";
        $requete = $pdo->prepare($sql);
        $requete->bindValue(":id_produit", trim($_GET['produit']), PDO::PARAM_INT);
        $requete->bindValue(":id_vendeur", trim($_GET['id_compte']), PDO::PARAM_INT);
        $requete->execute();
        $data= $requete->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($data);
    }
    else{
        $sql= "SELECT * FROM stats_par_produit WHERE id_vendeur = :id_vendeur";
        $requete = $pdo->prepare($sql);
        $requete->bindValue(":id_vendeur", trim($_GET['id_compte']), PDO::PARAM_INT);
        $requete->execute();
        $data= $requete->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($data);
    }
?>
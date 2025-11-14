<?php  
    
    //fonction pour calculer et afficher les moyennes d'un produit
    function afficher_moyenne_note($moyenne){
        if($moyenne > 5 || $moyenne < 0){
            return null;
        }
        // code de iwan pour calculer et afficher les moyennes d'un produit en fonction de sa moyenne
        for ($i =1; $i <= floor($moyenne); $i++){
            ?> <img src="../../images/etoile_pleine.svg" alt="étoile pleine"><?php
        }
        if(fmod(floor($moyenne*2),2)){
            ?> <img src="../../images/etoile_demi.svg" alt="étoile à moitié pleine"> <?php 
        }
        for ($i =5; $i > round($moyenne); $i--){
            ?> <img src="../../images/etoile_vide.svg" alt="étoile vide"><?php
        }
    }

    function detail_produit($id_produit){
        global $pdo;
        try {
            $requete = $pdo->prepare("SELECT * from _produit where id_produit = :id_produit");
            $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_STR);
            $requete->execute();
            return $requete->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    function vendeur_image_produit($id_produit){
        global $pdo;
        try {
            $requete = $pdo->prepare('SELECT * FROM `_images_produit` WHERE  id_produit = :id_produit');
            $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_STR);
            $requete->execute();
            return $requete->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    function vendeur_verif_produit($id_produit, $id_vendeur){
        global $pdo;
        try {
            $requete = $pdo->prepare('SELECT id_produit from `_produit` where id_vendeur = :id_vendeur AND id_produit = :id_produit');
            $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_STR);
            $requete->bindValue(':id_vendeur', $id_vendeur, PDO::PARAM_STR);
            $requete->execute();
            return $requete->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
        if ($requete == NULL) {
            echo "ce produit n'existe pas";
            header("../html/vendeur/stock/");
        }
    }
?>


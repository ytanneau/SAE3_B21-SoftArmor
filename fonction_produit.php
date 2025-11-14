<?php
    // Fonction pour calculer et afficher les moyennes d'un produit
    function afficher_moyenne_note($moyenne){
        if($moyenne > 5 || $moyenne < 0){
            return null;
        }
        // code de iwan pour calculer et afficher les moyennes d'un produit en fonction de sa moyenne
        for ($i =1; $i <= floor($moyenne); $i++){
            ?> <img src="../../image/etoile_pleine.svg" alt="étoile pleine"><?php
        }
        if(fmod(floor($moyenne*2),2)){
            ?> <img src="../../image/etoile_demi.svg" alt="étoile à moitié pleine"> <?php 
        }
        for ($i =5; $i > round($moyenne); $i--){
            ?> <img src="../../image/etoile_vide.svg" alt="étoile vide"><?php
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

    function detail_produit_image($id_produit) {
        global $pdo;

        $sql = "SELECT p.*,
        v.raison_sociale AS id_vendeur,
        ip.id_image_principale,
        i_principale.url_image AS image_principale_url,
        i_principale.titre AS image_principale_titre,
        i_principale.alt AS image_principale_alt,
        i1.url_image AS image_1_url,
        i1.titre AS image_1_titre,
        i1.alt AS image_1_alt,
        i2.url_image AS image_2_url,
        i2.titre AS image_2_titre,
        i2.alt AS image_2_alt
        FROM produit p
        JOIN compte_vendeur v ON p.id_vendeur = v.id_compte
        LEFT JOIN _images_produit ip ON p.id_produit = ip.id_produit
        LEFT JOIN _image i_principale ON ip.id_image_principale = i_principale.id_image
        LEFT JOIN _image i1 ON ip.id_image1 = i1.id_image
        LEFT JOIN _image i2 ON ip.id_image2 = i2.id_image
        WHERE p.id_produit = :id_produit";

        try {
            $requete = $pdo->prepare($sql);
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


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

        $sql = "SELECT * FROM produit_image WHERE id_produit = :id_produit";

        $requete = $pdo->prepare($sql);
        $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_STR);
        $requete->execute();

        return $requete->fetch(PDO::FETCH_ASSOC);
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

    function url_image_produit($id_image){
        global $pdo;
        try {
            $requete = $pdo->prepare('SELECT * FROM _image WHERE  id_image = :id_image');
            $requete->bindValue(':id_image', $id_image, PDO::PARAM_STR);
            $requete->execute();
            return $requete->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    function vendeur_verif_produit($id_produit, $id_vendeur){
        global $pdo;
        try {
            $requete = $pdo->prepare('SELECT id_produit from _produit where id_vendeur = :id_vendeur AND id_produit = :id_produit');
            $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_STR);
            $requete->bindValue(':id_vendeur', $id_vendeur, PDO::PARAM_STR);
            $requete->execute();
            print_r($requete);
            return $requete->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
        if ($requete['id_produit'] == NULL) {
            echo "ce produit n'existe pas";
            return NULL;
        }
    }

    function vendeur_All_produit($id_vendeur){
        global $pdo;
        try {
            $requete = $pdo->prepare('select id_produit, nom_stock, quantite from _produit where id_vendeur = :id_vendeur');
            $requete->bindValue(':id_vendeur', $id_vendeur, PDO::PARAM_STR);
            $requete->execute();
            return $requete->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    // Nom public, prix, moyenne des notes et informations de l'image de chaque produit
    function info_produit_accueil(){
        global $pdo;
        try {
            $requete = $pdo->prepare('SELECT produit_visible.id_produit,nom_public,prix,url_image,alt,_image.titre,note_moy AS moyenne FROM produit_visible INNER JOIN _images_produit ON produit_visible.id_produit = _images_produit.id_produit INNER JOIN _image ON _images_produit.id_image_principale = _image.id_image INNER JOIN produit_note ON produit_note.id_produit = produit_visible.id_produit WHERE produit_note.id_produit = produit_visible.id_produit;');
            $requete->execute();
            return $requete->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    // Nom public, prix, moyenne des notes et informations de l'image des produits alimentaires
    function info_produit_accueil_categorie($categorie){
        global $pdo;
        try {
            $requete = $pdo->prepare('SELECT produit_visible.id_produit,nom_public,prix,url_image,alt,_image.titre,note_moy AS moyenne FROM produit_visible INNER JOIN _images_produit ON produit_visible.id_produit = _images_produit.id_produit INNER JOIN _image ON _images_produit.id_image_principale = _image.id_image INNER JOIN produit_note ON produit_note.id_produit = produit_visible.id_produit INNER JOIN _produit_dans_categorie ON produit_visible.id_produit = _produit_dans_categorie.id_produit WHERE produit_note.id_produit = produit_visible.id_produit AND _produit_dans_categorie.nom_categorie = :categorie;');
            $requete->bindValue(':categorie', $categorie, PDO::PARAM_STR);
            $requete->execute();
            return $requete->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    // Nom public, prix, moyenne des notes et informations de l'image des produits les plus récents
    function info_produit_accueil_plus_recent(){
        global $pdo;
        try {
            $requete = $pdo->prepare('SELECT produit_visible.id_produit,nom_public,prix,url_image,alt,_image.titre,note_moy AS moyenne FROM produit_visible INNER JOIN _images_produit ON produit_visible.id_produit = _images_produit.id_produit INNER JOIN _image ON _images_produit.id_image_principale = _image.id_image INNER JOIN produit_note ON produit_note.id_produit = produit_visible.id_produit INNER JOIN _produit_dans_categorie ON produit_visible.id_produit = _produit_dans_categorie.id_produit WHERE produit_note.id_produit = produit_visible.id_produit ORDER BY date_creation DESC;');
            $requete->execute();
            return $requete->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    // Nom public, prix, moyenne des notes et informations de l'image des produits en réduction
    function info_produit_accueil_reduction(){
        global $pdo;
        try {
            $requete = $pdo->prepare('SELECT produit_visible.id_produit,nom_public,prix,url_image,alt,_image.titre,note_moy AS moyenne,TRUNCATE((prix - prix*reduction*0.01),2) AS prix_reduit FROM produit_visible INNER JOIN _images_produit ON produit_visible.id_produit = _images_produit.id_produit INNER JOIN _image ON _images_produit.id_image_principale = _image.id_image INNER JOIN produit_note ON produit_note.id_produit = produit_visible.id_produit INNER JOIN _produit_dans_categorie ON produit_visible.id_produit = _produit_dans_categorie.id_produit INNER JOIN _promotion ON produit_visible.id_produit = _promotion.id_produit WHERE produit_note.id_produit = produit_visible.id_produit;');
            $requete->execute();
            return $requete->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    function supprimer_produit_panier($id_produit,$id_compte){
        global $pdo;
        try {
            $requete = $pdo->prepare('DELETE FROM _elt_panier WHERE id_produit =:id_produit and id_client = :id_client;');
            $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_STR);
            $requete->bindValue(':id_client', $id_compte, PDO::PARAM_STR);
            $requete->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }

    function supprimer_produit_stock($id_produit) {
        global $pdo;
        try {
            $requete = $pdo->prepare('DELETE FROM _produit WHERE id_produit = :id_produit');
            $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_INT);
            $requete->execute();
            return $requete->rowCount() > 0;
        } catch (PDOException $e) {
            throw $e;
        }
    }
?>


<?php
    require_once HOME_GIT . '.config.php';

    // retourne les avis des client pour un produit donnée
    function avis_client_produit($id_produit){
        global $pdo;
        try {
            $requete = $pdo->prepare("SELECT * FROM avis_client WHERE id_produit=id_produit");
            $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_STR);
            $requete->execute();
            return $requete->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    //requete pour recuperer les avis du compte
    function tout_avis_client($id_client){
        global $pdo;
        try {
            $requete = $pdo->prepare("SELECT pseudo,date_avis,note,titre,commentaire,url_image,titre_image,alt_image FROM compte_client INNER JOIN _avis ON compte_client.id_compte = _avis.id_client LEFT JOIN compte_image_profil ON compte_client.id_compte = compte_image_profil.id_compte WHERE compte_client.id_compte = :id_client");
            $requete->bindValue(':id_client', $id_client, PDO::PARAM_STR);
            $requete->execute();
            return $requete->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    // requete pour crée un avis
    function cree_avis($id_produit, $note, $titre , $description, $image){
        global $pdo;
        try {
            $requete = $pdo->prepare("");
            $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_STR);
            $requete->bindValue(':note', $note, PDO::PARAM_STR);
            $requete->bindValue(':titre', $titre, PDO::PARAM_STR);
            $requete->bindValue(':description', $description, PDO::PARAM_STR);
            $requete->execute();
            return $requete->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    // verifie si un avis existe déjà
    function check_avis_existe($id_produit, $id_client){
        global $pdo;
        try {
            $requete = $pdo->prepare("SELECT * FROM avis_client WHERE id_produit=id_produit AND id_client=:id_client");
            $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_STR);
            $requete->bindValue(':id_client', $id_client, PDO::PARAM_STR);
            $requete->execute();
            return $requete->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }


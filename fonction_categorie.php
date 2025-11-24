<?php
    function get_sous_categorie($categorie){
        /**
         * Fonction get_sous_categorie() prend en parametre une categorie
         * Renvoie un tableau avec les sous categories de la categorie donnée en parametre
         */
        global $pdo;
        try{
            $stmt = $pdo->prepare("SELECT * FROM _categorie WHERE nom_categorie_sup = :categorie");
            $stmt->execute([':categorie' => $categorie]);
            $tabCategorie = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $tabCategorie;
        } catch(PDOException $e){
            throw $e;
        }
    }

    function get_categorie_parent(){
        /**
         * Fonction get_categorie_parent() ne prend pas de paremetre
         * Renvoie un tableau avec toutes le nom des categories n'ayant aucun parent
         */
        global $pdo;
        try{
            $stmt = $pdo->prepare("SELECT nom_categorie FROM _categorie WHERE nom_categorie_sup IS NULL");
            $stmt->execute();
            $tabParent = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $tabParent;
        } catch(PDOException $e){
            throw $e;
        }
    }

    function get_categorie(){
        /**
         * Fonction get_categorie() ne prend pas de parametre
         * Renvoie un tableau avec toutes les categories
         */
        global $pdo;
        try{
            $stmt = $pdo->prepare("SELECT nom_categorie FROM _categorie");
            $stmt->execute();
            $tabCategorie = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $tabCategorie;
        } catch(PDOException $e){
            throw $e;
        }
    }
?>
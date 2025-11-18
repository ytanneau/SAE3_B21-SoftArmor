<?php
    function get_sousCategorie($categorie){
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
?>
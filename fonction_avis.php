<?php
    require_once HOME_GIT . '.config.php';

    // retourne les avis des client pour un produit donnée
    function avis_client_produit($id_produit){
        global $pdo;
        try {
            $requete = $pdo->prepare("CALL avis_client_produit(:id_produit)");
            $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_STR);
            $requete->execute();
            return $requete->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }
<?php

    function get_produit_commande($id_vendeur){
        global $pdo;

        try{
            $stmt = $pdo->prepare("SELECT * FROM _elt_commande c INNER JOIN _produit p ON c.id_produit = p.id_produit WHERE id_vendeur = :id_vendeur");
            $stmt->execute([
                ':id_vendeur' => $id_vendeur
            ]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC); 
        } catch (PDOException $e){
            throw $e;
        }
    }


?>
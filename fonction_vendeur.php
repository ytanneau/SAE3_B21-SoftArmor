<?php 
    function set_informations_vendeur($raisonSociale, $description, $id_compte){
        global $pdo;

        try{
            $stmt = $pdo->prepare("UPDATE _vendeur SET raison_sociale = :raisonSociale, description = :desc$description WHERE id_compte = :id_compte");
            $stmt->execute([':raisonSociale' => $raisonSociale, ':desc$description' => $description, ':id_compte' => $id_compte]);
        } catch(PDOException $e) {
            throw $e;
        }
    }

    function set_adresse_vendeur($id_compte, $modifAdresse, $modifCodePostal, $modifCompelementAdr){
        global $pdo;
        try{
            $stmt = $pdo->prepare("UPDATE _adresse AS a JOIN _vendeur AS v 
                                ON v.id_adresse = a.id_adresse 
                                SET a.adresse = :adresse, 
                                    a.code_postal = :code_postal,
                                    a.complement_adresse = :complement_adresse 
                                WHERE v.id_compte = $id_compte;");
            $stmt->execute([':adresse' => $modifAdresse, ':code_postal' => $modifCodePostal, ':complement_adresse' => $modifCompelementAdr]);
        } catch(PDOException $e) {
            throw $e;
        }
    }

     
    function get_informations_vendeur($id_compte){
        global $pdo;

        try{
            $stmt = $pdo->prepare("SELECT * FROM _vendeur WHERE id_compte = :id_compte");
            $stmt->execute([':id_compte' => $id_compte]);
            $tabVendeur = $stmt->fetch(PDO::FETCH_ASSOC);

            return $tabVendeur;
        } catch(PDOException $e) {
            throw $e;
        }
    }

    function get_adresse_vendeur($id_adresse){
        global $pdo;

        try{
            $stmt = $pdo->prepare("SELECT * FROM _adresse WHERE id_adresse = :id_adresse");
            $stmt->execute([':id_adresse' => $id_adresse]);
            $tabAdresseVendeur = $stmt->fetch(PDO::FETCH_ASSOC);

            return $tabAdresseVendeur;
        } catch(PDOException $e) {
            throw $e;
        }   
    }
?>
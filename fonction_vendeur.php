<?php 
    function update_informations_vendeur($raisonSociale, $description, $id_compte){
        /**
         * Fonction update_informations_vendeur() prend en parametre les informations d'un vendeur
         * Met a jour les infromations d'un vendeur dans la base de données
         */
        global $pdo;

        try{
            $stmt = $pdo->prepare("UPDATE _vendeur SET raison_sociale = :raisonSociale, description = :description WHERE id_compte = :id_compte");
            $stmt->execute([':raisonSociale' => $raisonSociale, ':description' => $description, ':id_compte' => $id_compte]);
        } catch(PDOException $e) {
            throw $e;
        }
    }

    function update_adresse_vendeur($id_compte, $modifVille, $modifAdresse, $modifCodePostal, $modifCompelementAdr){
        /**
         * Fonction update_adresse_vendeur() prend en parametre les informations d'une adresse et l'id d'un vendeur
         * Met a jour les informations de l'adresse dans la base de données 
         */
        global $pdo;
        try{
            $stmt = $pdo->prepare("UPDATE _adresse AS a JOIN _vendeur AS v 
                                ON v.id_adresse = a.id_adresse 
                                SET a.ville = :ville,
                                    a.adresse = :adresse, 
                                    a.code_postal = :code_postal,
                                    a.complement_adresse = :complement_adresse 
                                WHERE v.id_compte = $id_compte;");
            $stmt->execute([':ville' => $modifVille, ':adresse' => $modifAdresse, ':code_postal' => $modifCodePostal, ':complement_adresse' => $modifCompelementAdr]);
        } catch(PDOException $e) {
            throw $e;
        }
    }

     
    function get_informations_vendeur($id_compte){
        /**
         * Fonction get_informations_vendeur() prend en parametre l'id d'un compte vendeur
         * Renvoie un tableau avec toutes les informations d'un vendeur
         */
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
        /**
         * Fonction get_adresse_vendeur() prend en paremetre l'id d'une adresse
         * Renvoie un tableau avec toutes les informations d'une adresse
         */
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

    function get_coor_id_vendeur(){
        global $pdo;

        try{
            $stmt = $pdo->prepare("SELECT id_compte,id_adresse,coor_x,coor_y,raison_sociale FROM _adresse a JOIN _vendeur v ON a.id_adresse = v.id_adresse");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e){
            throw $e;
        }
    }

    function get_adresse(){
        global $pdo;

        try{
            $stmt = $pdo->prepare("SELECT * FROM _adresse");
            $stmt->execute();
            return $stmt->fetchall(PDO::FETCH_ASSOC);
        } catch(PDOException $e){
            throw $e;
        }
    }
?>
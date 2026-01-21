<?php
    // Fonction pour calculer et afficher les moyennes d'un produit
    function afficher_moyenne_note($moyenne){
        if(is_null($moyenne)){
            return null;
        }
        if($moyenne > 5 || $moyenne < 0){
            return null;
        }
        // code de iwan pour calculer et afficher les moyennes d'un produit en fonction de sa moyenne
        for ($i =1; $i <= floor($moyenne); $i++){
            ?> <img src="/image/etoile_pleine.svg" alt="étoile pleine" title="étoile pleine" class="etoile"><?php
        }
        if(fmod(floor($moyenne*2),2)){
            ?> <img src="/image/etoile_demi.svg" alt="étoile à moitié pleine"  title="étoile à moitié pleine" class="etoile"> <?php 
        }
        for ($i =5; $i > round($moyenne); $i--){
            ?> <img src="/image/etoile_vide.svg" alt="étoile vide"  title="étoile vide" class="etoile"><?php
        }
    }

    function detail_produit($id_produit){
        global $pdo;
        
        $requete = $pdo->prepare("SELECT * from produit where id_produit = :id_produit");
        $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_INT);
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }

    function note_produit($id_produit) {
        global $pdo;
        
        $requete = $pdo->prepare("SELECT note_moy from produit_note where id_produit = :id_produit");
        $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_INT);
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }

    function detail_produit_image($id_produit) {
        global $pdo;

        $sql = "SELECT * FROM produit 
        INNER JOIN _image ON id_image_principale = _image.id_image
        WHERE id_produit = :id_produit";

        $requete = $pdo->prepare($sql);
        $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_INT);
        $requete->execute();

        return $requete->fetch(PDO::FETCH_ASSOC);
    }

    function vendeur_image_produit($id_produit){
        global $pdo;
        
        $requete = $pdo->prepare('SELECT * FROM _images_produit WHERE  id_produit = :id_produit');
        $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_INT);
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }

    function url_image_produit($id_image){
        global $pdo;
        
        $requete = $pdo->prepare('SELECT * FROM _image WHERE id_image = :id_image');
        $requete->bindValue(':id_image', $id_image, PDO::PARAM_INT);
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }

    function vendeur_verif_produit($id_produit, $id_vendeur){
        global $pdo;
        
        $requete = $pdo->prepare('SELECT id_produit from produit WHERE id_vendeur = :id_vendeur AND id_produit = :id_produit');
        $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_INT);
        $requete->bindValue(':id_vendeur', $id_vendeur, PDO::PARAM_INT);
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }

    function vendeur_All_produit($id_vendeur){
        global $pdo;
        
        $requete = $pdo->prepare('select id_produit, nom_stock, quantite, en_promotion, (ROUND(prix_actuel, 2) <> ROUND(prix, 2)) AS en_reduction from produit where id_vendeur = :id_vendeur');
        $requete->bindValue(':id_vendeur', $id_vendeur, PDO::PARAM_INT);
        $requete->execute();
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }

    // Nom public, prix, moyenne des notes et informations de l'image de chaque produit
    function info_produit_accueil(){
        global $pdo;
        
        $requete = $pdo->prepare('
        SELECT p.*, url_image, alt, _image.titre
        FROM produit_en_ligne p
        INNER JOIN _image ON id_image_principale = _image.id_image');
        $requete->execute();
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }

    // Nom public, prix, moyenne des notes et informations de l'image des produits alimentaires
    function info_produit_accueil_categorie($categorie){
        global $pdo;
        
        $requete = $pdo->prepare('
        SELECT p.*, url_image, alt, _image.titre
        FROM produit_en_ligne p
        INNER JOIN _image ON id_image_principale = _image.id_image
        WHERE p.categorie = :categorie
        
        UNION
        
        SELECT p.*, url_image, alt, _image.titre
        FROM produit_en_ligne p
        INNER JOIN _image ON id_image_principale = _image.id_image
        INNER JOIN _categorie ON nom_categorie = categorie
        WHERE nom_categorie_sup = :categorie');
        $requete->bindValue(':categorie', $categorie, PDO::PARAM_STR);
        $requete->execute();
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }

    // Nom public, prix, moyenne des notes et informations de l'image des produits les plus récents
    function info_produit_accueil_plus_recent(){
        global $pdo;
        
        $requete = $pdo->prepare('
        SELECT p.*, url_image, alt, _image.titre
        FROM produit_en_ligne p
        INNER JOIN _image ON id_image_principale = _image.id_image
        ORDER BY date_creation DESC;');
        $requete->execute();
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Nom public, prix, moyenne des notes et informations de l'image des produits en réduction
    function info_produit_accueil_promotion(){
        global $pdo;
        
        $requete = $pdo->prepare('
        SELECT p.*, url_image, alt, _image.titre
        FROM produit_en_ligne p
        INNER JOIN _image ON id_image_principale = _image.id_image
        WHERE en_promotion <> 0
        ORDER BY date_creation DESC;');
        $requete->execute();
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }

    function get_url_image($id_image) {
        global $pdo;

        $requete = $pdo->prepare('SELECT url_image AS url, titre, alt FROM _image WHERE id_image = :id_image');
        $requete->bindParam(":id_image", $id_image, PDO::PARAM_INT);
        $requete->execute();

        return $requete->fetch(PDO::FETCH_ASSOC);
    }


    function supprimer_produit_panier($id_produit,$id_compte){
        global $pdo;

        $requete = $pdo->prepare('DELETE FROM _elt_panier WHERE id_produit =:id_produit and id_client = :id_client;');
        $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_INT);
        $requete->bindValue(':id_client', $id_compte, PDO::PARAM_INT);
        $requete->execute();
    }

    function supprimer_produit_stock($id_produit) {
        global $pdo;
        $image = get_image_produit($id_produit);
        print_r($image);
        delete_image_produit($id_produit);

        //delete_image($image['id_image_principale']);
        if(!empty($image['id_image1'])){
            delete_image($image['id_image1']);
        } else if (!empty($image['id_image2'])) {
            delete_image($image['id_image2']);
        }

        $requete = $pdo->prepare('UPDATE _produit SET est_supprime = 1 WHERE id_produit = :id_produit');
        $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_INT);
        $requete->execute();
    }

    function delete_image_produit($id_produit){
        global $pdo;
        try{
            $stmt = $pdo->prepare("UPDATE _images_produit SET id_image1 = NULL, id_image2 = NULL WHERE id_produit = :id_produit");
            $stmt->execute([
                "id_produit" => $id_produit
            ]);
        } catch (PDOException $e){
            throw $e;
        }
    }

    function supprimer_images_produit($id_produit) {
        foreach (glob(HOME_SITE . "ressources/produit/" . $id_produit . "*.png") as $filename) {
            unlink($filename);
        }
    }

    function update_info_produit($id_produit, $libelle_prive, $libelle_public, $prix_ht,
    $tva, $code_barre, $reserve_majeur, $en_ligne, $qt_achete,
    $quantite_stock, $seuil_alerte, $desc_simple, 
    $desc_detaille, $poid, $volume_colis, $categorie){
        /**
         * Fonction set_info_produit() prend en parametre tout les informations d'un produit
         * Modifie les informations du produit la base de données
         */
        global $pdo;
        try{
            $requete = $pdo->prepare("UPDATE _produit 
                                SET nom_stock = :libelle_prive,
                                    nom_public = :libelle_public,
                                    description = :desc_simple,
                                    description_detaillee = :desc_detaille,
                                    categorie = :categorie,
                                    code_barre = :code_barre,
                                    quantite = :quantite_stock,
                                    prix = :prix_ht,
                                    tva = :tva,
                                    en_ligne = :en_ligne,
                                    seuil_alerte = :seuil_alerte,
                                    poids = :poid,
                                    volume = :volume_colis,
                                    plus_18 = :reserve_majeur,
                                    quantite_unite = :qt_achete
                                WHERE id_produit = :id_produit");
            $requete->execute(["id_produit" => $id_produit, 
                            "libelle_prive" => $libelle_prive, 
                            "libelle_public" => $libelle_public,
                            "desc_simple" => $desc_simple,
                            "desc_detaille" => $desc_detaille,
                            "categorie" => $categorie,
                            "code_barre" => $code_barre,
                            "quantite_stock" => $quantite_stock,
                            "prix_ht" => $prix_ht,
                            "tva" => $tva,
                            "en_ligne" => $en_ligne,
                            "seuil_alerte" => $seuil_alerte,
                            "poid" => $poid,
                            "volume_colis" => $volume_colis,
                            "reserve_majeur" => $reserve_majeur,
                            "qt_achete" => $qt_achete]);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    function add_produit($id_compte_vendeur, $libelle_prive, $libelle_public, $prix_ht,
    $tva, $code_barre, $reserve_majeur, $qt_achete, $quantite_stock, $seuil_alerte,
    $desc_simple, $desc_detaille, $poid, $volume_colis, $categorie){
        /**
         * Fonction add_produit() prend en parametre toutes les informations du produit
         * Ajoute un nouveau produit dans la base de donnée
         * Renvoie l'id du produit qui vient d'etre ajouté
         */
        global $pdo;

        try{ 
            $requete = $pdo->prepare("INSERT INTO _produit (id_vendeur, nom_stock, nom_public, description, description_detaillee, categorie, code_barre, quantite, prix, tva, seuil_alerte, poids, volume, plus_18, quantite_unite)
                                    VALUES(:id_vendeur,
                                            :nomPrv,
                                            :nomPblc,
                                            :descSimple,
                                            :descDetaille,
                                            :categorie,
                                            :codeBarre,
                                            :qtStock,
                                            :prixProd,
                                            :tva,
                                            :seuilAlerte,
                                            :poidProd,
                                            :volumeProd,
                                            :checkMajeur,
                                            :qtunite)"
            );
            $requete->execute([
                'id_vendeur' => $id_compte_vendeur,
                'nomPrv' => $libelle_prive,
                'nomPblc' => $libelle_public,
                'descSimple' => $desc_simple,
                'descDetaille' => $desc_detaille,
                'categorie' => $categorie,
                'codeBarre' => $code_barre,
                'qtStock' => $quantite_stock,
                'prixProd' => $prix_ht,
                'tva' => $tva,
                'seuilAlerte' => $seuil_alerte,
                'poidProd' => $poid, 
                'volumeProd' => $volume_colis,
                'checkMajeur' => $reserve_majeur ? 1 : 0,
                'qtunite' => $qt_achete
            ]);

            $id = $pdo->lastInsertId();
            return $id;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    function get_categorie_produit($id_produit){
            /**
             * Fonction get_categorie_produit() prend en parametre l'id du produit
             * Renvoie un tableau avec toute les informations d'un produit
             */
            global $pdo;
            try{
                $requete = $pdo->prepare("SELECT categorie FROM _produit WHERE id_produit = :id_produit");
                $requete->execute(["id_produit" => $id_produit]);
                $tabCategorie = $requete->fetch(PDO::FETCH_ASSOC);
                return $tabCategorie;
            } catch(PDOException $e){
                throw $e;
            }
        }
    function add_produit_categorie($id_produit, $categorie){
        /**
         * Fonction add_produit_categorie() prend en parametre l'id d'un produit et une categorie
         * Fait le lien entre la categorie et l'id du produit donnés en parametre
         */
        global $pdo;
        try{
            $sqlProduitCategorie = "INSERT INTO _produit_dans_categorie(id_produit,nom_categorie)
                                                VALUES(:id_prod,:nom_cate);
                                                ";
            $requete = $pdo->prepare($sqlProduitCategorie);
            $requete->execute([
                ':id_prod' => $id_produit,
                ':nom_cate' => $categorie
            ]);
        } catch(PDOException $e){
            throw $e;
        }
    }

    function update_categorie_produit($id_produit, $categorie){
        /**
         * Fonction update_categorie_produit() prend en parametre l'id du produit et une categorie
         * Met a jour la categorie du produit
         */
        global $pdo;
        try{
            $requete = $pdo->prepare("UPDATE _produit_dans_categorie SET nom_categorie = :categorie WHERE id_produit = :id_produit");
            $requete->execute([
                ":categorie" => $categorie,
                ":id_produit"=> $id_produit]);
        } catch(PDOException $e){
            throw $e;
        }
    }

    function add_image($url, $titre_img, $altDefault){
        /**
         * Fonction add_image() prend en parametre les caractéristiques d'une image
         * Ajoute l'image dans la base de données avec les parametres
         * Renvoie l'id de l'image dernierment ajouté
         */
        global $pdo;
        try{
            $requete = $pdo->prepare("INSERT INTO _image(url_image,titre,alt)VALUES(:url_img, :titre_img, :alt_img);");
            $requete->execute([
                ':url_img' => $url, 
                ':titre_img' => $titre_img, 
                ':alt_img' => $altDefault
            ]);
            return $pdo->lastInsertId();
        } catch(PDOException $e){
            throw $e;
        }
    }

    function add_image_produit($idProduit,$idImage){
        /**
         * Fonction add_image_produit() prend en parametre l'id du produit et l'id d'une image
         * Fait le lien entre une nouvelle image et un produit
         */
        global $pdo;
        try{
            $sqlImageProduit = "INSERT INTO _images_produit(id_produit,id_image_principale)
                                VALUES(:id_prod,:id_image_princ);";
            $requete = $pdo->prepare($sqlImageProduit);
            $requete->execute([
                ':id_prod' => $idProduit,
                ':id_image_princ' => $idImage
            ]);
        } catch(PDOException $e){
            throw $e;
        }
    }

    function link_image_produit($idProduit,$idImage,$numeroImage){
        /**
         * Fonction link_image_produit() prend en parametre l'id du produit, l'id de l'image ainsi que le numero de l'image (2 ou 3)
         * Fait le lien entre la deuxieme ou troisieme image du produit  
         */
        global $pdo;
        try{
            if($numeroImage === 2){
                $requete = $pdo->prepare("UPDATE _images_produit SET id_image1 = :id_image WHERE id_produit = :id_produit");
            } else if ($numeroImage === 3){
                $requete = $pdo->prepare("UPDATE _images_produit SET id_image2 = :id_image WHERE id_produit = :id_produit");
            } else { return; }
            $requete->execute([
                ':id_produit' => $idProduit,
                'id_image'=> $idImage,
            ]);
        } catch(PDOException $e){
            throw $e;
        }
    }

    function update_image_produit($idImage, $url, $titre_img, $altDefault){
        /**
         * Fonction update_image_produit() prend en parametre les informations d'une image 
         * Met a jour les informations d'une image dans la base de données
         */
        global $pdo;
        try{
            $requete = $pdo->prepare('UPDATE _image 
                                    SET url_image = :url_image,
                                    titre = :titre_img,
                                    alt = :altDefault 
                                    WHERE id_image = :idImage');
            $requete->execute([
                ':url_image' => $url,
                ':titre_img' => $titre_img,
                ':altDefault' => $altDefault,
                ':idImage' => $idImage,
            ]);    
        } catch(PDOException $e){
            throw $e;
        }
    }

    function get_image_produit($idProduit){
        /**
         * Fonction get_id_image_produit prend en parametre l'id d'un produit
         * Renvoie un tableau avec l'id du produit et les id des images en lien avec celui-ci
         */
        global $pdo;

        try{
            $requete = $pdo->prepare('SELECT * FROM _images_produit WHERE id_produit = :id_produit');
            $requete->execute([ ':id_produit' => $idProduit]);

            $tabIdImage = $requete->fetch(PDO::FETCH_ASSOC);
            return $tabIdImage;
        } catch(PDOException $e){
            throw $e;
        }
    }

    /*
    Modifie la valeur du stock d'un produit
    si on veut mettre une valeur, juste mettre un entier dans la quantité
    si on veut ajouter une valeur au stock (`stock += 1`), alors mettre "+1" en quantité (donc un str)
    si on veut retirer une valeur du stock (`stock -= 1`), alors mettre "-1" en quantité (donc un str)
    */
    function update_stock($id_produit, $quantite){
        global $pdo;

        $ancienne_quantite = detail_produit($id_produit)['quantite'];
        if (str_contains($quantite, '+') or str_contains($quantite, '-')) {
            $nouvelle_quantite = $ancienne_quantite + (int) $quantite;
        }

        else {
            $nouvelle_quantite = (int) $quantite;
        }

        if ($nouvelle_quantite < 0) {
            return false;
        }

        $requete = $pdo->prepare('UPDATE _produit SET quantite = :quantite WHERE id_produit = :id_produit');
        $requete->bindValue(":quantite", $nouvelle_quantite, PDO::PARAM_INT);
        $requete->bindValue(":id_produit", $id_produit, PDO::PARAM_INT);
        $requete->execute();

        return true;
    }


    function vendeur_possede_produit($id_vendeur, $id_produit){
        global $pdo;
        
        $requete = $pdo->prepare('SELECT * FROM produit WHERE id_vendeur = :id_vendeur AND id_produit = :id_produit');
        $requete->bindValue(':id_vendeur', $id_vendeur, PDO::PARAM_INT);
        $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_INT);
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }

    function renvoi(){
        if (headers_sent()) {
            die('Échec de redirection. Cliquez sur ce lien svp : <a href="../">Ici</a>');
        }
        else{
            exit(header("Location: ../"));
        }
    }

    function creer_promotion($id_produit, $date_debut, $date_fin, $reduction, $id_image_banniere){
        global $pdo;

        $stmt = $pdo->prepare("INSERT INTO _promotion(id_produit, date_debut, date_fin, reduction, id_image_banniere)
            VALUES (:id_produit, :date_debut, :date_fin, :reduction, :id_image)");
        $stmt->execute([
            "id_produit" => $id_produit,
            "date_debut" => $date_debut,
            "date_fin" => $date_fin,
            "reduction" => $reduction,
            "id_image" => $id_image_banniere
        ]);
    }

    function banniere_libre($date1,$date2){
        global $pdo;
        
        $stmt = $pdo->prepare("SELECT periode_banniere_libre(:date1,:date2) AS is_active");
        $stmt->execute([
            "date1" => $date1,
            "date2" => $date2
        ]);
        $resultat = $stmt->fetch(PDO::FETCH_ASSOC)['is_active'];
        if($resultat === 1){ return true; }
        else { return false; }
         
    }

    function produit_est_en_promotion($id_produit){

        $resultat = get_info_promotion($id_produit);
        if($resultat != null){
            return true;
        } else {
            return false;
        }
    }

    function get_info_promotion($id_produit){
        global $pdo;

        $stmt = $pdo->prepare("SELECT * FROM _promotion WHERE id_produit = :id_produit");
        $stmt->execute([
            "id_produit" => $id_produit
        ]);

        return $stmt->fetchall(PDO::FETCH_ASSOC);
    }

    function get_info_promotion_unique($id_promo){
        global $pdo;
        
        $stmt = $pdo->prepare("SELECT * FROM _promotion WHERE id_promo = :id_promo");
        $stmt->execute([
            "id_promo" => $id_promo
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function update_promotion($id_promotion, $id_produit, $date_debut, $date_fin, $reduction, $id_image_banniere){
        global $pdo;

        $stmt = $pdo->prepare("UPDATE _promotion 
        SET id_produit = :id_produit,
            date_debut = :date_debut,
            date_fin = :date_fin,
            reduction = :reduction,
            id_image_banniere = :id_image_banniere 
        WHERE id_promo = :id_promotion");

        $stmt->execute([
            "id_produit" => $id_produit,
            "date_debut" => $date_debut,
            "date_fin" => $date_fin,
            "reduction" => $reduction,
            "id_image_banniere" => $id_image_banniere,
            "id_promotion" => $id_promotion
        ]);
    }

    function get_image_promotion($id_image){
        global $pdo;

        $stmt = $pdo->prepare("SELECT * FROM _image WHERE id_image = :id_image");
        $stmt->execute([
            "id_image" => $id_image
        ]);
        if($stmt != null){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            return 0;
        }
    }

    function delete_promotion($id_promo){
        global $pdo;
        
        try{
            $tab = get_info_promotion_unique($id_promo);

            $stmt = $pdo->prepare("DELETE FROM _promotion WHERE id_promo = :id_promo");
            $stmt->execute([
                "id_promo" => $id_promo
            ]);
            if($tab['id_image_banniere'] != null){
                delete_image($tab['id_image_banniere']);
            }
        } catch(PDOException $e){
            throw $e;
        }
    }

    function delete_image($id_image){
        global $pdo;
        
        $tab_image = get_image($id_image);

        $stmt = $pdo->prepare("DELETE FROM _image WHERE id_image = :id_image");
        $stmt->execute([
            "id_image" => $id_image
        ]);
        unlink($_SERVER['DOCUMENT_ROOT'] . "/" . $tab_image['url_image']);
    }

    function delete_image_bdd($id_image){
        global $pdo;
        
        $tab_image = get_image($id_image);

        $stmt = $pdo->prepare("DELETE FROM _image WHERE id_image = :id_image");
        $stmt->execute([
            "id_image" => $id_image
        ]);
    }

    function get_image($id_image){
        global $pdo;

        $stmt = $pdo->prepare("SELECT * FROM _image WHERE id_image = :id_image");
        $stmt->bindValue(":id_image", $id_image, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    function date_banniere_occupe(){
        global $pdo;

        $stmt = $pdo->prepare("SELECT date_debut, date_fin FROM `_promotion` WHERE id_image_banniere IS NOT NULL");
        $stmt->execute();

        $tab = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $tab_final = array();
        foreach($tab as $lignes){
            array_push($tab_final, $lignes['date_debut'], $lignes['date_fin']);
        }
        return $tab_final;
    }

    function unlink_image_produit($id_image, $id_produit){
        global $pdo;

        try{
            $tab = get_image_produit($id_produit);

            if($tab['id_image1'] == $id_image){
                $stmt = $pdo->prepare("UPDATE _images_produit SET id_image1 = NULL WHERE id_produit = :id_produit");
            } else if ($tab['id_image2'] == $id_image) {
                $stmt = $pdo->prepare("UPDATE _images_produit SET id_image2 = NULL WHERE id_produit = :id_produit");
            } else {
                $stmt = null;
            }

            if($stmt != null){
                $stmt->execute([
                    "id_produit" => $id_produit
                ]);
            }
        } catch (PDOException $e){
            throw $e;
        }
    }
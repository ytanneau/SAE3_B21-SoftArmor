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
        
        $requete = $pdo->prepare("SELECT * from _produit where id_produit = :id_produit");
        $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_INT);
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }

    function detail_produit_image($id_produit) {
        global $pdo;

        $sql = "SELECT * FROM produit_image WHERE id_produit = :id_produit";

        $requete = $pdo->prepare($sql);
        $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_INT);
        $requete->execute();

        return $requete->fetch(PDO::FETCH_ASSOC);
    }

    function vendeur_image_produit($id_produit){
        global $pdo;
        
        $requete = $pdo->prepare('SELECT * FROM `_images_produit` WHERE  id_produit = :id_produit');
        $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_INT);
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }

    function url_image_produit($id_image){
        global $pdo;
        
        $requete = $pdo->prepare('SELECT * FROM _image WHERE  id_image = :id_image');
        $requete->bindValue(':id_image', $id_image, PDO::PARAM_INT);
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }

    function vendeur_verif_produit($id_produit, $id_vendeur){
        global $pdo;
        
        $requete = $pdo->prepare('SELECT id_produit from _produit where id_vendeur = :id_vendeur AND id_produit = :id_produit');
        $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_INT);
        $requete->bindValue(':id_vendeur', $id_vendeur, PDO::PARAM_INT);
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }

    function vendeur_All_produit($id_vendeur){
        global $pdo;
        
        $requete = $pdo->prepare('select id_produit, nom_stock, quantite from _produit where id_vendeur = :id_vendeur');
        $requete->bindValue(':id_vendeur', $id_vendeur, PDO::PARAM_INT);
        $requete->execute();
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }

    // Nom public, prix, moyenne des notes et informations de l'image de chaque produit
    function info_produit_accueil(){
        global $pdo;
        
        $requete = $pdo->prepare('SELECT produit_visible.id_produit,nom_public,prix,url_image,alt,_image.titre,note_moy AS moyenne FROM produit_visible INNER JOIN _images_produit ON produit_visible.id_produit = _images_produit.id_produit INNER JOIN _image ON _images_produit.id_image_principale = _image.id_image INNER JOIN produit_note ON produit_note.id_produit = produit_visible.id_produit WHERE produit_note.id_produit = produit_visible.id_produit;');
        $requete->execute();
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }

    // Nom public, prix, moyenne des notes et informations de l'image des produits alimentaires
    function info_produit_accueil_categorie($categorie){
        global $pdo;
        
        $requete = $pdo->prepare('SELECT produit_visible.id_produit,nom_public,prix,url_image,alt,_image.titre,note_moy AS moyenne FROM produit_visible INNER JOIN _images_produit ON produit_visible.id_produit = _images_produit.id_produit INNER JOIN _image ON _images_produit.id_image_principale = _image.id_image INNER JOIN produit_note ON produit_note.id_produit = produit_visible.id_produit INNER JOIN _produit_dans_categorie ON produit_visible.id_produit = _produit_dans_categorie.id_produit WHERE produit_note.id_produit = produit_visible.id_produit AND _produit_dans_categorie.nom_categorie = :categorie;');
        $requete->bindValue(':categorie', $categorie, PDO::PARAM_STR);
        $requete->execute();
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }

    // Nom public, prix, moyenne des notes et informations de l'image des produits les plus récents
    function info_produit_accueil_plus_recent(){
        global $pdo;
        
        $requete = $pdo->prepare('SELECT produit_visible.id_produit,nom_public,prix,url_image,alt,_image.titre,note_moy AS moyenne FROM produit_visible INNER JOIN _images_produit ON produit_visible.id_produit = _images_produit.id_produit INNER JOIN _image ON _images_produit.id_image_principale = _image.id_image INNER JOIN produit_note ON produit_note.id_produit = produit_visible.id_produit INNER JOIN _produit_dans_categorie ON produit_visible.id_produit = _produit_dans_categorie.id_produit WHERE produit_note.id_produit = produit_visible.id_produit ORDER BY date_creation DESC;');
        $requete->execute();
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Nom public, prix, moyenne des notes et informations de l'image des produits en réduction
    function info_produit_accueil_reduction(){
        global $pdo;
        
        $requete = $pdo->prepare('SELECT produit_visible.id_produit,nom_public,prix,url_image,alt,_image.titre,note_moy AS moyenne,TRUNCATE((prix - prix*reduction*0.01),2) AS prix_reduit FROM produit_visible INNER JOIN _images_produit ON produit_visible.id_produit = _images_produit.id_produit INNER JOIN _image ON _images_produit.id_image_principale = _image.id_image INNER JOIN produit_note ON produit_note.id_produit = produit_visible.id_produit INNER JOIN _produit_dans_categorie ON produit_visible.id_produit = _produit_dans_categorie.id_produit INNER JOIN promo_jour ON produit_visible.id_produit = promo_jour.id_produit WHERE produit_note.id_produit = produit_visible.id_produit;');
        $requete->execute();
        return $requete->fetchAll(PDO::FETCH_ASSOC);
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
        
        $requete = $pdo->prepare('DELETE FROM _produit WHERE id_produit = :id_produit');
        $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_INT);
        $requete->execute();
        return $requete->rowCount() > 0;
    }

    function get_info_produit($id_compte_vendeur, $id_produit){
        global $pdo;
        // cette fonction renvoie un tableau avec les informations d'un produit
        try {
            $stmt = $pdo->prepare("SELECT * FROM _produit WHERE id_vendeur = :id_compte_vendeur and id_produit = :id_produit;");
            $stmt->execute([':id_compte_vendeur' => $id_compte_vendeur, ':id_produit' => $id_produit]);

            $tableau = $stmt->fetch(PDO::FETCH_ASSOC);   
            return $tableau;
        } catch(PDOException $e) {
            throw $e;
        }
    }

    function set_info_produit($id_compte_vendeur,$id_produit, $libelle_prive, $libelle_public, $prix_ht,
    $tva, $code_barre, $reserve_majeur, $en_ligne, $qt_achete,
    $quantite_stock, $seuil_alerte, $desc_simple, 
    $desc_detaille, $poid, $volume_colis){
        global $pdo;
        try{
            $stmt = $pdo->prepare("UPDATE _produit 
                                SET nom_stock = :libelle_prive,
                                    nom_public = :libelle_public,
                                    description = :desc_simple,
                                    description_detaillee = :desc_detaille,
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
                                WHERE id_produit = :id_produit AND id_vendeur = :id_vendeur");
            $stmt->execute([":id_vendeur"=> $id_compte_vendeur, 
                            ":id_produit" => $id_produit, 
                            ":libelle_prive" => $libelle_prive, 
                            ":libelle_public" => $libelle_public,
                            ":desc_simple" => $desc_simple,
                            ":desc_detaille" => $desc_detaille,
                            ":code_barre" => $code_barre,
                            ":quantite_stock" => $quantite_stock,
                            ":prix_ht" => $prix_ht,
                            ":tva" => $tva,
                            ":en_ligne" => $en_ligne,
                            ":seuil_alerte" => $seuil_alerte,
                            ":poid" => $poid,
                            ":volume_colis" => $volume_colis,
                            ":reserve_majeur" => $reserve_majeur,
                            ":qt_achete" => $qt_achete]);
        } catch (PDOException $e) {
            throw $e; // lance une erreur que la fonction appelante catchera
        }
    }

    function add_produit($id_compte_vendeur, $libelle_prive, $libelle_public, $prix_ht,
    $tva, $code_barre, $reserve_majeur, $qt_achete, $quantite_stock, $seuil_alerte,
    $desc_simple, $desc_detaille, $poid, $volume_colis){
        global $pdo;

        try{
            $sqlAjoutProduit = "INSERT INTO _produit(id_vendeur,nom_stock,nom_public,description,description_detaillee,code_barre,quantite,prix,tva,seuil_alerte,poids,volume,plus_18,quantite_unite) 
                                VALUES(:id_vend, :nomPrv, :nomPblc, :descSimple, :descDetaille, :codeBarre, :qtStock, :prixProd, :tva, :seuilAlerte, :poidProd, :volumeProd, :checkMajeur, :qtunite); 
                                ";
            $stmt = $pdo->prepare($sqlAjoutProduit);
            $stmt->execute([
                ':id_vend' => $id_compte_vendeur,
                ':nomPrv' => $libelle_prive,
                ':nomPblc' => $libelle_public,
                ':descSimple' => $desc_simple,
                ':descDetaille' => $desc_detaille,
                ':codeBarre' => $code_barre,
                ':qtStock' => $quantite_stock,
                ':prixProd' => $prix_ht,
                ':tva' => $tva,
                ':seuilAlerte' => $seuil_alerte,
                ':poidProd' => $poid, 
                ':volumeProd' => $volume_colis,
                ':checkMajeur' => $reserve_majeur,
                ':qtunite' => $qt_achete
            ]);

            $id = $pdo->lastInsertId();
            return $id;
        } catch (PDOException $e) {
            throw $e; // lance une erreur que la fonction appelante catchera
        }
    }

    function get_categorieProduit($id_produit){
            global $pdo;
            try{
                $stmt = $pdo->prepare("SELECT * FROM _produit_dans_categorie WHERE id_produit = :id_produit");
                $stmt->execute([":id_produit" => $id_produit]);
                $tabCategorie = $stmt->fetch(PDO::FETCH_ASSOC);
                return $tabCategorie;
            } catch(PDOException $e){
                throw $e;
            }
        }
    function add_produit_categorie($id_produit, $categorie){
        global $pdo;
        try{
            $sqlProduitCategorie = "INSERT INTO _produit_dans_categorie(id_produit,nom_categorie)
                                                VALUES(:id_prod,:nom_cate);
                                                ";
            $stmt = $pdo->prepare($sqlProduitCategorie);
            $stmt->execute([
                ':id_prod' => $id_produit,
                ':nom_cate' => $categorie
            ]);
        } catch(PDOException $e){
            throw $e;
        }
    }

    function update_categorie_produit($id_produit, $categorieDuProduit){
        global $pdo;
        try{
            $stmt = $pdo->prepare("UPDATE _produit_dans_categorie SET nom_categorie = :categorie WHERE id_produit = :id_produit");
            $stmt->execute([
                ":categorie" => $categorieDuProduit,
                ":id_produit"=> $id_produit]);
        } catch(PDOException $e){
            throw $e;
        }
    }

    function add_image($url, $titre_img, $altDefault){
        global $pdo;
        try{
            $sqlImage = "INSERT INTO _image(url_image,titre,alt)
                        VALUES(:url_img, :titre_img, :alt_img);";
            $stmt = $pdo->prepare($sqlImage);
            $stmt->execute([
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
        global $pdo;
        try{
            $sqlImageProduit = "INSERT INTO _images_produit(id_produit,id_image_principale)
                                VALUES(:id_prod,:id_image_princ);";
            $stmt = $pdo->prepare($sqlImageProduit);
            $stmt->execute([
                ':id_prod' => $idProduit,
                ':id_image_princ' => $idImage
            ]);
        } catch(PDOException $e){
            throw $e;
        }
    }

    function link_image_produit($idProduit,$idImage,$numeroImage){
        global $pdo;
        try{
            if($numeroImage === 2){
                $stmt = $pdo->prepare("INSERT INTO _images_produit(id_image1) VALUES(:id_image) WHERE id_produit = :id_produit");
                $stmt->execute([
                    ':id_produit' => $idProduit,
                    'id_image'=> $idImage,
                ]);
            } else if ($numeroImage === 3){
                $stmt = $pdo->prepare("INSERT INTO _images_produit(id_image2) VALUES(:id_image) WHERE id_produit = :id_produit");
                $stmt->execute([
                    ':id_produit' => $idProduit,
                    'id_image'=> $idImage,
                ]);
            }
        } catch(PDOException $e){
            throw $e;
        }
    }

    function update_image_produit($idImage, $url, $titre_img, $altDefault){
        global $pdo;

        try{
            $stmt = $pdo->prepare('UPDATE _image 
                                    SET url_image = :url_image,
                                    titre = :titre_img,
                                    alt = :altDefault 
                                    WHERE id_image = :idImage');
            $stmt->execute([
                ':url_image' => $url,
                ':titre_img' => $titre_img,
                ':altDefault' => $altDefault,
                ':idImage' => $idImage,
            ]);    
        } catch(PDOException $e){
            throw $e;
        }
    }

    function get_id_image_produit($idProduit){
        global $pdo;

        try{
            $stmt = $pdo->prepare('SELECT * FROM _images_produit WHERE id_produit = :id_produit');
            $stmt->execute([ ':id_produit' => $idProduit]);

            $tabIdImage = $stmt->fetch(PDO::FETCH_ASSOC);
            return $tabIdImage;
        } catch(PDOException $e){
            throw $e;
        }
    }
?>
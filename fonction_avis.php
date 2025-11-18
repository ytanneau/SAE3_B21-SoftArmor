<?php
    require_once HOME_GIT . '.config.php';

    

    // fonction qui verifie les champs de l'avis 
    function condition_avis(){
        $erreur = [];

        if (!isset($_POST['note'])){
            $erreur['note'] = VIDE;
        }
        else if (!(1 <= $_POST['note'] && $_POST['note'] <= 5)){
            $erreur['note'] = FORMAT;
        }

        if (isset($_POST['description']) && !isset($_POST['titre'])){
            $erreur['titre'] = "Une description a besoin d'un titre";
        }
        else if (strlen($_POST['titre']) > TAILLE_TITRE){
            $erreur['titre'] = DEPASSE;
        }

        if (strlen($_POST['description']) > TAILLE_DESCRIPTION){
            $erreur['description'] = DEPASSE;
        }

        if (preg_match("/png/",$_FILES['image']['type'])){
            $erreur['image'] = "Type de l'image";
        }
        else if ($_FILES['image']['size'] > TAILLE_IMAGE){
            $erreur['image'] = "Image trop lourd";
        }

        $sql_produit = detail_produit_image($_POST['produit']);
        if ($sql_produit == null){
            $erreur['produit'] = EXISTE_PAS;
        }

        return $erreur;
    }

    // retourne les avis des client pour un produit donnée
    function avis_client_produit($id_produit){
        global $pdo;
        try {
            $requete = $pdo->prepare("SELECT * FROM avis_client WHERE id_produit = :id_produit");
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
            $requete = $pdo->prepare("SELECT _avis.id_produit,pseudo,date_avis,note,_avis.titre as titre,commentaire,compte_image_profil.url_image AS url_pdp,compte_image_profil.titre_image AS titre_pdp,compte_image_profil.alt_image AS alt_pdp,_image.url_image AS url_img,_image.titre AS titre_img,_image.alt AS alt_img FROM compte_client 
                                        INNER JOIN _avis 
                                        ON compte_client.id_compte = _avis.id_client 
                                        LEFT JOIN compte_image_profil 
                                        ON compte_client.id_compte = compte_image_profil.id_compte 
                                        INNER JOIN _images_produit 
                                        ON _avis.id_produit = _images_produit.id_produit 
                                        INNER JOIN _image 
                                        ON _images_produit.id_image_principale = _image.id_image 
                                        WHERE compte_client.id_compte = :id_client;");
            $requete->bindValue(':id_client', $id_client, PDO::PARAM_STR);
            $requete->execute();
            return $requete->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    // requete pour crée un avis
    function cree_avis($id_client, $id_produit, $note, $titre , $description, $image){
        global $pdo;
        try {
            $requete = $pdo->prepare("CALL creer_avis(:id_client, :id_produit, :note, :titre, :description, :url, :img_titre, :alt)");
            $requete->bindValue(':id_client', $id_client, PDO::PARAM_INT);
            $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_INT);
            $requete->bindValue(':note', $note, PDO::PARAM_INT);
            $requete->bindValue(':titre', $titre, PDO::PARAM_STR);
            $requete->bindValue(':description', $description, PDO::PARAM_STR);
            $requete->bindValue(':url', $image, PDO::PARAM_STR);
            $requete->bindValue(':img_titre', 'image avis', PDO::PARAM_STR);
            $requete->bindValue(':alt', 'image avis', PDO::PARAM_STR);
            $requete->execute();
            return 0;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    // verifie si un avis existe déjà
    function check_avis_existe($id_produit, $id_client){
        global $pdo;
        try {
            $requete = $pdo->prepare("SELECT * FROM avis_client WHERE id_produit=:id_produit AND id_client=:id_client");
            $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_INT);
            $requete->bindValue(':id_client', $id_client, PDO::PARAM_INT);
            $requete->execute();
            return $requete->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }


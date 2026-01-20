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

        if (strlen(trim($_POST['description'])) > 0 && (strlen(trim($_POST['titre'])) == 0)){
            $erreur['titre'] = "Une description a besoin d'un titre";
        }
        else if (strlen($_POST['titre']) > TAILLE_TITRE){
            $erreur['titre'] = DEPASSE;
        }

        if (strlen($_POST['description']) > TAILLE_DESCRIPTION){
            $erreur['description'] = DEPASSE;
        }

        if ($_FILES['image']['size'] > 0 && $_FILES['image']['type'] !== 'image/png'){
            $erreur['image'] = "Type de l'image";
        }
        else if ($_FILES['image']['size'] > TAILLE_IMAGE){
            $erreur['image'] = "Image trop lourde";
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
            $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_INT);
            $requete->execute();
            return $requete->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    //requete pour recuperer les avis du compte
    function get_avis_client($id_client){
        global $pdo;
        try {
            $requete = $pdo->prepare("SELECT * FROM avis_client WHERE id_client = :id_client;");
            $requete->bindValue(':id_client', $id_client, PDO::PARAM_INT);
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
        
        $requete = $pdo->prepare("SELECT * FROM avis_client WHERE id_produit=:id_produit AND id_client=:id_client");
        $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_INT);
        $requete->bindValue(':id_client', $id_client, PDO::PARAM_INT);
        $requete->execute();
        
        return $requete->fetch(PDO::FETCH_ASSOC);
    }

    // récupérer un avis par son identifiant
    function get_avis($id_avis){
        global $pdo;
        try {
            $requete = $pdo->prepare("SELECT * FROM avis_client WHERE id_avis = :id_avis");
            $requete->bindValue(':id_avis', $id_avis, PDO::PARAM_INT);
            $requete->execute();
            return $requete->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    function avis_produit($id_produit){
        global $pdo;
        $requete = $pdo->prepare("SELECT * FROM _avis WHERE id_produit=:id_produit");
        $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_INT);
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }

    // fonction pour supprimer un avis avec image d'un client sur un produit
    function supprimer_avis_image($id_produit, $url_img_avis, $id_client){
        global $pdo;
        try {
            $requete = $pdo->prepare("DELETE FROM _avis WHERE id_produit = :id_produit AND id_client = :id_client;");
            $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_INT);
            $requete->bindValue(':id_client', $id_client, PDO::PARAM_INT);
            $requete->execute();
            
            
            
            $requete = $pdo->prepare("DELETE FROM _image WHERE url_image = :url_img_avis ;");
            $requete->bindValue(':url_img_avis', $url_img_avis, PDO::PARAM_STR);
            $requete->execute();
            
            
            return 0;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    // fonction pour supprimer un avis sans image d'un client sur un produit
    function supprimer_avis($id_produit, $id_client){
        global $pdo;
        try {
            $requete = $pdo->prepare("DELETE FROM _avis WHERE id_produit = :id_produit AND id_client = :id_client;");
            $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_INT);
            $requete->bindValue(':id_client', $id_client, PDO::PARAM_INT);
            $requete->execute();
            
            
            return 0;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    // fonction pour modifier un avis existant
    function modifier_avis($id_client, $id_produit, $note, $titre, $description, $image){
        global $pdo;
        try {
            $requete = $pdo->prepare("UPDATE _avis SET note = :note, titre = :titre, commentaire = :description, date_publication = NOW() WHERE id_client = :id_client AND id_produit = :id_produit");
            $requete->bindValue(':id_client', $id_client, PDO::PARAM_INT);
            $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_INT);
            $requete->bindValue(':note', $note, PDO::PARAM_INT);
            $requete->bindValue(':titre', $titre, PDO::PARAM_STR);
            $requete->bindValue(':description', $description, PDO::PARAM_STR);
            $requete->execute();

            // Gérer l'image si elle existe
            if ($image !== null) {
                // Vérifier si une image existe déjà pour cet avis
                $check_img = $pdo->prepare("SELECT i.url_image FROM _avis a LEFT JOIN _image i ON a.id_image = i.id_image WHERE a.id_client = :id_client AND a.id_produit = :id_produit");
                $check_img->bindValue(':id_client', $id_client, PDO::PARAM_INT);
                $check_img->bindValue(':id_produit', $id_produit, PDO::PARAM_INT);
                $check_img->execute();
                $existing_img = $check_img->fetch(PDO::FETCH_ASSOC);

                if ($existing_img && $existing_img['url_image']) {
                    // Mettre à jour l'image existante
                    $update_img = $pdo->prepare("UPDATE _image SET url_image = :url WHERE url_image = :old_url");
                    $update_img->bindValue(':url', $image, PDO::PARAM_STR);
                    $update_img->bindValue(':old_url', $existing_img['url_image'], PDO::PARAM_STR);
                    $update_img->execute();
                } else {
                    // Insérer une nouvelle image et la lier à l'avis
                    $insert_img = $pdo->prepare("INSERT INTO _image (url_image, titre_image, alt_image) VALUES (:url, :img_titre, :alt)");
                    $insert_img->bindValue(':url', $image, PDO::PARAM_STR);
                    $insert_img->bindValue(':img_titre', 'image avis', PDO::PARAM_STR);
                    $insert_img->bindValue(':alt', 'image avis', PDO::PARAM_STR);
                    $insert_img->execute();
                    
                    $id_image = $pdo->lastInsertId();
                    $link_img = $pdo->prepare("UPDATE _avis SET id_image = :id_image WHERE id_client = :id_client AND id_produit = :id_produit");
                    $link_img->bindValue(':id_image', $id_image, PDO::PARAM_INT);
                    $link_img->bindValue(':id_client', $id_client, PDO::PARAM_INT);
                    $link_img->bindValue(':id_produit', $id_produit, PDO::PARAM_INT);
                    $link_img->execute();
                }
            }
            
            return 0;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    function signaler_avis($id_compte, $id_avis, $raison, $email) {
        global $pdo;

        try {
            // Enregistrer l'avis dans la BDD
            $requete = $pdo->prepare(
                "INSERT INTO _signalement (id_compte, id_avis, raison, email)
                VALUES (:id_compte, :id_avis, :raison, :email)"
            );

            $requete->bindValue(':id_compte', $id_compte, PDO::PARAM_INT);
            $requete->bindValue(':id_avis', $id_avis, PDO::PARAM_INT);
            $requete->bindValue(':raison', $raison, PDO::PARAM_STR);
            $requete->bindValue(':email', $email, PDO::PARAM_STR);
            $requete->execute();

            // Marquer l'avis comme signalé
        } catch (PDOException $e) {
            throw $e;
        }
    }

    // Retourne true si le compte (ou l'email) en paramètre a signalé l'avis, false sinon
    function avis_est_signale($id_avis, $id_compte = null, $email = null) {
        global $pdo;

        if (!isset($id_compte) && !isset($email)) {
            return false;
        }

        $params = [];

        try {
            $sql = "SELECT 1 
                    FROM _signalement
                    WHERE id_avis = :id_avis";
            
            $params[':id_avis'] = $id_avis;

            if (isset($id_compte)) {
                $sql .= " AND id_compte = :id_compte";
                $params[':id_compte'] = $id_compte;
            } else if (isset($email)) {
                $sql .= " AND email = :email";
                $params[':email'] = $email;
            }

            $requete = $pdo->prepare($sql);
            $requete->execute($params);

            return $requete->rowCount() > 0;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    // Renvoie true si le compte en paramètre a rédigé l'avis, false sinon
    function avis_fait_par($id_avis, $id_compte) {
        if (!isset($id_compte)) {
            return false;
        }
    
        global $pdo;

        try {
            $requete = $pdo->prepare(
                "SELECT 1 
                FROM avis_client
                WHERE id_client = :id_compte
                AND id_avis = :id_avis"
            );

            $requete->bindValue(':id_compte', $id_compte, PDO::PARAM_INT);
            $requete->bindValue(':id_avis', $id_avis, PDO::PARAM_INT);
            $requete->execute();

            return $requete->rowCount() > 0;
        } catch (PDOException $e) {
            throw $e;
        }
    }
    

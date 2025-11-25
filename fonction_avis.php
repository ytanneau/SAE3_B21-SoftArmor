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

        print_r($_POST);
        var_dump($_POST);
        if (isset($_POST['description']) && (strlen($_POST['titre']) == 0)){
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
    function tout_avis_client($id_client){
        global $pdo;
        try {
            $requete = $pdo->prepare("SELECT * FROM tout_avis_client WHERE id_compte = :id_client;");
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
        try {
            $requete = $pdo->prepare("SELECT * FROM avis_client WHERE id_produit=:id_produit AND id_client=:id_client");
            $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_INT);
            $requete->bindValue(':id_client', $id_client, PDO::PARAM_INT);
            $requete->execute();
            return $requete->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    function avis_produit($id_produit, $id_vendeur){
        global $pdo;
        $requete = $pdo->prepare("SELECT * FROM avis_produit WHERE id_produit=:id_produit AND id_vendeur=:id_vendeur");
        $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_INT);
        $requete->bindValue(':id_vendeur', $id_vendeur, PDO::PARAM_INT);
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }


<?php

// fonction permettant d'ajouter les éléments d'une commande dans la BDD
// a besoin de l'id de la commande qui vient d'être créée, et de la liste des produits à ajouter
// $liste_produits est une liste, avec chaque élément de forme : 
// ["id_commande" => int, "id_produit" => int, "quantite" => int, "prix" => float, "nom_produit" => str, "nom_vendeur" => str]
function ajout_commande($id_commande, $liste_produits)
{
    global $pdo;

    foreach ($liste_produits as $produit) {
        $stmt = $pdo->prepare("INSERT INTO _elt_commande (id_commande, id_produit, quantite, prix, nom_produit, nom_vendeur) VALUES (:id_commande, :id_produit, :quantite, :prix, :nom_produit, :nom_vendeur)");
        $stmt->bindValue(":id_commande", $id_commande, PDO::PARAM_INT);
        $stmt->bindValue(":id_produit", $produit["id_produit"], PDO::PARAM_INT);
        $stmt->bindValue(":quantite", $produit["quantite"], PDO::PARAM_INT);
        $stmt->bindValue(":prix", $produit["prix"], PDO::PARAM_INT);
        $stmt->bindValue(":nom_produit", $produit["nom_produit"], PDO::PARAM_STR);
        $stmt->bindValue(":nom_vendeur", $produit["nom_vendeur"], PDO::PARAM_STR);

        $stmt->execute();
    }
}

// renvoie la liste des commandes faites par un client
function get_commandes($id_client)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT id_commande, date_commande, id_client, bordereau_colis 
    FROM _commande WHERE id_client = :id_client ORDER BY date_commande DESC");
    $stmt->bindValue(":id_client", $id_client, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// renvoie la liste des commandes où il y a un produit de ce vendeur dans cette commande
// chaque commande est sous la forme :
// ["id_commande" => int, "date_commande" => date, "pseudo_client" => str]
// on peut ensuite récupérer les éléments de ces commandes avec la fonction get_elements_commande_vendeur()
function get_commandes_vendeur($id_vendeur)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT DISTINCT _commande.id_commande, date_commande, pseudo AS pseudo_client FROM _commande 
    INNER JOIN _elt_commande ON _commande.id_commande = _elt_commande.id_commande
    INNER JOIN _produit ON _elt_commande.id_produit = _produit.id_produit
    INNER JOIN _client ON _commande.id_client = _client.id_compte
    WHERE id_vendeur = :id_vendeur
    ORDER BY date_commande DESC");

    $stmt->bindValue(":id_vendeur", $id_vendeur, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// renvoie la date d'une commande avec son id
function get_date_commande($id_commande)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT date_commande FROM _commande WHERE id_commande = :id_commande");
    $stmt->bindValue(":id_commande", $id_commande, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['date_commande'];
}

// renvoie le pseudo du client associé à une commande
function get_pseudo_commande($id_commande)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT pseudo 
    FROM _commande 
    INNER JOIN client ON id_client = id_compte
    WHERE id_commande = :id_commande");
    $stmt->bindValue(":id_commande", $id_commande, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['pseudo'];
}

// renvoie la liste des éléments de la commande par son id
// les éléments sont sous la forme :
//  [
//      "id_produit" => int, 
//      "nom_produit" => str, 
//      "quantite" => int, 
//      "prix" => float, 
//      "nom_vendeur" => str, 
//      "id_vendeur" => int
//  ]
function get_elements_commande($id_commande)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT id_produit, nom_produit, quantite, prix, id_vendeur FROM _elt_commande
    WHERE id_commande = :id_commande
    ORDER BY id_vendeur");
    $stmt->bindValue(":id_commande", $id_commande, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// renvoie la liste des éléments de la commande vendus par le vendeur
// les éléments sont sous la forme :
//  [
//      "id_produit" => int, 
//      "nom_produit" => str, 
//      "quantite" => int, 
//      "prix" => float, 
//      "tva" => float
//  ]
function get_elements_commande_vendeur($id_commande, $id_vendeur)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT _elt_commande.id_produit, nom_produit, _elt_commande.quantite, _elt_commande.prix, tva FROM _elt_commande 
    INNER JOIN _produit ON _elt_commande.id_produit = _produit.id_produit
    WHERE id_commande = :id_commande AND id_vendeur = :id_vendeur ORDER BY nom_produit");
    $stmt->bindValue(":id_commande", $id_commande, PDO::PARAM_INT);
    $stmt->bindValue(":id_vendeur", $id_vendeur, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// renvoie certaines infos précises sur le vendeur et le client d'une commande / d'un élément de commande:
// [
//      "raison_sociale" => str,
//      "id_adresse_vendeur" => int,
//      "nom_client" => str,
//      "prenom_client" => str,
//      "id_adresse_client" => int | null,
//      "date_commande" => date
// ]
function get_infos_commande($id_commande, $id_vendeur) {
    global $pdo;

    $infos = [];

    $stmt = $pdo->prepare("SELECT raison_sociale, id_adresse AS id_adresse_vendeur FROM _vendeur WHERE id_compte = :id_vendeur");
    $stmt->bindValue(":id_vendeur", $id_vendeur, PDO::PARAM_INT);
    $stmt->execute();
    $infos_vendeur = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($infos_vendeur !== false) {
        $infos += $infos_vendeur;
    }

    $stmt = $pdo->prepare("SELECT nom AS nom_client, prenom AS prenom_client, id_adresse_fac AS id_adresse_client, date_commande
    FROM _commande 
    INNER JOIN _client 
    ON _commande.id_client = _client.id_compte 
    WHERE id_commande = :id_commande");

    $stmt->bindValue(":id_commande", $id_commande, PDO::PARAM_INT);
    $stmt->execute();
    $infos_client = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($infos_client !== false) {
        $infos += $infos_client;
    }

    return $infos;
}

function connexion_delivraptor($fd, $id, $mdp)
{
    fwrite($fd, "1.$id.$mdp");
    $buffer = fread($fd, 200);

    $buffer = explode("=", $buffer);
    return trim($buffer[1]);
}

function create_colis($fd)
{
    fwrite($fd, "2");
    $buffer = fread($fd, 200);

    $buffer = explode("=", $buffer);
    return trim($buffer[1]);
}

function get_info_colis($fd, $bordereau)
{
    fwrite($fd, "3.$bordereau");
    $buffer = fread($fd, 200);

    $info = explode("\n", trim($buffer));
    if (count($info) > 1) {
        $etape = explode("=", $info[0])[1];
        $rendu = explode("=", $info[1])[1];
        $refus = explode("=", $info[2])[1];

        $info_colis = [
            "ETAPE" => $etape,
            "RENDU" => $rendu,
            "REFUS" => $refus,
            "ERROR" => "N/A"
        ];
    } else {
        $error = explode("=", $info[0])[1];
        echo "erreur : ".$error.";";
        $info_colis = [
            "ETAPE" => "N/A",
            "RENDU" => "N/A",
            "REFUS" => "N/A",
            "ERROR" => $error
        ];
    }

    return $info_colis;
}
function get_image_colis($fd, $bordereau)
{
    $fin = false;
    fwrite($fd, "4.$bordereau");
    $photo = '';
    $file = fopen(HOME_SITE . "ressources/colis/$bordereau.png", "w");
    $buffer = fread($fd, 6);

    if ($buffer === "PHOTO=") {

        while (!$fin) {
            $buffer = fread($fd, 4096);



            if ($pos = strpos($buffer, "#") !== false) {
                $buffer = substr($buffer, 0, -1);
                $fin = true;
            }

            fwrite($file, binaire_to_octet($buffer));

        }

    }
    //sinon recuperer numero de l'erreur
    else {
        $buffer = fread($fd, 1);
        switch ($buffer) {
            //cas erreur pas de colis
            case '3':
                $texte_img = "Colis inexistent";
                break;
            //cas d'erreur pas de photo
            case '4':
                $texte_img = "Photo inexistante";
                break;

            default:
                $texte_img = "Erreur";
                break;
        }
        fclose($file);
        return $texte_img;
    }

    fclose($file);
    return "";
}

function binaire_to_octet($binString)
{
    $result = '';
    $length = strlen($binString);
    for ($i = 0; $i < $length; $i += 8) {
        $byte = substr($binString, $i, 8);
        if (strlen($byte) < 8)
            break; // ignore le reste incomplet
        $result .= chr(bindec($byte));
    }
    return $result;
}

function connexion_socket($ip, $port)
{
    $fd = @fsockopen($ip, $port, $errno, $errstr);
    return $fd;
}

function deconnexion_socket($fd)
{
    if ($fd !== false) {
        fwrite($fd, "-1");
        fclose($fd);
    }
}

function livraison_info($bordereau)
{
    global $fd, $conn;

    if ($bordereau == null){
        return "Etape inconnue";
    }

    if ($conn == 0){
        return "Etape inconnue (problème serveur)";
    }

    if ($conn != "1") {
        return "Erreur serveur";
    }

    //recuperation des données du colis
    $info_colis = get_info_colis($fd, $bordereau);

    // $texte_img = "";
    //si le colis est rendu dans la boite au lettre et si l'image n'existe pas
    /*if ($info_colis["RENDU"] == "1" && !file_exists(HOME_SITE . "ressources/colis/$bordereau.png")) {

        //recuperation de l'image
        $texte_img = get_image_colis($fd, $bordereau);
    }*/

    if ($info_colis["ERROR"] != "N/A") {
        return "Colis introuvable";
    }

    $texte_refus = "";

    //affichage du refsu de colis
    switch ($info_colis["REFUS"]) {
        case '0':
            $texte_refus = "Colis endommagé";
            break;
        case '1':
            $texte_refus = "Ne correspond pas à la commande";
            break;
        case '2':
            $texte_refus = "En retard";
            break;
        case '3':
            $texte_refus = "Plus besoin du colis";
            break;
    }
    //affichage rendu du colis
    switch ($info_colis["RENDU"]) {
        case '0':
            return "Colis remis en main propre";
            
        case '1':
            return "Colis dans la boite au lettre";
            
        case '2':
            return "Colis refusé. cause : $texte_refus";
    }

    //affichage des etapes
    switch ($info_colis["ETAPE"]) {
        case "1":
            return "Colis en cours de traitement";
            
        case "2":
            return "Prise en charge du colis chez Alizon";
            
        case "3":
            return "Arrivée chez le transporteur";
            
        case "4":
            return "Départ vers la plateforme régionale";
            
        case "5":
            return "Arrivée sur la plateforme régionale";
            
        case "6":
            return "Départ vers le centre local";
            
        case "7":
            return "Arrivée au centre local";

        case "8":
            return "Départ pour la livraison finale";

        case "9":
            return "Livré";

        default:
            return "Colis en cours de livraison";

    }
}
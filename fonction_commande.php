<?php

function ajout_commande($id_commande, $liste_produits) {
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

function get_commandes($id_client) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM _commande WHERE id_client = :id_client ORDER BY date_commande DESC");
    $stmt->bindValue(":id_client", $id_client, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_commandes_vendeur($id_vendeur) {
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

function get_date_commande($id_commande) {
    global $pdo;

    $stmt =  $pdo->prepare("SELECT date_commande FROM _commande WHERE id_commande = :id_commande");
    $stmt->bindValue(":id_commande", $id_commande, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['date_commande'];
}

function get_pseudo_commande($id_commande) {
    global $pdo;

    $stmt =  $pdo->prepare("SELECT pseudo 
    FROM _commande 
    INNER JOIN client ON id_client = id_compte
    WHERE id_commande = :id_commande");
    $stmt->bindValue(":id_commande", $id_commande, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['pseudo'];
}

function get_elements_commande($id_commande) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT id_produit, nom_produit, quantite, prix, nom_vendeur FROM _elt_commande 
    WHERE id_commande = :id_commande
    ORDER BY nom_vendeur");
    $stmt->bindValue(":id_commande", $id_commande, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_elements_commande_vendeur($id_commande, $id_vendeur) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT _elt_commande.id_produit, nom_produit, _elt_commande.quantite, _elt_commande.prix FROM _elt_commande 
    INNER JOIN _produit ON _elt_commande.id_produit = _produit.id_produit
    WHERE id_commande = :id_commande AND id_vendeur = :id_vendeur ORDER BY nom_produit");
    $stmt->bindValue(":id_commande", $id_commande, PDO::PARAM_INT);
    $stmt->bindValue(":id_vendeur", $id_vendeur, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function connexion_delivraptor($fd,$id,$mdp){
    fwrite($fd,"1.$id.$mdp");
    $buffer =fread($fd,200);
    
    $buffer = explode("=",$buffer);
    return trim($buffer[1]);
}

function create_colis($fd){
    fwrite($fd,"2");
    $buffer = fread($fd,200);
    
    $buffer = explode("=",$buffer);
    return trim($buffer[1]);
}

function get_info_colis($fd,$bordereau){
    fwrite($fd,"3.$bordereau");
    $buffer = fread($fd,200);

    $info = explode("\n",trim($buffer));
    if (count($info)>1) {
        $etape = explode("=",$info[0])[1];
        $rendu = explode("=",$info[1])[1];
        $refus = explode("=",$info[2])[1];

        $info_colis = [
            "ETAPE" => $etape,
            "RENDU" => $rendu,
            "REFUS" => $refus,
            "ERROR" => "N/A"
        ];
    }else{
        $error = explode("=",$info[0])[1];
        
        $info_colis = [
            "ETAPE" => "N/A",
            "RENDU" => "N/A",
            "REFUS" => "N/A",
            "ERROR" => $error
        ];
    }
   
    return $info_colis;
}

function get_image_colis($fd,$bordereau){
    fwrite($fd,"4.$bordereau");
    $photo = '';
    while (!feof($fd)) {
        $buffer = fread($fd, 4096);
        
        if (($pos = strpos($buffer, "#")) != false) {
            $photo .= substr($buffer, 0, $pos);
            break;
        }
        $photo .= $buffer;
        
    }
    
    $ret = explode("=",$photo);
    
    return $ret[1];

}
function binaireEnOctets($binString) {
    $ret = '';
    $length = strlen($binString);
    for ($i = 0; $i < $length; $i += 8) {
        $byte = substr($binString, $i, 8);
        if (strlen($byte) < 8) break; // ignore le reste incomplet
        $ret .= chr(bindec($byte));
    }
    return $ret;
}

function connexion_socket($ip,$port){
    $fd =@fsockopen($ip,$port, $errno, $errstr);
    if ($fd === false) {
        echo "Connexion Delivraptor échouée ";
    }
    return $fd;
}
function deconnexion_socket($fd){
    fwrite($fd,"0");
    fclose($fd);
}
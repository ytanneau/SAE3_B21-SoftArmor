<?php
//fonction pour ajouter produit au panier
function ajouter_panier($id_produit,$id_client,$quantite){
    global $pdo;
    $requete = $pdo->prepare("SELECT * FROM _elt_panier WHERE id_produit =:id_produit AND id_client=:id_client");
    $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_INT);
    $requete->bindValue(':id_client', $id_client, PDO::PARAM_INT);
    $requete->execute();
    $est_dans_panier = $requete->fetch(PDO::FETCH_ASSOC);

    $qtt_panier =0;

    if($est_dans_panier){
        foreach ($est_dans_panier as $row) {
            $qtt_panier = $row;
            $est_entre=true;
        }   
    }
    

    if(!$est_dans_panier){
        $requete = $pdo->prepare("INSERT INTO _elt_panier VALUES(:id_produit,:id_client,:quantite)");
        $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_INT);
        $requete->bindValue(':id_client', $id_client, PDO::PARAM_INT);
        $requete->bindValue(':quantite', $quantite, PDO::PARAM_INT);
        $requete->execute();
        
    }else{
        $quantite+=$qtt_panier;
        $requete = $pdo->prepare("UPDATE _elt_panier SET quantite = :quantite  WHERE id_client=:id_client AND id_produit= :id_produit");
        $requete->bindValue(':id_produit', $id_produit, PDO::PARAM_INT);
        $requete->bindValue(':id_client', $id_client, PDO::PARAM_INT);
        $requete->bindValue(':quantite', $quantite, PDO::PARAM_INT);
        $requete->execute();
    }
    return $requete->fetch(PDO::FETCH_ASSOC);

}


function update_quantite($id_produit, $quantite, $id_client){
    global $pdo;

    if (!elt_dans_panier($id_produit, $id_client)) {
        return false;
    }

    $ancienne_quantite = quantite_elt_panier($id_produit, $id_client);


    if (str_contains($quantite, '+') or str_contains($quantite, '-')) {
        $nouvelle_quantite = $ancienne_quantite + (int) $quantite;
    }

    else {
        $nouvelle_quantite = (int) $quantite;
    }

    require_once "fonction_produit.php";

    $nouvelle_quantite = max([
        min([
            $nouvelle_quantite, 
            detail_produit($id_produit)['quantite']
            ]),
        0
        ]);

    // Si la nouvelle quantité est nulle, on retire du panier 
    if ($nouvelle_quantite == 0) {
        $requete = $pdo->prepare("DELETE FROM _elt_panier WHERE id_produit = :id_produit AND id_client = :id_client");
    
    } else {
        $requete = $pdo->prepare('UPDATE _elt_panier SET quantite = :quantite WHERE id_produit = :id_produit AND id_client = :id_client');
        $requete->bindValue(":quantite", $nouvelle_quantite, PDO::PARAM_INT);
    }

    $requete->bindValue(":id_produit", $id_produit, PDO::PARAM_INT);
    $requete->bindValue(":id_client", $id_client, PDO::PARAM_INT);
    $requete->execute();

    return true;
}

function elt_dans_panier($id_produit, $id_client) {
    global $pdo;

    $requete = $pdo->prepare('SELECT COUNT(1) AS nb FROM _elt_panier WHERE id_produit = :id_produit AND id_client = :id_client');
    $requete->bindValue(":id_produit", $id_produit, PDO::PARAM_INT);
    $requete->bindValue(":id_client", $id_client, PDO::PARAM_INT);
    $requete->execute();

    return $requete->fetch(PDO::FETCH_ASSOC)['nb'] == 1;
}

function quantite_elt_panier($id_produit, $id_client) {
    global $pdo;

    $requete = $pdo->prepare('SELECT quantite FROM _elt_panier WHERE id_produit = :id_produit AND id_client = :id_client');
    $requete->bindValue(":id_produit", $id_produit, PDO::PARAM_INT);
    $requete->bindValue(":id_client", $id_client, PDO::PARAM_INT);
    $requete->execute();

    return $requete->fetch(PDO::FETCH_ASSOC)['quantite'];
}
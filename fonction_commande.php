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
    
    $stmt = $pdo->prepare("SELECT * FROM _commande WHERE id_client = :id_client ORDER BY date_commande");
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
    ORDER BY date_commande");

    $stmt->bindValue(":id_vendeur", $id_vendeur, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
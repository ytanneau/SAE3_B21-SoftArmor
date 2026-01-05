<?php

function ajout_commande($id_commande, $liste_produits) {
    global $pdo;

    foreach ($liste_produits as $produit) {
        $stmt = $pdo->prepare("INSERT INTO _elt_commande (id_commande, id_produit, quantite, prix) VALUES (:id_commande, :id_produit, :quantite, :prix)");
        $stmt->bindValue(":id_commande", $id_commande, PDO::PARAM_INT);
        $stmt->bindValue(":id_produit", $produit["id_produit"], PDO::PARAM_INT);
        $stmt->bindValue(":quantite", $produit["quantite"], PDO::PARAM_INT);
        $stmt->bindValue(":prix", $produit["prix"], PDO::PARAM_INT);

        $stmt->execute();
    }
}
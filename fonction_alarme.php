<?php

    // renvoie tous les produits d'un vendeur où la quantité en stock est inférieure ou égale à la valeur mise en alerte
    // les produits sont sous la forme :
    //  [
    //      "id_vendeur" => int,
    //      "id_produit" => int,
    //      "nom_stock" => str,
    //      "quantite" => int
    //  ]
    function get_alarme($id_vendeur){
        global $pdo;
        $requete = $pdo->prepare("SELECT id_vendeur, id_produit, nom_stock, quantite FROM produit_alerte WHERE id_vendeur = :id_vendeur");
        
        $requete->bindValue(':id_vendeur', $id_vendeur, PDO::PARAM_INT);
        $requete->execute();

        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }


    // fonction view (HTML) qui affiche tous les produits en alerte d'un vendeur
    function affiche_alarme($id_vendeur){
        $data = get_alarme($id_vendeur);
        if($data != null){
        ?>
            <div class="alarme">
                <?php  ?>
                    <h3>Alerte :</h3>
                    <ul>
                    <?php foreach ($data as $key => $value) { ?>
                        <li>
                            <a href="../produit/?produit=<?=htmlentities($value['id_produit'])?>">
                                <?=htmlentities($value['nom_stock'])?> | il reste <?=htmlentities($value['quantite'])?>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        <?php
        } 
    }
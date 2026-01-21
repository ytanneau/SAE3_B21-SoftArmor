<?php

    function get_alarme($id_vendeur){
        global $pdo;
        $requete = $pdo->prepare("SELECT id_vendeur, id_produit, nom_stock, quantite FROM produit_alerte WHERE id_vendeur = :id_vendeur");
        
        $requete->bindValue(':id_vendeur', $id_vendeur, PDO::PARAM_INT);
        $requete->execute();

        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }



    function affiche_alarme($id_vendeur){
        $data = get_alarme($id_vendeur);

        ?>
            <div>
                <ul>
                <?php foreach ($data as $key => $value) { ?>
                    <li>
                        <a href="../produit/?produit=<?=htmlentities($data['id_produit'])?>">
                            <?=htmlentities($data['nom_stock'])?>
                            <div>
                                il reste <?=htmlentities($data['quantite'])?>
                            </div>
                        </a>
                    </li>
                <?php }?>
                </ul>
            </div>

        <?php
    }
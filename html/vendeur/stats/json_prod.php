<?php

        $sql= "SELECT * FROM stats_par_produit2 WHERE id_vendeur = :id_vendeur";
        $requete = $pdo->prepare($sql);
        $requete->bindValue(":id_vendeur", trim($$_SESSION['id_compte']), PDO::PARAM_INT);
        $requete->execute();
        $data= $requete->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($data);
?>
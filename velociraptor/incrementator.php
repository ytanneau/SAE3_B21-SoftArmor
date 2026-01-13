#!/usr/bin/php
<?php 
    //les raisons de refus
    //["colis endommagé","ne correspond pas a la commande","en retard","plus besoin du _colis"];
    require_once '../.config.php';
    global $pdo;

    //recuperer les colis
    $sql = "SELECT * FROM _colis";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $ret = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //recuperer le nombre de colis entre l'etape 2 et 4
    $sql = "SELECT COUNT(*) AS nombre FROM _colis WHERE etape > 1 AND etape < 5";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $nb = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]["nombre"];

    //boucle pour choisir comment evolue le colis
    foreach ($ret as $key => $tab) {
        //si il y a plus de 4 colis entre l'etape 2 et 4 on n'augmente pas l'etape 
        if($tab['etape'] == 1 and $nb>4){
            continue;
        }
        //on augmente l'etape de 1
        else if($tab['etape'] < 8){
            $etape=$tab['etape']+1;
            $sql = "UPDATE _colis SET etape= :etape where bordereau = :bordereau";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':etape' => $etape,':bordereau' => $tab['bordereau']]);
        }//derniere etape on choisi comment la livraison se termine
        else if ($tab['etape'] == 8){
            $etape=$tab['etape']+1; 
            $est_livre= rand(0,1);
            $sql = "UPDATE _colis SET etape= :etape where bordereau = :bordereau";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':etape' => $etape,':bordereau' => $tab['bordereau']]);
            //s'il il est pas livré choisir raison de refus
            if(!$est_livre){
                $raison_refus = rand(0,3);
                $sql = "UPDATE _colis SET raison_refus= :raison_refus where bordereau = :bordereau";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':raison_refus' => $raison_refus,':bordereau' => $tab['bordereau']]);
            }
            //sinon choisir si absent ou non
            else {
                $absent= rand(0,1);
                $sql = "UPDATE _colis SET absent= :absent where bordereau = :bordereau";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':absent' => $absent,':bordereau' => $tab['bordereau']]);
            }
        }
        
    }
    
?>
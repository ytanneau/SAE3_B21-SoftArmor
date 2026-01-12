#!/usr/bin/php
<?php 
    //["colis endommagé","ne correspond pas a la commande","en retard","plus besoin du _colis"];
    require_once '../.config.php';
    global $pdo;
    
    $sql = "SELECT * FROM _colis";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $ret = $stmt->fetchAll(PDO::FETCH_ASSOC);

     
    foreach ($ret as $key => $tab) {
        if($tab['etape'] < 8){
            $etape=$tab['etape']+1; 
            $sql = "UPDATE _colis SET etape= :etape where bordereau = :bordereau";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':etape' => $etape,':bordereau' => $tab['bordereau']]);
        }
        else if ($tab['etape'] == 8){
            $etape=$tab['etape']+1; 
            $est_livre= rand(0,1);
            $sql = "UPDATE _colis SET etape= :etape where bordereau = :bordereau";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':etape' => $etape,':bordereau' => $tab['bordereau']]);
            
            if(!$est_livre){
                $raison_refus = rand(0,3);
                $sql = "UPDATE _colis SET raison_refus= :raison_refus where bordereau = :bordereau";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':raison_refus' => $raison_refus,':bordereau' => $tab['bordereau']]);
            }
            else {
                $absent= rand(0,1);
                $sql = "UPDATE _colis SET absent= :absent where bordereau = :bordereau";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':absent' => $absent,':bordereau' => $tab['bordereau']]);
            }
        }
        
    }
    
?>
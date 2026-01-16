<?php
define('HOME_SITE', 'html/');

$fd = fsockopen("0.0.0.0",9000, $errno, $errstr);

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
        $chunk = fread($fd, 4096);
        
        if (($pos = strpos($chunk, "#")) !== false) {
            $photo .= substr($chunk, 0, $pos);
            break;
        }
        $photo .= $chunk;
        
    }
    
    $a = explode("=",$photo);
    
    return $a[1];

}
function binaireEnOctets($binString) {
    $result = '';
    $length = strlen($binString);
    for ($i = 0; $i < $length; $i += 8) {
        $byte = substr($binString, $i, 8);
        if (strlen($byte) < 8) break; // ignore le reste incomplet
        $result .= chr(bindec($byte));
    }
    return $result;
}


$conn = connexion_delivraptor($fd,"alizon","098f6bcd4621d373cade4e832627b4f6");
    if ($conn == "true"){
        $bordereau = "FR1002";
        
        $info_colis =get_info_colis($fd,$bordereau);
        $texte_img="";
        if ($info_colis["RENDU"] == "1") {
            $img = get_image_colis($fd,$bordereau);
            switch ($img) {
                case '3':
                    $texte_img="Colis inexistent";
                    break;
                    
                case '4':
                    $texte_img="Photo inexistante";
                    break;
                
                default:
                    $octets = binaireEnOctets($img);
                    $fich = file_put_contents(HOME_SITE . "ressources/colis/test.png",$octets);
                    break;
            }
        }   
    }
fclose($fd);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DELIVRAPTOR</title>
</head>
<body>
    <main>
        <?php

        if ($conn =="true" and $info_colis["ERROR"]=="N/A" and $texte_img =="") :?>

            
        
        <p>bordereau:<?php echo($bordereau) ; ?></p>
        
        
            
        <?php
        switch ($info_colis["REFUS"]) {
            case '0':
                $texte_refus = "colis endommagé";
                break;
            case '1':
                $texte_refus = "ne correspond pas à la commande";
                break;
            case '2':
                $texte_refus = "en retard";
                break;
            case '3':
                $texte_refus = "plus besoin du colis";
                break;
        }

        switch ($info_colis["RENDU"]) {
            case '0':
                $texte_rendu = "colis remis en main propre";
                break;
            case '1':
                $texte_rendu ="colis dans la boite au lettre";
                break;
            case '2':
                $texte_rendu ="colis refusé. cause : $texte_refus";
                break;
            
            default:
                $texte_rendu ="";
                break;
            }
            
            ?>
            <p><?php echo $texte_rendu ;?></p>
        
        <?php
            $livraison = "colis en cours de livraison";
            switch ($info_colis["ETAPE"]) {
                case "1":
                    $texte_etape = "Création d’un bordereau de livraison";
                    break;
                case "2":
                    $texte_etape = "Prise en charge du colis chez Alizon";
                    break;
                case "3":
                    $texte_etape = "Arrivée chez le transporteur";
                    break;
                case "4":
                    $texte_etape = "Départ vers la plateforme régionale";
                    break;
                case "5":
                    $texte_etape = "Arrivée sur la plateforme régionale";
                    break;
                case "6":
                    $texte_etape = "Départ vers le centre local";
                    break;
                case "7":
                    $texte_etape = "Arrivée au centre local";
                    break;
                case "8":
                    $texte_etape = "Départ pour la livraison finale";
                    break;
                case "9":
                    $texte_etape = "Fin de livraison";
                    $livraison = "";
                    break;
                
               
            }
            ?>
            <p><?php echo($livraison);?></p>
            <p><?php echo($texte_etape);?></p>
        <?php
        elseif ($conn == "false") :?>
        <p>Connexion refusé</p>
        <?php elseif ($conn =="true" and $info_colis["ERROR"]!="N/A") :?>
            <p>Erreur, le colis <?php echo $bordereau?> n'existe pas</p>
         <?php
        elseif  ($conn =="true" and $texte_img !="") :?>
        <p>Erreur, <?php echo $texte_img?></p>
        <?php endif; ?>
    </main>
</body>
</html>
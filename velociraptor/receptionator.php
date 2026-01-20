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
    $fin = false;
    fwrite($fd,"4.$bordereau");
    $photo = '';
    $file = fopen(HOME_SITE ."ressources/colis/$bordereau.png","w");
    $buffer = fread($fd, 6);

    if ($buffer === "PHOTO="){
    
        while (!$fin) {
            $buffer = fread($fd, 4096);

            

            if ($pos = strpos($buffer,"#") !== false) {
                $buffer = substr($buffer,0,-1); 
                $fin = true;
            }
            
            fwrite($file, binaire_to_octet($buffer));
            
        }
                
    }
    else{
        
        fclose($file);
        return false;
    }
    
    fclose($file);
    return true;

}

function binaire_to_octet($binString) {
    $result = '';
    $length = strlen($binString);
    for ($i = 0; $i < $length; $i += 8) {
        $byte = substr($binString, $i, 8);
        if (strlen($byte) < 8) break; // ignore le reste incomplet
        $result .= chr(bindec($byte));
    }
    return $result;
}

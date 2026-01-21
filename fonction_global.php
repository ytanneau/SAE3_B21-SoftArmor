<?php

function pset($value) {
    return isset($value) ? htmlentities($value) : "";
}

//fonction pour aficcher les textes trop fong avec des ...
function limiter_caracteres($texte, $limite) {
    $largeur = 0;
    $resultat = "";

    // tab des caractere en fonction de leur largeur
    $poids = [
        'large' => "WMQGOBD",
        'moyen' => "ACEFHJKLNPRSTUVXYZ234567890_",
        'etroit' => "abcdeghknopqrsuvwxyz-",
        'mince' => "ijlftI1'"
    ];

    foreach (mb_str_split($texte) as $char) {
        if (str_contains($poids['large'], $char)){
            $largeur += 2.4;
        }  
        elseif (str_contains($poids['moyen'], $char)){
            $largeur += 2.0;
        } 
        elseif (str_contains($poids['etroit'], $char)){
            $largeur += 1.6;
        } 
        elseif (str_contains($poids['mince'], $char)){
            $largeur += 1.0;
        } 
        else {
            $largeur += 1.8; 
        }

        if ($largeur > $limite){
            return $resultat . "...";
        } 

        $resultat .= $char;
    }

    return $resultat;
}


?>
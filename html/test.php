<?php 
    
    
    //fonction pour calculer et afficher les moyennes d'un produit
    function afficher_moyenne_note($moyenne){
        if($moyenne > 5 || $moyenne < 0){
            return null;
        }
        // code de iwan pour calculer et afficher les moyennes d'un produit en fonction de sa moyenne
        for ($i =1; $i <= floor($moyenne); $i++){
            ?> <img src="images/etoile_pleine.svg" alt="étoile pleine"><?php
        }
        if(fmod(floor($moyenne*2),2)){
            ?> <img src="images/etoile_demi.svg" alt="étoile à moitié pleine"> <?php 
        }
        for ($i =5; $i > round($moyenne); $i--){
            ?> <img src="images/etoile_vide.svg" alt="étoile vide"><?php
        }
    }
    
    echo "TEST NOTE NON VALIDE";
    echo "<br>";
    afficher_moyenne_note(6);
    echo "<br>";
    afficher_moyenne_note(-2);
    echo "<br>";
    echo "TEST NOTE 1 à 5";
    echo "<br>";
    for($i=0; $i < 6 ; $i++){
        afficher_moyenne_note($i);
        echo "<br>";
    }
    echo "TEST NOTE NOTE FLOAT";
    echo "<br>";
    for($j =0 ;$j<=5; $j = $j+0.01){
        afficher_moyenne_note($j);
        echo $j;
        echo "<br>";
    }
?>
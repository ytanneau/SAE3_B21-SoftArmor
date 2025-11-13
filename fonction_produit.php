<?php  
    
    //fonction pour calculer et afficher les moyennes d'un produit
    function afficher_moyenne_note($moyenne){
        if($moyenne > 5 || $moyenne < 0){
            return null;
        }
        // code de iwan pour calculer et afficher les moyennes d'un produit en fonction de sa moyenne
        for ($i =1; $i <= floor($moyenne); $i++){
            ?> <img src="../../image/etoile_pleine.svg" alt="étoile pleine"><?php
        }
        if(fmod(floor($moyenne*2),2)){
            ?> <img src="../../image/etoile_demi.svg" alt="étoile à moitié pleine"> <?php 
        }
        for ($i =5; $i > round($moyenne); $i--){
            ?> <img src="../../image/etoile_vide.svg" alt="étoile vide"><?php
        }
    }
?>
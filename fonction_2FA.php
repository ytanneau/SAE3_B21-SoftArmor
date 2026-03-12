<?php

require_once ".config.php";
require_once HOME_GIT . "vendor/autoload.php";
use OTPHP\TOTP;

// permet d'activer la double authentification sur un compte, en ajoutant la clef de la 2FA au compte dans la BDD
function activer_2FA($id_compte, $clef) {
    global $pdo;
    
    $requete = $pdo->prepare('UPDATE _compte SET clef = :clef WHERE id_compte = :id_compte');
    $requete->bindValue(":clef", $clef, PDO::PARAM_STR);
    $requete->bindValue(":id_compte", $id_compte, PDO::PARAM_INT);

    $requete->execute();
}

// permet de désactiver la double authentification sur un compte
function desactiver_2FA($id_compte) {
    global $pdo;

    $requete = $pdo->prepare("UPDATE _compte SET clef = NULL WHERE id_compte = :id_compte");
    $requete->bindValue(":id_compte", $id_compte, PDO::PARAM_INT);
    $requete->execute();
}

// fonction qui teste si l'utilisateur a activé la double authentification
// renvoie true s'il l'a activée
function a_2FA($id_compte) {
    global $pdo;

    $requete = $pdo->prepare("SELECT clef FROM _compte WHERE id_compte = :id_compte AND clef IS NOT NULL");
    $requete->bindValue(":id_compte", $id_compte, PDO::PARAM_INT);
    $requete->execute();
    return $requete->rowCount() == 1;
}

// renvoie la clef de double authentification liée à un compte (avec son id)
// cette clef permet de vérifier le code PIN de l'utilisateur ensuite
function get_clef_2FA($id_compte) {
    global $pdo;

    $requete = $pdo->prepare("SELECT clef FROM _compte WHERE id_compte = :id_compte");
    $requete->bindValue(":id_compte", $id_compte, PDO::PARAM_INT);
    $requete->execute();

    return $requete->fetch(PDO::FETCH_ASSOC)['clef'];
}

// permet de vérifier le code PIN envoyé par l'utilisateur
// vérifie le code PIN de l'instant T, et celui de 29 secondes avant, pour laisser le temps à l'utilisateur
// $otp : objet créé avec OTPHP\TOTP
function verify_2FA($otp, $codePIN) {        
    return $otp->verify($codePIN, leeway:29) || $otp->verify($codePIN);
}

// vérifie si le code PIN est un nombre à 5 chiffres
function check_code_PIN($codePIN) {
    $erreur = "";

    if (empty($codePIN)) {
        $erreur = "Veuillez remplir ce champ";
    } else if (!is_numeric($codePIN) || strlen($codePIN) != 6) {
        $erreur = "Format invalide";
    } 

    return $erreur;
}
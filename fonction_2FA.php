<?php


    require_once ".config.php";
    require_once HOME_GIT . "vendor/autoload.php";
    use OTPHP\TOTP;

    function activer_2FA($id_compte, $clef) {
        global $pdo;
        
        $requete = $pdo->prepare('UPDATE _compte SET clef = :clef WHERE id_compte = :id_compte');
        $requete->bindValue(":clef", $clef, PDO::PARAM_STR);
        $requete->bindValue(":id_compte", $id_compte, PDO::PARAM_INT);

        $requete->execute();
    }

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

    function get_clef_2FA($id_compte) {
        global $pdo;

        $requete = $pdo->prepare("SELECT clef FROM _compte WHERE id_compte = :id_compte");
        $requete->bindValue(":id_compte", $id_compte, PDO::PARAM_INT);
        $requete->execute();

        return $requete->fetch(PDO::FETCH_ASSOC)['clef'];
    }
    
    function verify_2FA($otp, $codePIN) {        
        return $otp->verify($codePIN, leeway:29) || $otp->verify($codePIN);
    }
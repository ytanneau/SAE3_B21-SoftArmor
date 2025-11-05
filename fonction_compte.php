<?php
    define("VIDE", "Champ est vide");
    define("DEPASSE","Dépassement de champ");
    define("FORMAT","Format invalide");
    define("EXISTE","Existe déjà");
    define("EXISTE_PAS","Existe pas");
    define("CORRESPOND_PAS","Ne correspond pas au mot de passe");

    define("TAILLE_RAISON_SOCIAL", 60);
    define("TAILLE_EMAIL", 80);
    define("TAILLE_ADRESSE", 120);
    define("TAILLE_MDP", 100);

    //print_r(hash_algos()); | verifier que algos est sur la machine
    //fonction qui renvoir le mot de passe cryper et saler
    function crypte_v1($mdp){
        return hash($algo = "xxh128",$data = $mdp);
    }

    //fonction qui renvoir le mot de passe cryper et saler
    function crypte_v2($mdp){
        return password_hash($password=$mdp, $algo=PASSWORD_BCRYPT);
    }
    
    //fonction qui permer de cree un compte vendeur
    function create_profile_vendeur($raisonSocial, $numSiret, $numCobrec, $email, $adresse, $codePostal, $mdp, $mdpc, $chemin){
        $raisonSocial = trim($raisonSocial);
        $numSiret = nettoyer_chaine(trim($numSiret));
        $numCobrec = nettoyer_chaine(trim($numCobrec));
        $email = trim($email);
        
        $adresse = trim($adresse);
        $codePostal = trim($codePostal);

        $mdp = trim($mdp);
        $mdpc = trim($mdpc);

        $res = true;
        if (check_raison_social_all($raisonSocial)
        && check_num_siret_all($numSiret) 
        && check_num_cobrec_all($numCobrec) 
        && check_email_all($email) 
        && check_adresse_all($adresse)
        && check_code_postal_all($codePostal)
        && check_create_MDP($mdp, $mdpc)) {

            //coucou fix2
            require_once($chemin . '.config.php');

            //echo "succes";
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $requete = $pdo->prepare("SELECT 1 FROM compte_actif WHERE email = :email");
            $requete->bindValue(':email', $email, PDO::PARAM_STR);
            $requete->execute();
            $resSQL = $requete->fetch(PDO::FETCH_ASSOC);
            print_r($resSQL);
            if ($resSQL == null){
                echo "succes";
            }
            else{
                $res['EM'] = EXISTE;
            }
        }
        else{
            $res = check_erreur_vendeur($raisonSocial, $numSiret, $numCobrec, $email, $adresse, $codePostal, $mdp, $mdpc);
        }
        return $res;
    }
    
//toute les fonction de verrification de champ

    //verifie la raison social
    function check_raison_social_all($raisonSocial){
        return ((!check_vide($raisonSocial)) && check_taille($raisonSocial, TAILLE_RAISON_SOCIAL) && check_raison_social($raisonSocial));
    }
    //verifie la forme de la raison social
    function check_raison_social($raisonSocial){
        return (preg_match("/^.{3,}$/", $raisonSocial) && preg_match("/(EI|EIRL|EURL|SASU|SARL|SAS|SNC|SA|SCA|SCS)$/",$raisonSocial));
    }


    //verifie le numero de siret
    function check_num_siret_all($numSiret){
        return ((!check_vide($numSiret)) && check_num_siret($numSiret));
    }
    //verifie la forme du numero de siret
    function check_num_siret($numSiret){
        return preg_match("/^[0-9]{14}$/", $numSiret);
    }


    //verifie le numero de la cobrec
    function check_num_cobrec_all($numCobrec){
        return ((!check_vide($numCobrec)) && check_num_cobrec($numCobrec));
    }
    //verifie la forme du numero de la cobrec
    function check_num_cobrec($numCobrec){
        return preg_match("/^[0-9]{15}$/", $numCobrec);
    }


    //verifie l'adresse
    function check_email_all($adresse){
        return ((!check_vide($adresse)) && check_taille($adresse, TAILLE_EMAIL) && check_email($adresse));
    }
    //verifie l'email
    function check_email($email){
        return preg_match("/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+.[a-zA-Z]{2,}$/",$email);
    }


    //verifie l'adresse
    function check_adresse_all($adresse){
        return ((!check_vide($adresse)) && check_taille($adresse, TAILLE_ADRESSE) && check_adresse($adresse));
    }
    //verifie la forme de l'adresse
    function check_adresse($adresse){
        return preg_match("/^([1-9][0-9]*(?:-[1-9][0-9]*)*)[\s,-]+(?:(bis|ter|qua)[\s,-]+)?([\w]+[\-\w]*)[\s,]+([-\w].+)$/", $adresse);
    }


    //verifie le code postal
    function check_code_postal_all($codePostal){
        return ((!check_vide($codePostal)) && check_code_postal($codePostal));
    }
    //verifie la forme du code postal
    function check_code_postal($codePostal){
        return preg_match("/^\d{5}$/", $codePostal);
    }


    //verifie le mot de passe
    function check_mot_de_passe($mdp){
        return (preg_match("/^.{12,}$/",$mdp));
    }

    //verifie le mot de passe
    function check_create_MDP($mdp, $mdpc){
        return (check_Mot_de_passe($mdp) && check_taille($mdp, TAILLE_MDP) && check_same_MDP($mdp, $mdpc));
    }

    //verifie le mot de passe
    function check_same_MDP($mdp1, $mdp2){
        return ($mdp1 === $mdp2);
    }

    //supprime les espaces, underscores et tirets
    function nettoyer_chaine($texte) {
        return str_replace([' ', '_', '-'], '', $texte);
    }

    //verifie le mot de passe crypte avec crypte_v2
    function check_crypte_MDP($mdp, $crypter){
        return password_verify($mdp, $crypter);
    }

    function check_vide($valeur){
        return (strlen($valeur) === 0);
    }

    function check_taille($valeur, $taille){
        return (strlen($valeur) <= $taille);
    }


//toute les fonction d'erreur

    //renvoit toute les erreur posible de champ
    function check_erreur_vendeur($raisonSocial, $numSiret, $numCobrec, $email, $adresse, $codePostal, $mdp, $mdpc){
        $res = [];

        //recherche l'erreur dans la raison social
        if (check_vide($raisonSocial)){
            $res['RS'] = VIDE;
        }
        else if (!check_taille($raisonSocial, TAILLE_RAISON_SOCIAL)){
            $res['RS'] = DEPASSE;
        }
        else if (!check_raison_social($raisonSocial)){
            $res['RS'] = FORMAT;
        }
        
        //recherche l'erreur dans le numero de siret
        if (check_vide($numSiret)){
            $res['NS'] = VIDE;
        }
        else if (!check_num_siret($numSiret) ){
            $res['NS'] = FORMAT;
        }

        //recherche l'erreur dans le numero de la COBREC
        if (check_vide($numCobrec)){
            $res['NC'] = VIDE;
        }
        else if (!check_num_cobrec($numCobrec) ){
            $res['NC'] = FORMAT;
        }

        //recherche l'erreur dans l'email
        if (check_vide($email)){
            $res['EM'] = VIDE;
        }
        else if (!check_taille($email, TAILLE_EMAIL)){
            $res['EM'] = DEPASSE;
        }
        else if (!check_email($email)){
            $res['EM'] = FORMAT;        
        }
        
        //recherche l'erreur dans l'adresse'
        if (check_vide($adresse)){
            $res['AD'] = VIDE;
        }
        else if (!check_taille($adresse, TAILLE_ADRESSE)){
            $res['AD'] = DEPASSE;
        }
        else if (!check_adresse($adresse)){
            $res['AD'] = FORMAT;
        }

        //recherche l'erreur dans code postal
        if (check_vide($codePostal)){
            $res['CP'] = VIDE;
        }
        else if (!check_code_postal($codePostal)){
            $res['CP'] = FORMAT;
        }

        //recherche l'erreur dans le mot de passe
        if (check_vide($mdp)){
            $res['MDP'] = VIDE;
        }
        else if (!check_taille($mdp, TAILLE_MDP)){
            $res['MDP'] = DEPASSE;
        }
        else if (!check_mot_de_passe($mdp)){
            $res['MDP'] = FORMAT;
        }

        //recherche l'erreur dans le mot de passe
        if (check_vide($mdpc)){
            $res['MDPC'] = VIDE;
        }
        if (!check_same_MDP($mdp, $mdpc)){
            $res['MDPC'] = CORRESPOND_PAS;
        }

        return $res;
    }
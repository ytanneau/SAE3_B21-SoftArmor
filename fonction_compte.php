<?php

    const VIDE = "Champ est vide";
    const DEPASSE = "Dépassement de champ";
    const FORMAT = "Format invalide";
    const EXISTE = "Existe déjà";
    const EXISTE_PAS = "Existe pas";
    const CORRESPOND_PAS = "Ne correspond pas au mot de passe";
    const CONNECT_PAS = "L'email ou mot de passe invalide";

    const TAILLE_NOM = 40;
    const TAILLE_RAISON_SOCIAL = 60;
    const TAILLE_EMAIL = 80;
    const TAILLE_ADRESSE = 120;
    const TAILLE_MDP = 100;
    
    require_once ".config.php";
    
    //print_r(hash_algos()); | verifier que algos est sur la machine
    //fonction qui renvoir le mot de passe cryper et saler
    function crypte_v1($mdp){
        return hash(algo: "xxh128",data: $mdp);
    }

    //fonction qui renvoir le mot de passe cryper et saler
    function crypte_v2($mdp){
        return password_hash(password:$mdp, algo:PASSWORD_BCRYPT);
    }
    
    //fonction qui permer de cree un compte vendeur
    function create_profile_vendeur($raisonSocial, $numSiret, $numCobrec, $email, $adresse, $codePostal, $mdp, $mdpc, $chemim){
        $raisonSocial = strtoupper(trim($raisonSocial));
        $numSiret = nettoyer_chaine(trim($numSiret));
        $numCobrec = nettoyer_chaine(trim($numCobrec));
        $email = trim($email);
        
        $adresse = trim($adresse);
        $codePostal = trim($codePostal);

        $mdp = trim($mdp);
        $mdpc = trim($mdpc);

        $res['correcte'] = true;
        if (check_raison_social_all($raisonSocial)
        && check_num_siret_all($numSiret) 
        && check_num_cobrec_all($numCobrec) 
        && check_email_all($email) 
        && check_adresse_all($adresse)
        && check_code_postal_all($codePostal)
        && check_create_MDP($mdp, $mdpc)) {

            require ($chemim . '.config.php');
            //print_r($resSQL);
            try{
                if (!sql_check_email($pdo, $email)){
                    echo "succes";

                    if (sql_check_cle($pdo, $numCobrec)){
                        echo "succes 2";

                        //sql_create_vendeur()
                    }
                    else{
                        $res['numero_cobrec'] = EXISTE_PAS;
                    }
                }
                else{
                    $res['email'] = EXISTE;
                }
            }
            catch(PDOException $e){
                $res['fatal'] = true;
            }
        }
        else{
            $res2 = check_erreur_vendeur($raisonSocial, $numSiret, $numCobrec, $email, $adresse, $codePostal, $mdp, $mdpc);

            if (isset($res2)) {
                $res = array_merge($res, $res2);
                $res['correcte'] = false;
            }
        }
        return $res;
    }

    //fonction qui permer de cree un compte client
    function create_profile_client($email, $nom, $prenom, $pseudo, $date_naiss, $mdp, $mdpc){
        $nom = strtoupper(trim($nom));
        $prenom = trim($prenom);
        $pseudo = trim($pseudo);
        $email = trim($email);

        $mdp = trim($mdp);
        $mdpc = trim($mdpc);

        $res["correcte"] = true;
        if (check_nom($nom)
        && check_nom($prenom) 
        && check_nom($pseudo) 
        && check_date_passee($date_naiss)
        && check_create_MDP($mdp, $mdpc)) {

            global $pdo;
            
            //print_r($resSQL);
            try{
                if (!sql_check_email($pdo, $email)){
                    echo "succes";

                    if (sql_create_client($pdo, $nom, $prenom, $pseudo, $email, $date_naiss, $mdp)){
                        echo "succes 2";
                    }
                    else{
                        // changer l'erreur $res['CR'] = EXISTE_PAS;
                        $res['correcte'] = false;
                    }
                    
                }
                else{
                    $res['email'] = EXISTE;
                    $res['correcte'] = false;
                }
            }
            catch(PDOException $e){
                $res['fatal'] = true;
                $res['correcte'] = false;
            }
        }
        else{
            $res['correcte'] = false;
            $res2 = check_erreur_client($nom, $prenom, $pseudo, $email, $date_naiss, $mdp, $mdpc);
            if ($res2) {
                $res = array_merge($res, $res2);
            }
        }
        return $res;
    }
    
    function connect_compte($email, $mdp, $typeCompte, $chemin){
        $email = trim($email);
        $mdp = trim($mdp);

        $res['correcte'] = true;
        if (check_email_all($email) 
        && check_MDP($mdp)) {

            require ($chemim . '.config.php');
            

            //print_r($resSQL);
            try{
                $resSQL = sql_email_compte($pdo, $email, $typeCompte);
                if ($resSQL != null){
                    echo "succes";

                    if (check_crypte_MDP($mdp, $resSQL['mdp'])){
                        echo "succes 2";

                        $_SESSION['logged_in'] = true;
                        $_SESSION['id_compte'] = $resSQL['id_compte'];
                        $_SESSION['email'] = $email;
                        if ($typeCompte == 'vendeur'){
                            $_SESSION['raison_sociale'] = $resSQL['raison_sociale'];
                        }
                        else{
                            $_SESSION['pseudo'] = $resSQL['pseudo'];
                        }

                        return $res;
                    }
                    else{
                        $res['erreur'] = CONNECT_PAS;
                        $res['correcte'] = false;
                    }
                }
                else{
                    $res['erreur'] = CONNECT_PAS;
                    $res['correcte'] = false;
                }
            }
            catch(PDOException $e){
                $res['fatal'] = true;
                $res['correcte'] = false;
            }
        } else {
            $res2 = check_erreur_vendeur($raisonSocial, $numSiret, $numCobrec, $email, $adresse, $codePostal, $mdp, $mdpc);

            if (isset($res2)) {
                $res = array_merge($res, $res2);
                $res['correcte'] = false;
            }
        }

        require ($chemin . '.config.php');
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

    function check_MDP($mdp){
        return (check_Mot_de_passe($mdp) && check_taille($mdp, TAILLE_MDP));
    }

    //verifie le mot de passe
    function check_same_MDP($mdp1, $mdp2){
        return ($mdp1 === $mdp2);
    }

    // verifie un nom (nom, prénom ou pseudo)
    function check_nom($nom) {
        return (!check_vide($nom) && check_taille($nom, TAILLE_NOM));
    }

    // verifie que la date est passée
    function check_date_passee($date) {
        return (strtotime("1900-01-01") < strtotime($date) && strtotime($date) < time());
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
            $res['raison_sociale'] = VIDE;
        }
        else if (!check_taille($raisonSocial, TAILLE_RAISON_SOCIAL)){
            $res['raison_sociale'] = DEPASSE;
        }
        else if (!check_raison_social($raisonSocial)){
            $res['raison_sociale'] = FORMAT;
        }
        
        //recherche l'erreur dans le numero de siret
        if (check_vide($numSiret)){
            $res['numero_siret'] = VIDE;
        }
        else if (!check_num_siret($numSiret) ){
            $res['numero_siret'] = FORMAT;
        }

        //recherche l'erreur dans le numero de la COBREC
        if (check_vide($numCobrec)){
            $res['numero_cobrec'] = VIDE;
        }
        else if (!check_num_cobrec($numCobrec) ){
            $res['numero_cobrec'] = FORMAT;
        }

        //recherche l'erreur dans l'email
        if (check_vide($email)){
            $res['email'] = VIDE;
        }
        else if (!check_taille($email, TAILLE_EMAIL)){
            $res['email'] = DEPASSE;
        }
        else if (!check_email($email)){
            $res['email'] = FORMAT;        
        }

        
        //recherche l'erreur dans l'adresse'
        if (check_vide($adresse)){
            $res['adresse'] = VIDE;
        }
        else if (!check_taille($adresse, TAILLE_ADRESSE)){
            $res['adresse'] = DEPASSE;
        }
        else if (!check_adresse($adresse)){
            $res['adresse'] = FORMAT;
        }

        //recherche l'erreur dans code postal
        if (check_vide($codePostal)){
            $res['code_postal'] = VIDE;
        }
        else if (!check_code_postal($codePostal)){
            $res['code_postal'] = FORMAT;
        }

        //recherche l'erreur dans le mot de passe
        if (check_vide($mdp)){
            $res['mdp'] = VIDE;
        }
        else if (!check_taille($mdp, TAILLE_MDP)){
            $res['mdp'] = DEPASSE;
        }
        else if (!check_mot_de_passe($mdp)){
            $res['mdp'] = FORMAT;
        }

        //recherche l'erreur dans le mot de passe
        if (check_vide($mdpc)){
            $res['mdpc'] = VIDE;
        }
        else if (!check_same_MDP($mdp, $mdpc)){
            $res['mdpc'] = CORRESPOND_PAS;
        }

        return $res;
    }

    // renvoit toutes les erreurs possibles de champ pour inscription client
    function check_erreur_client($nom, $prenom, $pseudo, $email, $date_naiss, $mdp, $mdpc){
        $res = [];

        // erreur champ nom
        if (check_vide($nom)){
            $res['nom'] = VIDE;
        }
        else if (!check_taille($nom, TAILLE_NOM)){
            $res['nom'] = DEPASSE;
        }
        
        // erreur champ prenom
        if (check_vide($prenom)){
            $res['prenom'] = VIDE;
        }
        else if (!check_taille($prenom, TAILLE_NOM)){
            $res['prenom'] = DEPASSE;
        }

        // erreur champ pseudo
        if (check_vide($pseudo)){
            $res['pseudo'] = VIDE;
        }
        else if (!check_taille($pseudo, TAILLE_NOM)){
            $res['pseudo'] = DEPASSE;
        }

        //recherche l'erreur dans l'email
        if (check_vide($email)){
            $res['email'] = VIDE;
        }
        else if (!check_taille($email, TAILLE_EMAIL)){
            $res['email'] = DEPASSE;
        }
        else if (!check_email($email)){
            $res['email'] = FORMAT; 
        }
        
        // erreur champ date naissance
        if (check_vide($date_naiss)) {
            $res['date_naiss'] = VIDE;
        }
        else if (!check_date_passee($date_naiss)){
            $res['date_naiss'] = FORMAT;  
        }

        //recherche l'erreur dans le mot de passe
        if (check_vide($mdp)){
            $res['mdp'] = VIDE;
        }
        else if (!check_taille($mdp, TAILLE_MDP)){
            $res['mdp'] = DEPASSE;
        }
        else if (!check_mot_de_passe($mdp)){
            $res['mdp'] = FORMAT;
        }

        //recherche l'erreur dans le mot de passe
        if (check_vide($mdpc)){
            $res['mdpc'] = VIDE;
        }
        else if (!check_same_MDP($mdp, $mdpc)){
            $res['mdpc'] = CORRESPOND_PAS;
        }

        return $res;
    }



//fonctuion pour la base de donnée

    //verifie la présence d'un email
    //return 1 si existe, 0 si absent
    function sql_check_email($pdo, $email){
        try{
            $requete = $pdo->prepare("SELECT 1 FROM compte_actif WHERE email = :email");
            $requete->bindValue(':email', $email, PDO::PARAM_STR);
            $requete->execute();
            return ($requete->fetch(PDO::FETCH_ASSOC) != null);
        }
        catch (PDOException $e) {
            $fichierLog = __DIR__ . "/erreurs.log";
            $date = date("Y-m-d H:i:s");
            file_put_contents($fichierLog, "[$date] Failed SQL request : check_email()\n", FILE_APPEND);
            throw $e;
        }
    }

    //return 1 si existe, 0 si absent
    function sql_email_compte($pdo, $email, $typecompte){
        try{
            if ($typecompte == 'vendeur'){
                $requete = $pdo->prepare("SELECT * FROM compte_vendeur WHERE email = :email");
            }
            else{
                $requete = $pdo->prepare("SELECT * FROM compte_client WHERE email = :email");
            }
            $requete->bindValue(':email', $email, PDO::PARAM_STR);
            $requete->execute();
            return ($requete->fetch(PDO::FETCH_ASSOC) != null);
        }
        catch (PDOException $e) {
            $fichierLog = __DIR__ . "/erreurs.log";
            $date = date("Y-m-d H:i:s");
            file_put_contents($fichierLog, "[$date] Failed SQL request : check_email()\n", FILE_APPEND);
            throw $e;
        }
    }

    //verifie la présence de la cle de la cobrec
    //return 1 si existe, 0 si absent
    function sql_check_cle($pdo, $cle){
        try{
            $requete = $pdo->prepare("SELECT 1 FROM _cle_vendeur WHERE cle_cobrec = :cle");
            $requete->bindValue(':cle', $cle, PDO::PARAM_STR);
            $requete->execute();
            return ($requete->fetch(PDO::FETCH_ASSOC) != null);
        }
        catch (PDOException $e) {
            $fichierLog = __DIR__ . "/erreurs.log";
            $date = date("Y-m-d H:i:s");
            file_put_contents($fichierLog, "[$date] Failed SQL request : check_cle()", FILE_APPEND);
            throw $e;
        }
    }

    //
    //
    function sql_create_vendeur(){
        try{
            $requete = $pdo->prepare("SELECT 1 FROM compte_actif WHERE email = :email");
            $requete->bindValue(':email', $email, PDO::PARAM_STR);
            $requete->execute();
            return $requete->fetch(PDO::FETCH_ASSOC);
        }
        catch (PDOException $e) {
            $fichierLog = __DIR__ . "/erreurs.log";
            $date = date("Y-m-d H:i:s");
            file_put_contents($fichierLog, "[$date] Failed SQL request : sql_create_vendeur()\n", FILE_APPEND);
            throw $e; // lance une erreur que la fonction appelante catchera
        }
    }

    function sql_create_client($pdo, $nom, $prenom, $pseudo, $email, $date_naiss, $mdp) {
        try{
            $requete = $pdo->prepare("INSERT INTO _compte (email, mdp) VALUES (:email, :mdp)");
            $requete->bindValue(':email', $email, PDO::PARAM_STR);
            $requete->bindValue(':mdp', $mdp, PDO::PARAM_STR);
            $requete->execute();
            
            $requete = $pdo->prepare("SELECT id_compte FROM _compte WHERE email = :email");
            $requete->bindValue(':email', $email);
            $requete->execute();
            $id_compte = $requete->fetch(PDO::FETCH_ASSOC)[0];

            $requete = $pdo->prepare("INSERT INTO _client (id_compte, pseudo, nom, prenom, date_naissance) VALUES (:id_compte, :pseudo, :nom, :prenom, :date_naissance)");
            $requete->bindValue(':id_compte', $id_compte, PDO::PARAM_STR);
            $requete->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
            $requete->bindValue(':nom', $nom, PDO::PARAM_STR);
            $requete->bindValue(':prenom', $prenom, PDO::PARAM_STR);
            $requete->bindValue(':date_naissance', $date_naiss, PDO::PARAM_STR);
            $requete->execute();


            return $requete->fetch(PDO::FETCH_ASSOC);
        }
        catch (PDOException $e) {
            $fichierLog = __DIR__ . "/erreurs.log";
            $date = date("Y-m-d H:i:s");
            file_put_contents($fichierLog, "[$date] Failed SQL request : create_vendeur()\n", FILE_APPEND);
            throw $e; // lance une erreur que la fonction appelante catchera
        }
    }


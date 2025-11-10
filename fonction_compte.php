<?php

    const VIDE = "Champ est vide";
    const DEPASSE = "Dépassement de champ";
    const FORMAT = "Format invalide";
    const EXISTE = "Existe déjà";
    const EXISTE_PAS = "Existe pas";
    const CORRESPOND_PAS = "Ne correspond pas au mot de passe";
    const CONNECT_PAS = "L'email ou mot de passe invalide";

    const TAILLE_NOM = 40;
    const TAILLE_RAISON_SOCIALE = 60;
    const TAILLE_EMAIL = 80;
    const TAILLE_ADRESSE = 120;
    const TAILLE_MDP = 100;
    
    require_once ".config.php";
    
    // Fonction qui renvoie le mot de passe crypté et salé
    function crypte_v1($mdp){
        return hash(algo: "xxh128",data: $mdp);
    }

    // Fonction qui renvoie le mot de passe crypté et salé
    function crypte_v2($mdp){
        return password_hash(password:$mdp, algo:PASSWORD_BCRYPT);
    }
    
    // Fonction qui permet de créer un compte vendeur
    function create_profile_vendeur($raisonSocial, $numSiret, $numCobrec, $email, $adresse, $compAdresse, $codePostal, $mdp, $mdpc, $chemin){
        global $pdo;
        
        $raisonSocial = strtoupper(trim($raisonSocial));
        $numSiret = nettoyer_chaine(trim($numSiret));
        $numCobrec = nettoyer_chaine(trim($numCobrec));
        $email = trim($email);
        
        $adresse = trim($adresse);
        $compAdresse = trim($compAdresse);
        $codePostal = trim($codePostal);

        $mdp = trim($mdp);
        $mdpc = trim($mdpc);

        $res['correcte'] = true;
        if (check_raison_sociale_all($raisonSocial)
        && check_num_siret_all($numSiret) 
        && check_num_cobrec_all($numCobrec) 
        && check_email_all($email) 
        && check_adresse_all($adresse)
        && check_code_postal_all($codePostal)
        && check_create_MDP($mdp, $mdpc)) {

            try{
                if (!sql_check_email($pdo, $email)){

                    if (sql_check_cle($pdo, $numCobrec)){
                        sql_create_vendeur($pdo, $raisonSocial, $numSiret, $email, $adresse, $compAdresse, $codePostal, $mdp);
                    }
                    else{
                        $res['connect'] = CONNECT_PAS;
                        $res['correcte'] = false;
                    }
                }
                else{
                    $res['connect'] = CONNECT_PAS;
                    $res['correcte'] = false;
                }
            }
            catch(PDOException $e){
                $res['fatal'] = true;
                $res['correcte'] = false;
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

    // Fonction qui permet de créer un compte client
    function create_profile_client($email, $nom, $prenom, $pseudo, $date_naiss, $mdp, $mdpc){
        $nom = strtoupper(trim($nom));
        $prenom = trim($prenom);
        $pseudo = trim($pseudo);
        $email = trim($email);

        $mdp = trim($mdp);
        $mdpc = trim($mdpc);

        // Pas d'erreur initialement
        $res["correcte"] = true;

        // Si toutes les informations sont correctes
        if (check_nom($nom)
        && check_nom($prenom) 
        && check_nom($pseudo) 
        && check_date_passee($date_naiss)
        && check_create_MDP($mdp, $mdpc)) {

            global $pdo;
            
            try {
                if (!sql_check_email($pdo, $email)){

                    if (sql_create_client($pdo, $nom, $prenom, $pseudo, $email, $date_naiss, $mdp)){
                    } else {
                        // changer l'erreur $res['CR'] = EXISTE_PAS;
                        $res['correcte'] = false;
                    }
                    
                } else {
                    $res['email'] = EXISTE;
                    $res['correcte'] = false;
                }
            } catch(PDOException $e) {
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
    

    // Fonction pour se connecter à un compte
    function connect_compte($email, $mdp, $typeCompte, $chemin){
        global $pdo;

        $email = trim($email);
        $mdp = trim($mdp);

        $res['correcte'] = true;

        echo "Test 1";

        if (check_email_all($email) && check_mot_de_passe_all($mdp)) {

            echo "Test 2";
            
            try {
                $resSQL = sql_email_compte($pdo, $email, $typeCompte);

                if ($resSQL != null) {
                    //echo "succes";

                    echo "Test 3";

                    if (check_crypte_MDP($mdp, $resSQL['mdp'])){
                        //echo "succes 2";

                        
                        session_start();

                        $_SESSION['logged_in'] = true;
                        $_SESSION['id_compte'] = $resSQL['id_compte'];
                        $_SESSION['email'] = $email;

                        if ($typeCompte == 'vendeur'){
                            $_SESSION['raison_sociale'] = $resSQL['raison_sociale'];
                        } else {
                            $_SESSION['pseudo'] = $resSQL['pseudo'];
                        }

                        return $res;
                    }
                    else {
                        $res['erreur'] = CONNECT_PAS;
                        $res['correcte'] = false;
                    }
                }
                else {
                    $res['erreur'] = CONNECT_PAS;
                    $res['correcte'] = false;
                }
            } catch(PDOException $e) {
                $res['fatal'] = true;
                $res['correcte'] = false;
            }
        } else {
            return check_erreur_connection($email, $mdp);
        }
    }


// +---------------------------------------+
// |  FONCTIONS DE VÉRIFICATION DE CHAMPS  |
// +---------------------------------------+

    // Vérifie la raison sociale (non vide, bonne taille, bon format)
    function check_raison_sociale_all($raisonSociale){
        return ((!check_vide($raisonSociale)) && check_taille($raisonSociale, TAILLE_RAISON_SOCIALE) && check_raison_sociale($raisonSociale));
    }

    // Vérifie le format de la raison sociale
    function check_raison_sociale($raisonSociale){
        return (preg_match("/^.{3,}$/", $raisonSociale) && preg_match("/(EI|EIRL|EURL|SASU|SARL|SAS|SNC|SA|SCA|SCS)$/",$raisonSociale));
    }


    // Vérifie le numéro de SIRET (non vide, bon format)
    function check_num_siret_all($numSiret){
        return ((!check_vide($numSiret)) && check_num_siret($numSiret));
    }

    // Vérifie le format du numéro de SIRET
    function check_num_siret($numSiret){
        return preg_match("/^[0-9]{14}$/", $numSiret);
    }


    // Vérifie le numéro de la COBREC (non vide, bon format)
    function check_num_cobrec_all($numCobrec){
        return ((!check_vide($numCobrec)) && check_num_cobrec($numCobrec));
    }

    // Vérifie le format du numéro de la COBREC
    function check_num_cobrec($numCobrec){
        return preg_match("/^[0-9]{15}$/", $numCobrec);
    }

    // Vérifie l'e-mail (non vide, bonne taille, bon format)
    function check_email_all($adresse){
        return ((!check_vide($adresse)) && check_taille($adresse, TAILLE_EMAIL) && check_email($adresse));
    }

    // Vérifie le format de l'e-mail
    function check_email($email){
        return preg_match("/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+.[a-zA-Z]{2,}$/",$email);
    }


    // Vérifie l'adresse (non vide, bonne taille, bon format)
    function check_adresse_all($adresse){
        return ((!check_vide($adresse)) && check_taille($adresse, TAILLE_ADRESSE) && check_adresse($adresse));
    }

    // Vérifie le format de l'adresse
    function check_adresse($adresse){
        return preg_match("/^([1-9][0-9]*(?:-[1-9][0-9]*)*)[\s,-]+(?:(bis|ter|qua)[\s,-]+)?([\w]+[\-\w]*)[\s,]+([-\w].+)$/", $adresse);
    }


    // Vérifie le code postal (non vide, bon format)
    function check_code_postal_all($codePostal){
        return ((!check_vide($codePostal)) && check_code_postal($codePostal));
    }
    // Vérifie le format du code postal
    function check_code_postal($codePostal){
        return preg_match("/^\d{5}$/", $codePostal);
    }

    // Vérifie le mot de passe (bon format, bonne taille)
    function check_mot_de_passe_all($mdp){
        echo ("Format : " . check_mot_de_passe($mdp));
        echo ("Taille : " . check_taille($mdp, TAILLE_MDP));
        return (check_mot_de_passe($mdp) && check_taille($mdp, TAILLE_MDP));
    }

    // Vérifie le format du mot de passe
    function check_mot_de_passe($mdp){
        return (preg_match("/^.{12,}$/",$mdp));
    }

    // Vérifie l'égalité du MDP et de la confirmation du MDP
    function check_create_MDP($mdp, $mdpc){
        return (check_Mot_de_passe($mdp) && check_taille($mdp, TAILLE_MDP) && ($mdp === $mdpc));
    }

    // Vérifie un nom/prénom/pseudo (non vide, bonne taille)
    function check_nom($nom) {
        return (!check_vide($nom) && check_taille($nom, TAILLE_NOM));
    }

    // Vérifie que la date est passée
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


// +----------------------+
// |  FONCTIONS D'ERREUR  |
// +----------------------+

    //renvoit toute les erreur posible de champ
    function check_erreur_vendeur($raisonSociale, $numSiret, $numCobrec, $email, $adresse, $codePostal, $mdp, $mdpc){
        $res = [];

        //recherche l'erreur dans la raison social
        if (check_vide($raisonSociale)){
            $res['raison_sociale'] = VIDE;
        }
        else if (!check_taille($raisonSociale, TAILLE_RAISON_SOCIALE)){
            $res['raison_sociale'] = DEPASSE;
        }
        else if (!check_raison_sociale($raisonSociale)){
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
        else if ($mdp !== $mdpc){
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
        else if ($mdp !== $mdpc) {
            $res['mdpc'] = CORRESPOND_PAS;
        }

        return $res;
    }

    function check_erreur_connection($email, $mdp){

        //recherche dans l'email
        if (check_vide($email)){
            $res['email'] = VIDE;
        }
        else if (!check_taille($email, TAILLE_EMAIL)){
            $res['email'] = DEPASSE;
        }
        else if (!check_email($email)){
            $res['email'] = FORMAT; 
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

    // Return un e-mail et MDP hashé si le compte existe, ou null sinon (OU erreur)
    function sql_email_compte($pdo, $email, $typecompte){
        try {
            if ($typecompte == 'vendeur') {
                $requete = $pdo->prepare("SELECT * FROM compte_vendeur WHERE email = :email");
            } else {
                $requete = $pdo->prepare("SELECT * FROM compte_client WHERE email = :email");
            }
            $requete->bindValue(':email', $email, PDO::PARAM_STR);
            $requete->execute();
            return $requete->fetch(PDO::FETCH_ASSOC);
        }
        catch (PDOException $e) {
            $fichierLog = __DIR__ . "/erreurs.log";
            $date = date("Y-m-d H:i:s");
            //file_put_contents($fichierLog, "[$date] Failed SQL request : check_email()\n", FILE_APPEND);
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

    // EN COURS DE CRÉATION
    function sql_create_client($pdo, $nom, $prenom, $pseudo, $email, $date_naiss, $mdp) {
        try {
            $requete = $pdo->prepare("INSERT INTO _compte (email, mdp) VALUES (:email, :mdp)");
            $requete->bindValue(':email', $email, PDO::PARAM_STR);
            $requete->bindValue(':mdp', crypte_v2($mdp), PDO::PARAM_STR);
            $requete->execute();
            
            $requete = $pdo->prepare("SELECT id_compte FROM _compte WHERE email = :email");
            $requete->bindValue(':email', $email);
            $requete->execute();
            $id_compte = $requete->fetch(PDO::FETCH_ASSOC)['id_compte'];

            $requete = $pdo->prepare("INSERT INTO _client (id_compte, pseudo, nom, prenom, date_naissance) VALUES (:id_compte, :pseudo, :nom, :prenom, :date_naissance)");
            $requete->bindValue(':id_compte', $id_compte, PDO::PARAM_STR);
            $requete->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
            $requete->bindValue(':nom', $nom, PDO::PARAM_STR);
            $requete->bindValue(':prenom', $prenom, PDO::PARAM_STR);
            $requete->bindValue(':date_naissance', $date_naiss, PDO::PARAM_STR);
            $requete->execute();

            return $requete->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $fichierLog = __DIR__ . "/erreurs.log";
            $date = date("Y-m-d H:i:s");
            file_put_contents($fichierLog, "[$date] Failed SQL request : create_vendeur()\n", FILE_APPEND);
            throw $e; // lance une erreur que la fonction appelante catchera
        }
    }

    function sql_create_vendeur($pdo, $raisonSocial, $numSiret, $email, $adresse, $compAdresse, $codePostal, $mdp) {
        try {
            echo "test succes 0";

            $requete = $pdo->prepare("INSERT INTO _compte (email, mdp) VALUES (:email, :mdp)");
            $requete->bindValue(':email', $email, PDO::PARAM_STR);
            $requete->bindValue(':mdp', crypte_v2($mdp), PDO::PARAM_STR);
            $requete->execute();

            echo "test succes 1";
            
            $requete = $pdo->prepare("SELECT id_compte FROM _compte WHERE email = :email");
            $requete->bindValue(':email', $email);
            $requete->execute();
            $id_compte = $requete->fetch(PDO::FETCH_ASSOC)['id_compte'];

            echo "test succes 2";

            $requete = $pdo->prepare("INSERT INTO _adresse (adresse, complement_adresse, code_postal) VALUES (:adresse, :comp_adresse, :code_postal)");
            $requete->bindValue(':adresse', $adresse, PDO::PARAM_STR);
            $requete->bindValue(':comp_adresse', $compAdresse, PDO::PARAM_STR);
            $requete->bindValue(':code_postal', $codePostal, PDO::PARAM_STR);
            $requete->execute();

            echo "test succes 3";

            $requete = $pdo->prepare("SELECT id_adresse FROM _adresse WHERE adresse = :adresse");
            $requete->bindValue(':adresse', $adresse);
            $requete->execute();
            $id_adresse = $requete->fetch(PDO::FETCH_ASSOC)['id_adresse'];

            echo "test succes 4";

            $requete = $pdo->prepare("INSERT INTO _vendeur (id_compte, raison_sociale, num_siret, id_adresse) VALUES (:id_compte, :raison_social, :numero_siret, :adresse)");
            echo "test succes 4.1";
            $requete->bindValue(':id_compte', $id_compte, PDO::PARAM_STR);
            echo "test succes 4.2";
            $requete->bindValue(':raison_social', $raisonSocial, PDO::PARAM_STR);
            echo "test succes 4.3";
            $requete->bindValue(':numero_siret', $numSiret, PDO::PARAM_STR);
            echo "test succes 4.4";
            $requete->bindValue(':adresse', $id_adresse, PDO::PARAM_STR);
            echo "test succes 4.5";
            $requete->execute();

            echo "test succes 5";

            return $requete->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $fichierLog = __DIR__ . "/erreurs.log";
            $date = date("Y-m-d H:i:s");
            file_put_contents($fichierLog, "[$date] Failed SQL request : create_vendeur()\n", FILE_APPEND);
            echo $e;
            throw $e; // lance une erreur que la fonction appelante catchera
        }
    }
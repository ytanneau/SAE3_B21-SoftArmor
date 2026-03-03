<?php

    const VIDE = "Veuillez renseigner ce champ";
    const DEPASSE = "Dépassement de champ";
    const FORMAT = "Le format est invalide";
    const EXISTE = "existe déjà";
    const EXISTE_PAS = "n'existe pas";
    const CORRESPOND_PAS = "Les deux mots de passe ne correspondent pas";
    const CONNECTE_PAS = "L'email ou le mot de passe est incorrect";

    const TAILLE_NOM = 40;
    const TAILLE_RAISON_SOCIALE = 60;
    const TAILLE_EMAIL = 80;
    const TAILLE_ADRESSE = 100;
    const TAILLE_VILLE = 60;
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
    function create_profile_vendeur($raisonSocial, $numSiret, $numCobrec, $email, $ville, $adresse, $compAdresse, $codePostal, $mdp, $mdpc, $chemin){
        global $pdo;
        $erreurs = [];
        
        $raisonSocial = strtoupper(trim($raisonSocial));
        $numSiret = nettoyer_chaine(trim($numSiret));
        $numCobrec = nettoyer_chaine(trim($numCobrec));
        $email = trim($email);
        
        $ville = trim($ville);
        $adresse = trim($adresse);
        $compAdresse = trim($compAdresse);
        $codePostal = trim($codePostal);

        $mdp = trim($mdp);
        $mdpc = trim($mdpc);

        if (check_raison_sociale_all($raisonSocial)
        && check_num_siret_all($numSiret) 
        && check_num_cobrec_all($numCobrec) 
        && check_email_all($email) 
        && check_ville_all($ville)
        && check_adresse_all($adresse)
        && check_code_postal_all($codePostal)
        && check_create_MDP($mdp, $mdpc)) {

            try{
                if (!sql_check_email($pdo, $email)){
                    if (sql_check_cle($pdo, $numCobrec)){
                        sql_create_vendeur($pdo, $raisonSocial, $numSiret, $email, $ville, $adresse, $compAdresse, $codePostal, $mdp, $numCobrec);
                    }
                    else{
                        $erreurs['numero_cobrec'] = EXISTE_PAS;
                    }
                }
                else{
                    $erreurs['email'] = "l'email ".$email ." ". EXISTE;
                }
            }
            catch(PDOException $e){
                echo $e->getMessage();
                $erreurs['fatal'] = true;
                $erreurs['correcte'] = false;
            }
        }
        else{
            $erreurs = array_merge($erreurs, check_erreur_vendeur($raisonSocial, $numSiret, $numCobrec, $email, $ville, $adresse, $codePostal, $mdp, $mdpc));

        }
        return $erreurs;
    }

    // Fonction qui permet de créer un compte client
    function create_profile_client($email, $nom, $prenom, $pseudo, $date_naiss, $mdp, $mdpc, $mot_clef, $reponse){
        $nom = strtoupper(trim($nom));
        $prenom = ucfirst(trim($prenom));
        $pseudo = trim($pseudo);
        $email = trim($email);

        $mdp = trim($mdp);
        $mdpc = trim($mdpc);

        $mot_clef = trim($mot_clef);
        $reponse = trim($reponse);

        $erreurs = [];

        // Pas d'erreur initialement

        // Si toutes les informations sont correctes
        if (check_nom($nom)
        && check_nom($prenom) 
        && check_nom($pseudo) 
        && check_date_passee($date_naiss)
        && check_create_MDP($mdp, $mdpc)
        && sql_check_mot_clef($mot_clef)
        && check_reponse($reponse))
        {

            global $pdo;
            
            try {
                if (!sql_check_email($pdo, $email)){

                    if (sql_create_client($pdo, $nom, $prenom, $pseudo, $email, $date_naiss, $mdp, $mot_clef, $reponse)){
                        
                    } else {
                        // changer l'erreur $erreurs['CR'] = EXISTE_PAS;
                    }
                    
                } else {
                    $erreurs['email'] = "l'email ".$email ." ". EXISTE;
                }
            } catch(PDOException $e) {
                $erreurs['fatal'] = true;
            }
        }
        else{
            $erreurs = array_merge($erreurs, check_erreur_client($nom, $prenom, $pseudo, $email, $date_naiss, $mdp, $mdpc, mot_clef: $mot_clef, reponse: $reponse));
        }
        return $erreurs;
    }
    

    // Fonction pour se connecter à un compte
    function connect_compte($email, $mdp, $typeCompte, $chemin){
        global $pdo;

        $email = trim($email);
        $mdp = trim($mdp);

        $erreurs = [];

        if (check_email_all($email) && check_mot_de_passe_all($mdp)) {
            
            try {
                $resSQL = sql_email_compte($pdo, $email, $typeCompte);

                if ($resSQL != null) {

                    if (check_crypte_MDP($mdp, $resSQL['mdp'])){

                        $_SESSION['logged_in'] = true;
                        $_SESSION['id_compte'] = $resSQL['id_compte'];
                        $_SESSION['email'] = $email;

                        if ($typeCompte == 'vendeur'){
                            $_SESSION['raison_sociale'] = $resSQL['raison_sociale'];
                        } else {
                            $_SESSION['pseudo'] = $resSQL['pseudo'];

                            require "fonction_panier.php";
                            transferer_panier_visiteur_compte($resSQL['id_compte']);
                        }
                    }
                    else {
                        $erreurs['connecte'] = CONNECTE_PAS;
                    }
                }
                else {
                    $erreurs['connecte'] = CONNECTE_PAS;
                }
            } catch(PDOException $e) {
                $erreurs['fatal'] = true;
            }
        } else {
            $erreurs = check_erreur_connection($email, $mdp);
        }

        return $erreurs;
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


    // Vérifie le nom de la ville (non vide, bonne taille)
    function check_ville_all($ville){
        return ((!check_vide($ville)) && check_taille($ville, TAILLE_VILLE));
    }
    
    // Vérifie l'adresse (non vide, bonne taille, bon format)
    function check_adresse_all($adresse){
        return ((!check_vide($adresse)) && check_taille($adresse, TAILLE_ADRESSE) && check_adresse($adresse));
    }

    // Vérifie le format de l'adresse
    function check_adresse($adresse){
        return preg_match("/^([1-9][0-9]*(?:-[1-9][0-9]*)*)[\s,-]+(?:(bis|ter|qua)[\s,-]+)?([\w]+[\-\w]*)[\s,]+/", $adresse);
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
        return (check_mot_de_passe($mdp) && check_taille($mdp, TAILLE_MDP));
    }

    // Vérifie le format du mot de passe
    function check_mot_de_passe($mdp){
        return (preg_match("/^.{12,}$/",$mdp));
    }

    // Vérifie l'égalité du MDP et de la confirmation du MDP
    function check_create_MDP($mdp, $mdpc){
        return (check_mot_de_passe($mdp) && check_taille($mdp, TAILLE_MDP) && ($mdp === $mdpc));
    }

    function check_reponse($reponse) {
        return (!check_vide($reponse));
    }


    // Vérifie un nom/prénom/pseudo (non vide, bonne taille)
    function check_nom($nom) {
        return (!check_vide($nom) && check_taille($nom, TAILLE_NOM));
    }

    // Vérifie que la date est passée
    function check_date_passee($date) {
        return (strtotime("1900-01-01") < strtotime($date) && strtotime($date) < time());
    }

    // vérifie le code de la carte bancaire
    function check_code_carte($code) {
        return (strlen($code) == 16);
    }

    // vérifie la date d'expiration de la carte
    function check_date_exp($date) {
        $mois = substr($date, 0, 2);
        $annee = substr($date, 3, 2);

        return preg_match('/^\d{2}\/\d{2}$/', $date) && 0 < $mois && $mois <= 12 && $annee >= date('Y') % 100;
    }

    //supprime les espaces, underscores et tirets
    function nettoyer_chaine($texte) {
        return str_replace([' ', '_', '-'], '', $texte);
    }

    //verifie le mot de passe crypte avec crypte_v2
    function check_crypte_MDP($mdp, $crypter){
        return password_verify($mdp, $crypter);
    }

    //verifie si la chaine est vide
    function check_vide($valeur){
        return (strlen($valeur ?? '') === 0);
    }

    //verifie la taille de la chaine
    function check_taille($valeur, $taille){
        return (strlen($valeur ?? '') <= $taille);
    }


// +----------------------+
// |  FONCTIONS D'ERREUR  |
// +----------------------+

    // Renvoie toutes les erreurs possibles de champ vendeur
    function check_erreur_vendeur($raisonSociale, $numSiret, $numCobrec, $email, $ville, $adresse, $codePostal, $mdp, $mdpc){
        $erreurs = [];

        // Recherche l'erreur dans la raison sociale
        if (check_vide($raisonSociale)){
            $erreurs['raison_sociale'] = VIDE;
        }
        else if (!check_taille($raisonSociale, TAILLE_RAISON_SOCIALE)){
            $erreurs['raison_sociale'] = DEPASSE;
        }
        else if (!check_raison_sociale($raisonSociale)){
            $erreurs['raison_sociale'] = FORMAT;
        }
        
        // Recherche l'erreur dans le numero de SIRET
        if (check_vide($numSiret)){
            $erreurs['numero_siret'] = VIDE;
        }
        else if (!check_num_siret($numSiret) ){
            $erreurs['numero_siret'] = FORMAT;
        }

        // Recherche l'erreur dans le numéro de la COBREC
        if (check_vide($numCobrec)){
            $erreurs['numero_cobrec'] = VIDE;
        }
        else if (!check_num_cobrec($numCobrec) ){
            $erreurs['numero_cobrec'] = FORMAT;
        }

        // Recherche l'erreur dans l'e-mail
        if (check_vide($email)){
            $erreurs['email'] = VIDE;
        }
        else if (!check_taille($email, TAILLE_EMAIL)){
            $erreurs['email'] = DEPASSE;
        }
        else if (!check_email($email)){
            $erreurs['email'] = FORMAT;        
        }

        
        // Recherche l'erreur dans l'adresse
        $erreurs = array_merge($erreurs, check_coordonnees($ville, $adresse, $codePostal));

        // Recherche l'erreur dans le mot de passe
        if (check_vide($mdp)){
            $erreurs['mdp'] = VIDE;
        }
        else if (!check_taille($mdp, TAILLE_MDP)){
            $erreurs['mdp'] = DEPASSE;
        }
        else if (!check_mot_de_passe($mdp)){
            $erreurs['mdp'] = FORMAT;
        }

        // Recherche l'erreur dans le mot de passe
        if (check_vide($mdpc)){
            $erreurs['mdpc'] = VIDE;
        }
        else if ($mdp !== $mdpc){
            $erreurs['mdpc'] = CORRESPOND_PAS;
        }

        return $erreurs;
    }

    // Renvoie toutes les erreurs de champ possibles pour un client
    function check_erreur_client($nom, $prenom, $pseudo, $email, $date_naiss, $mdp = null, $mdpc = null, $ville = null, $adresse = null, $code_postal = null, $mot_clef = null, $reponse = null){
        $erreurs = [];
        global $pdo;

        // erreur champ nom
        if (check_vide($nom)){
            $erreurs['nom'] = VIDE;
        }
        else if (!check_taille($nom, TAILLE_NOM)){
            $erreurs['nom'] = DEPASSE;
        }
        
        // erreur champ prenom
        if (check_vide($prenom)){
            $erreurs['prenom'] = VIDE;
        }
        else if (!check_taille($prenom, TAILLE_NOM)){
            $erreurs['prenom'] = DEPASSE;
        }

        // erreur champ pseudo
        if (check_vide($pseudo)){
            $erreurs['pseudo'] = VIDE;
        }
        else if (!check_taille($pseudo, TAILLE_NOM)){
            $erreurs['pseudo'] = DEPASSE;
        }

        //recherche l'erreur dans l'email
        if (check_vide($email)){
            $erreurs['email'] = VIDE;
        }
        else if (!check_taille($email, TAILLE_EMAIL)){
            $erreurs['email'] = DEPASSE;
        }
        else if (!check_email($email)){
            $erreurs['email'] = FORMAT; 
        }
        else if (sql_check_email($pdo,$email)){
            $erreurs['email'] = "l'email ".$email ." ". EXISTE; 
        }
        
        // erreur champ date naissance
        if (check_vide($date_naiss)) {
            $erreurs['date_naiss'] = VIDE;
        }
        else if (!check_date_passee($date_naiss)){
            $erreurs['date_naiss'] = FORMAT;  
        }

        // Recherche l'erreur dans le mot de passe
        if (isset($mdp)) {
            if (check_vide($mdp)){
                $erreurs['mdp'] = VIDE;
            }
            else if (!check_taille($mdp, TAILLE_MDP)){
                $erreurs['mdp'] = DEPASSE;
            }
            else if (!check_mot_de_passe($mdp)){
                $erreurs['mdp'] = FORMAT;
            }

            // Recherche l'erreur dans le mot de passe
            if (check_vide($mdpc)){
                $erreurs['mdpc'] = VIDE;
            }
            else if ($mdp !== $mdpc) {
                $erreurs['mdpc'] = CORRESPOND_PAS;
            }
        }

        // Recherche l'erreur dans l'adresse
        $erreurs = array_merge($erreurs, check_coordonnees($ville, $adresse, $code_postal));

        // Recherche l'erreur dans la question secrète (mot clef)
        if (check_vide($mot_clef)) {
            $erreurs['question'] = VIDE;
        } else if (!sql_check_mot_clef($mot_clef)) {
            $erreurs['question'] = EXISTE_PAS;
        }

        // Recherche l'erreur dans la réponse secrète
        if (check_vide($reponse)){
            $erreurs['reponse'] = VIDE;
        }

        return $erreurs;
    }

    // Renvoie toutes les erreurs possbiles pour la partie coordonnées
    function check_coordonnees($ville, $adresse, $code_postal) {
        $erreurs = [];

        // Recherche l'erreur dans la ville
        if (isset($ville)) {
            if (check_vide($ville)) {
                $erreurs['ville'] = VIDE;
            }
            else if (!check_taille($ville, TAILLE_VILLE)){
                $erreurs['ville'] = DEPASSE;
            }
        }

        // Recherche l'erreur dans l'adresse
        if (isset($adresse)) {
            if (check_vide($adresse)){
                $erreurs['adresse'] = VIDE;
            }
            else if (!check_taille($adresse, TAILLE_ADRESSE)){
                $erreurs['adresse'] = DEPASSE;
            }
            else if (!check_adresse($adresse)){
                $erreurs['adresse'] = FORMAT;
            }
        }

        // Recherche l'erreur dans le code postal
        if (isset($code_postal)) {
            if (check_vide($code_postal)){
                $erreurs['code_postal'] = VIDE;
            }
            else if (!check_code_postal($code_postal)){
                $erreurs['code_postal'] = FORMAT;
            }
        }

        return $erreurs;
    }

    // renvoit toutes les erreurs possibles pour la partie coordonnées bancaires
    function check_coordonnees_bancaires($code_carte, $date_exp, $code_securite) {
        $erreurs = [];

        // erreur sur code de carte
        if (check_vide($code_carte)) {
            $erreurs['code_carte'] = VIDE;
        }
        else if (!check_code_carte($code_carte)) {
            $erreurs['code_carte'] = FORMAT;
        }

        // erreur sur date d'expiration
        $date_exp = trim($date_exp);
        
        if (check_vide($date_exp)) {
            $erreurs['date_exp'] = VIDE;
        } else if (!check_date_exp($date_exp)) {
            $erreurs['date_exp'] = FORMAT;
        }
        
        // erreur sur code de sécurité
        if (check_vide($code_securite)) {
            $erreurs['code_securite'] = VIDE;
        }
        else if (!check_taille($code_securite, 3)) {
            $erreurs['code_securite'] = FORMAT;
        }

        return $erreurs;
    }

    function check_erreur_connection($email, $mdp){
        $erreurs = [];

        //recherche dans l'email
        if (check_vide($email)){
            $erreurs['email'] = VIDE;
        }
        else if (!check_taille($email, TAILLE_EMAIL)){
            $erreurs['email'] = DEPASSE;
        }
        else if (!check_email($email)){
            $erreurs['email'] = FORMAT; 
        }

        //recherche l'erreur dans le mot de passe
        if (check_vide($mdp)){
            $erreurs['mdp'] = VIDE;
        }
        else if (!check_taille($mdp, TAILLE_MDP)){
            $erreurs['mdp'] = DEPASSE;
        }
        else if (!check_mot_de_passe($mdp)){
            $erreurs['mdp'] = FORMAT;
        }

        return $erreurs;
    }

// +-----------------------------+
// |  FONCTIONS BASE DE DONNÉES  |
// +-----------------------------+

    // Vérifie la présence d'un email dans la BDD
    // Return true si existe, false sinon
    function sql_check_email($pdo, $email){
        $requete = $pdo->prepare("SELECT email_actif_existe(:email) AS existe");
        $requete->bindValue(':email', $email, PDO::PARAM_STR);
        $requete->execute();

        return ($requete->fetch(PDO::FETCH_ASSOC)['existe'] == 1);
    }

    // Return la question associée à une adresse email, sinon null
    function sql_email_question($email) {
        global $pdo;

        $requete = $pdo->prepare("SELECT q.question FROM client c INNER JOIN _question_secu q ON c.question = q.mot_clef WHERE email = :email");
        $requete->bindValue(':email', $email, PDO::PARAM_STR);
        $requete->execute();

        return ($requete->fetch(PDO::FETCH_ASSOC));
    }

    // Vérifie si la réponse donnée est bien celle associée à l'e-mail donné
    function sql_check_reponse($email, $reponse) {
        global $pdo;

        $requete = $pdo->prepare("SELECT reponse FROM compte_client WHERE email = :email");
        $requete->bindValue(':email', $email, PDO::PARAM_STR);
        $requete->execute();

        $res = $requete->fetch(PDO::FETCH_ASSOC);

        return (check_crypte_MDP($reponse, $res['reponse']));
    }

    // Return un e-mail et MDP hashé si le compte existe, ou null sinon (OU erreur)
    function sql_email_compte($pdo, $email, $typecompte){
        if ($typecompte == 'vendeur') {
            $requete = $pdo->prepare("SELECT * FROM vendeur WHERE email = :email");
        } else {
            $requete = $pdo->prepare("SELECT * FROM client WHERE email = :email");
        }

        $requete->bindValue(':email', $email, PDO::PARAM_STR);
        $requete->execute();

        return $requete->fetch(PDO::FETCH_ASSOC);
    }

    // Vérifie l'existence d'une clé COBREC
    // Return true si existe, false sinon
    function sql_check_cle($pdo, $cle){
        $requete = $pdo->prepare("SELECT 1 FROM _cle_vendeur WHERE cle_cobrec = :cle");
        $requete->bindValue(':cle', $cle, PDO::PARAM_INT);
        $requete->execute();

        return ($requete->fetch(PDO::FETCH_ASSOC) != null);
    }

    // Vérifie l'existence d'un mot clef
    function sql_check_mot_clef($mot_clef) {
        global $pdo;

        $requete = $pdo->prepare("SELECT mot_clef FROM _question_secu WHERE mot_clef = :mot_clef");
        $requete->bindValue(':mot_clef', $mot_clef, PDO::PARAM_STR);
        $requete->execute();

        return ($requete->fetch(PDO::FETCH_ASSOC) != null);
    }

    
    function sql_create_client($pdo, $nom, $prenom, $pseudo, $email, $date_naiss, $mdp, $mot_clef, $reponse) {
        $requete = $pdo->prepare("INSERT INTO _compte (email, mdp) VALUES (:email, :mdp)");
        $requete->bindValue(':email', $email, PDO::PARAM_STR);
        $requete->bindValue(':mdp', crypte_v2($mdp), PDO::PARAM_STR);
        $requete->execute();
        
        $requete = $pdo->prepare("SELECT id_compte FROM _compte WHERE email = :email");
        $requete->bindValue(':email', $email);
        $requete->execute();
        $id_compte = $requete->fetch(PDO::FETCH_ASSOC)['id_compte'];

        $requete = $pdo->prepare("INSERT INTO _client (id_compte, pseudo, nom, prenom, date_naissance, question, reponse) VALUES (:id_compte, :pseudo, :nom, :prenom, :date_naissance, :mot_clef, :reponse)");
        $requete->bindValue(':id_compte', $id_compte, PDO::PARAM_INT);
        $requete->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
        $requete->bindValue(':nom', $nom, PDO::PARAM_STR);
        $requete->bindValue(':prenom', $prenom, PDO::PARAM_STR);
        $requete->bindValue(':date_naissance', $date_naiss, PDO::PARAM_STR);
        $requete->bindValue(':mot_clef', $mot_clef, PDO::PARAM_STR);
        $requete->bindValue(':reponse', crypte_v2($reponse), PDO::PARAM_STR);
        $requete->execute();

        return $requete->fetch(PDO::FETCH_ASSOC);
    }

    function sql_create_vendeur($pdo, $raisonSociale, $numSiret, $email, $ville, $adresse, $compAdresse, $codePostal, $mdp, $numCobrec) {
        $mdp = crypte_v2($mdp);
        
        $requete = $pdo->prepare("CALL creer_vendeur_compte(:email, :mdp, :ville, :adresse, :complement_adresse, :code_postal, :raison_sociale, :num_siret, :cle_cobrec)");
        $requete->bindValue(":email", $email, PDO::PARAM_STR);
        $requete->bindValue(":mdp", $mdp, PDO::PARAM_STR);
        $requete->bindValue(":ville", $ville, PDO::PARAM_STR);
        $requete->bindValue(":adresse", $adresse, PDO::PARAM_STR);
        $requete->bindValue(":complement_adresse", $compAdresse, PDO::PARAM_STR);
        $requete->bindValue(":code_postal", $codePostal, PDO::PARAM_STR);
        $requete->bindValue(":raison_sociale", $raisonSociale, PDO::PARAM_STR);
        $requete->bindValue(":num_siret", $numSiret, PDO::PARAM_STR);
        $requete->bindValue(":cle_cobrec", $numCobrec, PDO::PARAM_STR);

        $requete->execute();
    }

    function sql_update_client($pdo, $nom, $prenom, $pseudo, $email, $ville, $adresse, $code_postal, $complement_adresse, $mdpc, $id_compte, $id_adresse) {
        $nom = strtoupper(trim($nom));
        $prenom = ucfirst(trim($prenom));
        $pseudo = trim($pseudo);
        $email = trim($email);

        if($mdpc==""){
            $requete = $pdo->prepare("UPDATE _compte SET email = :email WHERE id_compte = :id_compte");
            $requete->bindValue(':email', $email, PDO::PARAM_STR);
            $requete->bindValue(':id_compte', $id_compte, PDO::PARAM_INT);
            $requete->execute();

        } else{
            $requete = $pdo->prepare("UPDATE _compte SET email = :email, mdp = :mdpc WHERE id_compte = :id_compte");
            $requete->bindValue(':email', $email, PDO::PARAM_STR);
            $requete->bindValue(':mdpc', crypte_v2($mdpc), PDO::PARAM_STR);
            $requete->bindValue(':id_compte', $id_compte, PDO::PARAM_INT);
            $requete->execute();
        }
        

        $requete = $pdo->prepare("UPDATE _client SET pseudo = :pseudo, nom = :nom, prenom = :prenom WHERE id_compte = :id_compte");
        $requete->bindValue(':id_compte', $id_compte, PDO::PARAM_INT);
        $requete->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
        $requete->bindValue(':nom', $nom, PDO::PARAM_STR);
        $requete->bindValue(':prenom', $prenom, PDO::PARAM_STR);
        $requete->execute();
        
        $ancienne_adresse= sql_get_info_compte($id_compte)['email'];
        if ($ancienne_adresse!=null){
            $requete = $pdo->prepare("UPDATE _adresse SET ville = :ville, adresse = :adresse, code_postal = :code_postal, complement_adresse = :complement_adresse WHERE id_adresse = :id_adresse");
            $requete->bindValue(':id_adresse', $id_adresse, PDO::PARAM_INT);
            $requete->bindValue(':ville', $ville, PDO::PARAM_STR);
            $requete->bindValue(':adresse', $adresse, PDO::PARAM_STR);
            $requete->bindValue(':code_postal', $code_postal, PDO::PARAM_STR);
            $requete->bindValue(':complement_adresse', $complement_adresse, PDO::PARAM_STR);
            $requete->execute();

        } else {
            sql_insert_adresse_client($pdo, $id_compte, $ville, $adresse, $complement_adresse, $code_postal);
        }
        
        
        return $requete->fetch(PDO::FETCH_ASSOC);
        
    }

    // Fonction pour changer le mot de passe d'un client à partir de son e-mail
    function sql_change_mdp($email, $mdp) {
        global $pdo;

        $requete = $pdo->prepare("UPDATE _compte SET mdp = :mdp WHERE email = :email");
        $requete->bindValue(':email', $email, PDO::PARAM_STR);
        $requete->bindValue(':mdp', crypte_v2($mdp), PDO::PARAM_STR);
        $requete->execute();

        return $requete->rowCount() > 0;
    }

    // fonction qui insère l'adresse pour le client
    function sql_insert_adresse_client($pdo, $id_compte, $ville, $adresse, $complement_adresse, $code_postal) {
        $requete = $pdo->prepare("INSERT INTO _adresse (ville, adresse, complement_adresse, code_postal) VALUES (:ville, :adresse, :comp_adresse, :code_postal)");
        $requete->bindValue(':ville', $ville, PDO::PARAM_STR);
        $requete->bindValue(':adresse', $adresse, PDO::PARAM_STR);
        $requete->bindValue(':comp_adresse', $complement_adresse, PDO::PARAM_STR);
        $requete->bindValue(':code_postal', $code_postal, PDO::PARAM_STR);
        $requete->execute();

        $id_adresse = $pdo->lastInsertId();

        $requete = $pdo->prepare("UPDATE _client SET id_adresse_fac = :id_adresse WHERE id_compte = :id_compte");
        $requete->bindValue(":id_adresse", $id_adresse, PDO::PARAM_INT);
        $requete->bindValue(":id_compte", $id_compte, PDO::PARAM_INT);
        $requete->execute();
    }

    //requete pour recuperer informations du compte sans l'adresse
    function sql_get_info_compte($id_compte){
        global $pdo;
    
        $requete = $pdo->prepare('SELECT * FROM client WHERE id_compte = :id_compte;');
        $requete->bindValue(":id_compte", $id_compte, PDO::PARAM_INT);
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }

    //requete pour recuperer les infos d'une adresse
    function sql_get_adresse($id_adresse){
        global $pdo;
        
        $requete = $pdo->prepare('SELECT * FROM _adresse WHERE id_adresse = :id_adresse;');
        $requete->bindValue(":id_adresse", $id_adresse, PDO::PARAM_INT);
        $requete->execute();
        return $requete->fetch(PDO::FETCH_ASSOC);
    }

    
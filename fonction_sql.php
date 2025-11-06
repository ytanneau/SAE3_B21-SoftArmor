<?php

    define('DB_NAME', $_ENV['DB_NAME']);
    define('DB_HOST', $_ENV['DB_HOST']);
    define('DB_USERNAME', $_ENV['DB_USERNAME']);
    define('DB_PASSWORD', $_ENV['DB_PASSWORD']);

    try {
        $pdo = new PDO("mysql:dbname=" . DB_NAME . ";host=" . DB_HOST, DB_USERNAME,DB_PASSWORD);
        
        // Les erreurs seront sous forme d'exceptions
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        die("ERREUR: La connexion a échoué. " . $e->getMessage());
    }

    function sql_request($prepare, $parametre){
        global $pdo;
        try{
            $requete = $pdo->prepare($prepare);
            foreach ($parametre as $key => $value){
                $requete->bindValue($key, $value, PDO::PARAM_STR);
            }
            $requete->execute();
            return ($requete->fetch(PDO::FETCH_ASSOC) != null);
        }
        catch (PDOException $e) {
            $fichierLog = __DIR__ . "/erreurs.log";
            $date = date("Y-m-d H:i:s");
            file_put_contents($fichierLog, "[$date] Failed SQL request ", FILE_APPEND);
            throw $e;
        }
    }
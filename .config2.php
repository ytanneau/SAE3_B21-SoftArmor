<?php
    class RequeteSQL{

        private $DB_NAME;
        private $DB_HOST;
        private $DB_USERNAME;
        private $DB_PASSWORD;
        private $pdo;
        private $requete;

        function __construct() {
            $this->DB_NAME = "saedb";
            $this->DB_HOST = "mariadb";
            $this->DB_PASSWORD = "sae";
            $this->DB_PASSWORD = "dbsae3dunyles";
            $this->requete = null;

            try {
                $this->$pdo = new PDO("mysql:dbname=" . DB_NAME . ";host=" . DB_HOST, DB_USERNAME,DB_PASSWORD);
                
                // Les erreurs seront sous forme d'exceptions
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(PDOException $e) {
                die("ERREUR: La connexion a échoué. " . $e->getMessage());
            }

            function prepare($requete) {
                try{
                    $this->requete = $pdo->prepare($requete);
                } catch(PDOException $e){
                    throw $e;
                }
            }

            function bindValue($parametre) {
                try{
                    foreach ($parametre as $key => $value){
                        $this->requete->bindValue($key, $value, PDO::PARAM_STR);
                    }
                } catch(PDOException $e){
                    throw $e;
                }
            }

            function prepare($requete) {
                try{
                    $this->requete->execute();
                    return $this->requete->fetch(PDO::FETCH_ASSOC);
                } catch(PDOException $e){
                    throw $e;
                }
            }
        }
    }

        
    
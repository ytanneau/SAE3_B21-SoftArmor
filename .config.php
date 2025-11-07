<?php
define('DB_NAME', 'SAE');
define('DB_HOST', 'mysql');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'dbsae3dunyles');

try {
    $pdo = new PDO("mysql:dbname=" . DB_NAME . ";host=" . DB_HOST, DB_USERNAME,DB_PASSWORD);
    
    // Les erreurs seront sous forme d'exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("ERREUR: La connexion a échoué. " . $e->getMessage());
}
?>
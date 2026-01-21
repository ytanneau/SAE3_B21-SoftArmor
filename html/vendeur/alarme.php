<?php

if (!isset($_SESSION)) {
    session_start();
}

define('HOME_GIT', '../../');
define('HOME_SITE', '../');


require_once HOME_GIT .".config.php";
require_once(HOME_GIT . 'fonction_alarme.php');

affiche_alarme($_SESSION['id_compte']);

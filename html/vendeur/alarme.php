<?php


define('HOME_GIT', '../../');
define('HOME_SITE', '../');



require_once(HOME_GIT . 'fonction_alarme.php');

$data = get_alarme($_SESSION['raison_sociale']);

print_r($data);
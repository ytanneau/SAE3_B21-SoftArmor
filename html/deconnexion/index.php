<?php
    session_start();

    $location = '../';

    if (isset($_SESSION['raison_sociale'])) {
        $location = '../vendeur/';
    }

    session_destroy();
    header('location: ' . $location);
    exit;
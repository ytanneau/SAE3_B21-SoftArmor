<?php
define("HOME_GIT", "../../../../");
define("HOME_SITE", "../../../");

session_start();
?>

<!DOCTYPE html>
<html lang="">

<head>
  <meta charset="UTF-8">
  <link rel="icon" href="/favicon.ico">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100..900&display=swap" rel="stylesheet">

  <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
  <link rel="icon" type="image/png" href="<?= HOME_SITE ?>image/logo_Alizon_bleu.png">

  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vite App</title>
  <script type="module" crossorigin src="./assets/index-DfKfvJDl.js"></script>
  <link rel="stylesheet" crossorigin href="./assets/index-DrrHkUzT.css">

  <style>
    #header_vendeur {
      position: sticky;
      top: 0;
      z-index: 11;
      padding: 0.5em 1em;
      background-color: #6B31D0;
    }

    #header_vendeur * {
      font-family: "Montserrat";
      font-weight: bold;
    }

    @media screen and (max-width: 1024px) {
      #header_vendeur {
        padding: 1.5em 1em;
      }
    }

    @media screen and (max-width: 1024px) {
      #header_vendeur .hide-on-mobile {
        display: none;
      }
    }

    #header_vendeur .menu-button {
      display: none;
      margin-left: auto;
    }

    #header_vendeur .menu-button img {
      height: 75px;
      width: 75px;
    }

    @media screen and (max-width: 1024px) {
      #header_vendeur .menu-button {
        display: block;
      }
    }

    #header_vendeur .dropdown {
      position: relative;
      display: inline-block;
      font-size: 1em;
    }

    #header_vendeur .dropdown>.dropdown-button {
      display: flex;
      flex-direction: row;
      align-items: center;
      font-size: 1em;
      background-color: #6B31D0;
      color: white;
      border: none;
      padding-left: 0;
      padding-right: 0;
      cursor: pointer;
    }

    #header_vendeur .dropdown>.dropdown-content {
      display: none;
      position: absolute;
      top: 2.5em;
      right: 0;
      min-width: 200px;
      box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.35);
      z-index: 1;
      background-color: #ffffff;
      border-radius: 5px;
    }

    #header_vendeur .dropdown>.dropdown-content>a {
      display: flex;
      flex-direction: row;
      align-items: center;
      padding: 0.25em 0.5em;
      text-decoration: none;
      color: black;
      font-weight: bold;
      border-radius: 5px;
    }

    #header_vendeur .dropdown>.dropdown-content>a:hover {
      background-color: #ECEDF9;
    }

    #header_vendeur .dropdown>.show {
      display: block;
    }

    #header_vendeur>nav {
      display: flex;
      flex-direction: row;
      align-items: center;
      justify-content: space-between;
    }

    #header_vendeur>nav>ul {
      display: flex;
      flex-direction: row;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      list-style: none;
      width: 100%;
      margin: 0;
      padding: 0;
    }

    #header_vendeur>nav>ul>li {
      margin: 0 1em;
    }

    #header_vendeur>nav>ul>li.searchbar {
      display: flex;
      flex-grow: 1;
      justify-content: center;
    }

    #header_vendeur>nav>ul>li.searchbar form {
      display: flex;
      flex-basis: 65%;
      align-items: center;
      background-color: white;
      border-radius: 20px;
    }

    #header_vendeur>nav>ul>li.searchbar form input,
    #header_vendeur>nav>ul>li.searchbar form button {
      border: none;
    }

    #header_vendeur>nav>ul>li.searchbar form input[type=search] {
      padding: 0.5em 0.5em 0.5em 1.5em;
      border-radius: 20px;
      flex-grow: 1;
      outline: none;
      font-weight: 400;
    }

    @media screen and (max-width: 1024px) {
      #header_vendeur>nav>ul>li.searchbar form input[type=search] {
        padding: 1.5em 0.5em 1.5em 1.5em;
      }
    }

    #header_vendeur>nav>ul>li.searchbar form button[type=submit] {
      background-color: transparent;
    }

    @media screen and (max-width: 1024px) {
      #header_vendeur>nav>ul>li.searchbar {
        order: 3;
        flex-basis: 100%;
      }
    }

    #header_vendeur>nav>ul>li:first-child img {
      height: 30px;
    }

    @media screen and (max-width: 1024px) {
      #header_vendeur>nav>ul>li:first-child img {
        height: 50px;
      }
    }

    #header_vendeur>nav>ul>li>a {
      display: flex;
      flex-direction: row;
      align-items: center;
      color: white;
      text-decoration: none;
    }

    #header_vendeur>nav>ul.sidebar {
      display: none;
      position: fixed;
      top: 0;
      right: 0;
      max-width: 400px;
      height: 100vh;
      padding: 1em;
      font-size: 2.25em;
      background-color: rgba(0, 0, 0, 0.75);
      backdrop-filter: blur(10px);
      flex-direction: column;
      align-items: flex-start;
      justify-content: flex-start;
      z-index: 5;
    }

    #header_vendeur>nav>ul.sidebar li {
      margin: 0 0 1em 0;
    }

    #header_vendeur>nav>ul.sidebar li:first-of-type {
      margin-bottom: 2em;
    }

    #header_vendeur>nav>ul.sidebar li:nth-of-type(2) {
      margin-left: 0;
    }

    #header_vendeur>nav>ul.sidebar img {
      height: 60px;
      width: 60px;
    }
  </style>
</head>

<body>
  <?php include HOME_SITE . "/vendeur/header.php" ?>
  <div id="app">

  </div>
</body>

</html>
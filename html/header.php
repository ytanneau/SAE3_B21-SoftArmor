<?php $images = HOME_SITE . "image/" ?>

<header id="header_client">
    <nav>
        <ul class="sidebar">
            <li onclick=closeSidebar()> <img src="<?= $images . 'fermer_blanc.svg' ?>"> </li>

            <!-- Si connecté -->
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) { ?>
                <li> 
                    <a href=""> 
                        <img src="<?= $images . 'panier_blanc.svg' ?>" class="icon">Mon panier
                    </a> 
                </li>

                <li> 
                    <a href="<?= HOME_SITE . 'compte/informations' ?>"> 
                        <img src="<?= $images . 'compte_blanc.svg' ?>" class="icon">Mon profil
                    </a> 
                </li>

                <li>
                    <a href="<?= HOME_SITE . 'deconnexion' ?>">
                        <img src="<?= $images . 'deconnexion_blanc.svg' ?>" class="icon">Déconnexion
                    </a> 
                </li>


            <!-- Si déconnecté -->
            <?php } else {
                $param = "";
                if (isset($_GET['produit'])) $param = "?produit=" . $_GET['produit']; ?>

                <li>
                    <a href="<?= HOME_SITE . 'compte/inscription' . $param?>"> 
                        <img src="<?= $images . 'compte_blanc.svg' ?>" class="icon">S'inscrire
                    </a> 
                </li>

                <li>
                    <a href="<?= HOME_SITE . 'compte/connexion' . $param?>">
                        <img src="<?= $images . 'connexion_blanc.svg' ?>" class="icon">Se connecter
                    </a> 
                </li>
            <?php } ?>
        </ul>
            
        <ul>
            <li>
                <a href="<?= HOME_SITE ?>">
                    <img src="<?= $images . 'Alizon_blanc.png' ?>" alt="Logo Alizon" title="Logo Alizon"> 
                </a> 
            </li>

            <li class="hide-on-mobile searchbar">
                <form action="<?= HOME_SITE . 'recherche/' ?>" id="form_recherche" method="get">
                    <input type="search" name="recherche" id="recherche" size="60" placeholder="Rechercher...">
                    <button type="submit"><img src="../image/loupe.svg"></button>
                </form>
            </li>
            
            <!-- Si connecté -->
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) { ?>
                <li class="hide-on-mobile">
                    <a href="<?= HOME_SITE . 'panier/' ?>">
                        <img src="<?= $images . 'panier_blanc.svg' ?>" class="icon">Mon panier
                    </a> 
                </li>

                <li class="hide-on-mobile">
                    <div class="dropdown">
                        <button onclick="ouvrirMenu()" class="dropdown-button">
                            <img src="<?= $images . 'compte_blanc.svg' ?>" class="icon">
                            <?= htmlentities($_SESSION['pseudo'] ?? '') ?>
                        </button>

                        <div id="dropdown-compte" class="dropdown-content">
                            <a href="<?= HOME_SITE . 'compte/informations' ?>"> <img src="<?= $images . 'compte.svg' ?>" class="icon">Mon profil</a>
                            <!-- <a href="#"> <img src="../image/options.svg" class="icon">Paramètres</a> -->
                            <a href="<?= HOME_SITE . 'commande' ?>"> <img src="<?= $images . 'panier.svg' ?>" class="icon">Mé comand</a>
                            <a href="<?= HOME_SITE . 'deconnexion' ?>"> <img src="<?= $images . 'deconnexion.svg' ?>" class="icon">Déconnexion</a>
                        </div>
                    </div>
                </li>

            <!-- Si déconnecté -->
            <?php } else {
                $param = "";
                if (isset($_GET['produit'])) $param = "?produit=" . $_GET['produit']; ?>

                <li class="hide-on-mobile">
                    <a href="<?= HOME_SITE . 'compte/inscription' . $param?>"> 
                        <img src="<?= $images . 'compte_blanc.svg' ?>" class="icon">S'inscrire
                    </a> 
                </li>

                <li class="hide-on-mobile">
                    <a href="<?= HOME_SITE . 'compte/connexion' . $param?>">
                        <img src="<?= $images . 'connexion_blanc.svg' ?>" class="icon">Se connecter
                    </a> 
                </li>
            <?php } ?>

            <li class="menu-button" onclick=showSidebar()>
                <img src="<?= $images . 'menu_blanc.svg' ?>">
            </li>
        </ul>
    </nav>
</header>

<script>
    function showSidebar() {
        const sidebar = document.querySelector(".sidebar");
        sidebar.style.display = "flex";
    }

    function closeSidebar() {
        const sidebar = document.querySelector(".sidebar");
        sidebar.style.display = "none";
    }

    function ouvrirMenu() {
        document.getElementById("dropdown-compte").classList.toggle("show");
    }

    // Ferme le menu si on clique ailleurs
    window.onclick = function(event) {
        if (!event.target.matches('.dropdown-button')) {
            var dropdowns = document.getElementsByClassName("dropdown-content");
            var i;

            for (i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    }
</script>
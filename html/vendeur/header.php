<?php $images = HOME_SITE . "image/" ?>

<header id="header_vendeur">
    <nav>
        <ul class="sidebar">
            <li onclick=closeSidebar()> <img src="<?= $images . 'fermer_blanc.svg' ?>"> </li>

            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) { ?>
                <li> 
                    <a href="<?= HOME_SITE . 'vendeur/compte/information_compte_vendeur' ?>">
                        <img src="<?= $images . 'compte_vendeur_blanc.svg' ?>" class="icon">Mon profil
                    </a>
                </li>

                <li>
                    <a href="<?= HOME_SITE . 'deconnexion' ?>">
                        <img src="<?= $images . 'deconnexion_blanc.svg' ?>" class="icon">Déconnexion
                    </a> 
                </li>
            <?php } ?>
        </ul>
            
        <ul>
            <li>
                <a href="<?= HOME_SITE . 'vendeur/stock' ?>">
                    <img src="<?= $images . 'Alizon_vendeur_blanc.png' ?>" alt="Logo Alizon Vendeur" title="Logo Alizon Vendeur"> 
                </a> 
            </li>

            <li class="hide-on-mobile">
                <div class="dropdown">
                    <button onclick="ouvrirMenu()" class="dropdown-button">
                        <img src="<?= $images . 'compte_vendeur_blanc.svg' ?>" class="icon">
                        <?= htmlentities($_SESSION['raison_sociale'] ?? '') ?>
                    </button>

                    <div id="dropdown-compte" class="dropdown-content">
                        <a href="<?= HOME_SITE . 'vendeur/compte/information_compte_vendeur' ?>"> <img src="<?= $images . 'compte_vendeur.svg' ?>" class="icon">Mon profil</a>
                        <!-- <a href="#"> <img src="../image/options.svg" class="icon">Paramètres</a> -->
                        <a href="<?= HOME_SITE . 'vendeur/commande'?>"><img src="<?=$images?>panier.svg" class="icon">Lé komand</a>
                        <a href="<?= HOME_SITE . 'deconnexion' ?>"> <img src="<?=$images?>deconnexion.svg" class="icon">Déconnexion</a>
                    </div>
                </div>
            </li>

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
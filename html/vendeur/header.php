<?php $images = HOME_SITE . "image/" ?>

<header id="header_client">
    <div>
        <a href=<?= HOME_SITE ?>>
            <img src="<?= $images . 'Alizon_vendeur_blanc.png' ?>" alt="Logo Alizon vendeur" title="Logo Alizon vendeur">
        </a>

        <ul>
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) { ?>
                <li>
                    <div class="dropdown">
                        <button onclick="ouvrirMenu()" class="dropdown-button">
                            <img src="<?= $images . 'compte_vendeur_blanc.svg' ?>" class="icon">
                            <?= $_SESSION['raison_sociale'] ?>
                        </button>

                        <div id="dropdown-compte" class="dropdown-content">
                            <a href="<?= HOME_SITE . 'vendeur/compte/information_compte_vendeur' ?>"> <img src="<?= $images . 'compte_vendeur.svg' ?>" class="icon">Mon profil</a> 
                            <!-- <a href="#"> <img src="<?= $images . 'options.svg' ?>" class="icon">Paramètres</a> -->
                            <a href="<?= HOME_SITE . 'deconnexion' ?>"> <img src="<?= $images . 'deconnexion.svg' ?>" class="icon">Déconnexion</a>
                        </div>
                    </div>
                </li>
            <?php } ?>
        </ul>
    </div>
</header>

<script>
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
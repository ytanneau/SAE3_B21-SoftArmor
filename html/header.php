<?php $images = HOME_SITE . "image/" ?>

<header id="header_client">
    <div>
        <a href=<?= HOME_SITE ?>>
            <img src="<?= $images . 'Alizon_blanc.png' ?>" alt="Logo Alizon" title="Logo Alizon">
        </a>

        <ul>
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) { ?>
                <li>
                    <div class="dropdown">
                        <button onclick="ouvrirMenu()" class="dropdown-button">
                            <img src="image/compte_blanc.svg" class="icon">
                            <?= $_SESSION['pseudo'] ?>
                        </button>

                        <div id="dropdown-compte" class="dropdown-content">
                            <a href="compte/informations"> <img src="<?= $images . 'compte.svg' ?>" class="icon">Mon profil</a> 
                            <a href="#"> <img src="<?= $images . 'options.svg' ?>" class="icon">Paramètres</a>
                            <a href="deconnexion"> <img src="<?= $images . 'deconnexion.svg' ?>" class="icon">Déconnexion</a>
                        </div>
                    </div>
                </li>
            <?php } else { ?>
                <li> <a href="compte/inscription"> <img src="<?= $images . 'compte_blanc.svg' ?>" class="icon">S'inscrire</a> </li>
                <li> <a href="compte/connexion"> <img src="<?= $images . 'connexion_blanc.svg' ?>" class="icon">Se connecter</a> </li>
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
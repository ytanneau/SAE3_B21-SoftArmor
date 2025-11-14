<header id="header_client">
    <div>
        <a href="#">
            <img src="image/Alizon_blanc.png" alt="Logo Alizon" title="Logo Alizon">
        </a>

        <ul>
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) { ?>
                <li>
                    <div class="dropdown">
                        <button onclick="ouvrirMenu()" class="dropdown-button">
                            <img src="image/compte_blanc.svg" class="icon">
                            <?= "Roger" ?>
                        </button>

                        <div id="dropdown-compte" class="dropdown-content">
                            <a href="#"> <img src="image/compte.svg" class="icon"> Mon profil </a> 
                            <a href="#"> <img src="image/options.svg" class="icon"> Paramètres</a>
                            <a href="deconnexion"> <img src="image/deconnexion.svg" class="icon"> Déconnexion</a>
                        </div>
                    </div>
                </li>
            <?php } else { ?>
                <li> <a href="compte/connexion">Se connecter</a> </li>
                <li> <a href="compte/inscription">S'inscrire</a> </li>
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
<?php $images = HOME_SITE . "image/" ?>

<header id="header_client">
    <div>
        <a href=<?= HOME_SITE ?>>
            <img src="<?= $images . 'Alizon_blanc.png' ?>" alt="Logo Alizon" title="Logo Alizon">
        </a>

        <ul>
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) { ?>
                <li> <a href="<?= HOME_SITE . 'panier/' ?>"> <img src="<?= $images . 'panier_blanc.svg' ?>" class="icon">Mon panier</a> </li>
                <li>
                    <div class="dropdown">
                        <button onclick="ouvrirMenu()" class="dropdown-button">
                            <img src="<?= $images . 'compte_blanc.svg' ?>" class="icon">
                            <?= $_SESSION['pseudo'] ?>
                        </button>
                        <div id="dropdown-compte" class="dropdown-content">
                            <a href="<?= HOME_SITE . 'compte/informations' ?>"> <img src="<?= $images . 'compte.svg' ?>" class="icon">Mon profil</a> 
                            <!-- <a href="#"> <img src="<?= $images . 'options.svg' ?>" class="icon">Paramètres</a> -->
                            <a href="<?= HOME_SITE . 'deconnexion' ?>"> <img src="<?= $images . 'deconnexion.svg' ?>" class="icon">Déconnexion</a>
                        </div>
                    </div>
                </li>
            <?php } else { 
                $param = "";
                // permet de faire que si un utilisateur est sur une page produit, et qu'il passe par les boutons du header, une fois co, il soit remis sur la page où il était
                if (isset($_GET['id_produit'])) $param = "?id_produit=" . $_GET['id_produit'];
                ?>
                <li> <a href="<?= HOME_SITE . 'compte/inscription' . $param?>"> <img src="<?= $images . 'compte_blanc.svg' ?>" class="icon">S'inscrire</a> </li>
                <li> <a href="<?= HOME_SITE . 'compte/connexion' . $param?>"> <img src="<?= $images . 'connexion_blanc.svg' ?>" class="icon">Se connecter</a> </li>
            <?php } ?>
        </ul>
    </div>
</header>


<footer id= "footer_client">
    <div>
        
    </div>
</footer>
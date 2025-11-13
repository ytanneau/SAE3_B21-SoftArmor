<header id="header_vendeur">
    <div>
        <a href="<?=HOME_SITE?>vendeur/stock">
            <img src="<?=HOME_SITE?>image/Alizon_vendeur_blanc.png" alt="logo alizon vendeur" title="logo alizon vendeur">
        </a>
        <nav>
            <ul>
                <a href="<?=HOME_SITE?>vendeur/stock">
                    <li>
                        <img src="<?=HOME_SITE?>image/stocks.svg" alt="">
                        Stock
                    </li>
                </a>
            </ul>
        </nav>
        <ul>
            <li>
                <img src="<?=HOME_SITE?>image/compte_vendeur.svg" alt="logo vendeur">
                <?=htmlentities($_SESSION['raison_sociale'])?>
            </li>
        </ul>
    </div>
    <div>
        <ul>

        </ul>
    </div>
</header>
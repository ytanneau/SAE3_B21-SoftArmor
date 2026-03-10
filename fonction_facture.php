<?php
function print_head()
{
    ?>
    <div class="facture-head">
        <h1>Facture</h1>
        <img src="<?= HOME_SITE ?>/image/logo_Alizon_bleu.png" alt="">
    </div>
    <?php
}

function print_carater($vendeur, $adr_vendeur, $client, $adr_client)
{
    ?>
    <div class="facture-carater">
        <div>
            <div><strong>Vendeur</strong></div>
            <div>
                <?= htmlentities($vendeur) ?>
                <br>
                <?= htmlentities($adr_vendeur) ?>
            </div>
        </div>
        <div>
            <div><strong>Vendeur</strong></div>
            <div>
                <?= htmlentities($client) ?>
                <br>
                <?= htmlentities($adr_client) ?>
            </div>
        </div>
    </div>
    <?php
}


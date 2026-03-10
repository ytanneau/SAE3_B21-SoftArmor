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

function print_carater($vendeur, $adr_vendeur, $client, $adr_client, $date)
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
        <div>
            <div><strong>Date</strong></div>
            <div>
                <?= htmlentities($date) ?>
            </div>
        </div>
    </div>
    <?php
}

function print_table($data)
{
    ?>
    <table class="facture-carater">
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Unité</th>
                <th>Prix uniaire HT</th>
                <th>% reduction</th>
                <th>Total reduction</th>
                <th>% TVA</th>
                <th>Total TVA</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $valeur) { ?>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php
}


function facture_vendeur($id_vendeur, $id_commande): void{
    $data = get_elements_commande_vendeur($id_commande, $id_vendeur);
    print($data);
}



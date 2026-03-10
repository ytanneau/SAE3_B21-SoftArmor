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

function sommeHt($data){

}

function sommeTVA($data){
    
}

function sommeTTC($data){
    return sommeHt($data) + sommeTVA($data);
}

function print_table($data)
{
    ?>
    <table class="facture-carater">
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Prix uniaire HT</th>
                <th>% TVA</th>
                <th>Total TVA</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $valeur) { ?>
                <tr>
                    <td><?= htmlentities($valeur['nom_produit']) ?></td>
                    <td><?= htmlentities($valeur['quantite']) ?></td>
                    <td><?= htmlentities($valeur['prix']) ?></td>
                    <td><?= htmlentities($valeur['tva']) ?></td>
                    <td><?= number_format($valeur['quantite'] * $valeur['prix'] * (1 + $valeur['tva'] / 100), 2) ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <div class="facture-carater">

    </div>
    <?php
}

function facture($id_vendeur, $id_commande)
{
    print_head();

    //print_carater();

    $data = get_elements_commande_vendeur($id_commande, $id_vendeur);
    print_table($data);
}

function facture_vendeur($id_commande): void
{
    $pseudo = get_pseudo_commande($_GET['commande']);

    facture($_SESSION['id_compte'], $id_commande);
}
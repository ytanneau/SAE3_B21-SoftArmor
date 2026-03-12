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
            <div><strong>Client</strong></div>
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

function sommeHt($data)
{
    $somme = 0;
    foreach ($data as $valeur) {
        $somme += $valeur['prix'];
    }
    return $somme;
}

function sommeTVA($data)
{
    $somme = 0;
    foreach ($data as $valeur) {
        $somme += $valeur['quantite'] * $valeur['prix'] * (1 + $valeur['tva'] / 100);
    }
    return $somme;
}

function sommeTTC($data)
{
    return sommeHt($data) + sommeTVA($data);
}

function print_table($data)
{
    ?>
    <table class="facture-table">
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Prix uniaire HT</th>
                <th>TVA</th>
                <th>Total TVA</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $valeur) { ?>
                <tr>
                    <td><?= htmlentities($valeur['nom_produit']) ?></td>
                    <td><?= htmlentities($valeur['quantite']) ?></td>
                    <td><?= htmlentities($valeur['prix']) ?> €</td>
                    <td><?= htmlentities($valeur['tva']) ?> %</td>
                    <td><?= number_format($valeur['quantite'] * $valeur['prix'] * (1 + $valeur['tva'] / 100), 2) ?> €</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <table class="facture-toto">
        <tr>
            <td>Total HT</td>
            <td><?= sommeHt($data) ?></td>
        </tr>
        <tr>
            <td>Total TVA</td>
            <td><?= sommeTVA($data) ?></td>
        </tr>
        <tr>
            <td>Total TTC</td>
            <td><?= number_format(sommeTTC($data), 2) ?></td>
        </tr>
    </table>
    <?php
}

function facture($id_vendeur, $id_commande)
{
    print_head();


    $info = get_infos_commande($id_commande, $id_vendeur);
    $adresse = sql_get_adresse($info['id_adresse_vendeur']);
    $adr_vendeur = $adresse['adresse'] . " " . $adresse['ville'] . ", " . $adresse['code_postal'];

    $adresse = sql_get_adresse($info['id_adresse_client']);
    if ($adresse != null) {
        $adr_client = $adresse['adresse'] . " " . $adresse['ville'] . ", " . $adresse['code_postal'];
    } else {
        $adr_client = " ";
    }

    print_carater($info['raison_sociale'], $adr_vendeur, $info['nom_client'] . " " . $info['prenom_client'], $adr_client, $info['date_commande']);


    $data = get_elements_commande_vendeur($id_commande, $id_vendeur);
    print_table($data);
}

function facture_vendeur($id_commande): void
{
    $pseudo = get_pseudo_commande($_GET['commande']);

    facture($_SESSION['id_compte'], $id_commande);
}


function facture_client($id_commande): void
{
    $data = get_elements_commande($id_commande);

    $id_vendeurs = [];
    foreach ($data as $value) {
        if (!in_array($value['id_vendeur'], $id_vendeurs)) {
            array_push($id_vendeurs, $value['id_vendeur']);
        }
    }

    /*$id_adresse = get_infos_commande($id_commande, $id_vendeurs[0])['id_adresse_client'];

    $adresse = sql_get_adresse($id_adresse);
    if ($adresse != null){
        $adr_client = $adresse['adresse'] ." ". $adresse['ville'] .", ". $adresse['code_postal'];
    }
    else{
        $adr_client = " ";
    }*/

    foreach ($id_vendeurs as $id_vendeur) {
        facture($id_vendeur, $id_commande);

        /*print_head();

        $info = get_infos_commande($id_commande, $id_vendeur);
        $adresse = sql_get_adresse($info['id_adresse_vendeur']);
        $adr_vendeur = $adresse['adresse'] ." ". $adresse['ville'] .", ". $adresse['code_postal'];

        print_carater($info['raison_sociale'], $adr_vendeur, $info['nom_client']." ". $info['prenom_client'], $adr_client, $info['date_commande']);

        $data = get_elements_commande_vendeur($id_commande, $id_vendeur);
        print_table($data);*/
        echo "<hr>";
    }

    facture($_SESSION['id_compte'], $id_commande);
}
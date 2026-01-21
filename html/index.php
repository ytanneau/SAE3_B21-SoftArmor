<?php
define('HOME_GIT', "../" );
define('HOME_SITE', "./" );

if (!isset($_SESSION)) {
    session_start();

    if(isset($_SESSION['raison_sociale'])){
        header('location: /vendeur/stock/');
    }
}

require_once (HOME_GIT . '.config.php');
require_once (HOME_GIT . 'fonction_produit.php');
require_once (HOME_GIT . 'fonction_categorie.php');
require_once (HOME_GIT . 'fonction_global.php');

// Nom public, prix, moyenne des notes et informations de l'image de chaque produit
$produit_catalogue = info_produit_accueil();

// Nom public, prix, moyenne des notes et informations de l'image des produits les plus récents
$produit_recent = info_produit_accueil_plus_recent();

// Nom public, prix, moyenne des notes et informations de l'image des produits en réduction
$produit_promotion = info_produit_accueil_promotion();


function afficher_un_produit($row){?>
    <li>
        <a href="/produit/?produit=<?= $row['id_produit'];?>"> 
            <?php
            $prix_normal = $row['prix'] * (1 + $row['tva'] / 100);
            $prix_reduit = $row['prix_actuel'] * (1 + $row['tva'] / 100);
            $reduction = $prix_normal != $prix_reduit;
            $pourcentage = round((1 - ($prix_reduit / $prix_normal) )*100);

                        if($reduction){?>
                        <span class="reduction">%</span>
                        <?php }?>

                        <img  src="<?= $row['url_image'];?>" title="<?= pset($row['titre'])?>" alt="<?= pset($row['alt'])?>">
                        <h3><?= limiter_caracteres($row['nom_public'],50); ?></h3>
                        

                        <div>
                            <?php 

                                if(!isset($row['note_moy'])){
                                    ?><p>Produit Non Noté</p><?php
                                }
                                else{
                                    $moy = $row['note_moy'];
                                    afficher_moyenne_note($moy);
                                }
                            ?>
                        </div>

                        

                        <!-- Affiche que le prix normal s'il n'y a pas de réduction, sinon affiche aussi le prix réduit (et barre le normal)-->
                        <p class="<?=$reduction ? "ancien_prix" : "prix"?>"><?= number_format($prix_normal, 2, ',', '')?> €</p>
                        
                        
                        
                        <?php if ($reduction) { ?>
                            <p class="pourcentage"><?= $reduction ? htmlentities("-$pourcentage%") : ""?></p>
                            <p class="prix"><?= number_format($prix_reduit, 2, ',', ''); ?> €</p>
                        <?php } ?>
                    </a>
                </li>
    <?php }


// fonction d'affichage de produits
// $liste_produits est une liste avec des produits dedans
// $nom_classe_js est une chaine de car qui permet au script JS de fonctionner
// pour rajouter une nouvelle liste => penser à changer le script et ajouter des trucs
function afficher_produits($liste_produits, $nom_classe_js = "") {
    $nom_classe_js = strtr($nom_classe_js, ' ', '_');
    $nom_classe_js = strtr($nom_classe_js, 'É', 'é');
    $nom_classe_js = strtr($nom_classe_js, ' ', '_');
    $nom_classe_js = strtr($nom_classe_js, '&', 'et');
    ?>
    
    <div>
        <button class="fleche gauche <?=$nom_classe_js?>"><img src="image/fleche_droite_blanc.svg"></button>
        <ul class="container <?=$nom_classe_js?>">
            <?php
            // Boucle pour ajouter un produit dans un <li>
            foreach ($liste_produits as $row) {
                afficher_un_produit($row);
            }?>
        </ul>
        <button class="fleche droite <?=$nom_classe_js?>"><img src="image/fleche_droite_blanc.svg"></button>
    </div>
<?php }
$pub = false;
$tabPromotion = get_promotion_banniere();
if($tabPromotion != false){
    $url_banniere = get_url_image($tabPromotion['id_image_banniere'])['url'];
} else {
    $pub = true;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="Page accueil" content="width=device-width, initial-scale=1.0">
    <?php include HOME_SITE . "link_head.php"; ?>
    <title>Alizon - Accueil</title>
</head>
<body class="accueil">
    <?php 
        include HOME_SITE . "header.php";
        include HOME_SITE . "toolbar_categories.php";
    ?>

    <main>
        <!--Produit Banniere au jour (possiblement a faire)-->
        <?php if($pub){?>
        <div>
        
        </div>
        <?php } else {?>
        <div class="banniere">
            <a href="produit/?produit=<?=$tabPromotion['id_produit']?>">
                <img src="<?=HOME_GIT . $url_banniere?>" alt="Banniere de promotion du produit">
            </a>
        </div>
        <?php } ?>
        <!--Vedette de la journée (possiblement a faire)(pour telephone)-->
            <!--Produit Ajoutés Récemment-->
            <h1>Produits ajoutés récemment</h1>
            <?php afficher_produits($produit_recent, "recent")?>
            <hr>
            
            <!-- Produits en réduction -->
            <h1>Produits en promotion</h1>
            <?php afficher_produits($produit_promotion, "reduction")?>
            <hr>

            <!-- Produits de catégories -->
            <?php
            $categories = get_categorie();

            foreach($categories as $cat) {
                $cat = $cat['nom_categorie'];
                $produits_cat = info_produit_accueil_categorie($cat);

                if (count($produits_cat) != 0) {
                    ?>
                    <h1>Produits <?=strtolower($cat)?></h1>
                    <?php afficher_produits($produits_cat, strtolower($cat))?>
                    <hr>
                <?php }
            } ?>

            <!-- Tous les produits du catalogue -->
            <h1>Produits du catalogue</h1>
            <?php afficher_produits($produit_catalogue, "catalogue")?>

            <!-- Navigation (pour teléphone)
            <nav>
                <a href=""><img src="image/home.svg" title="Acceder à la page d'Accueil" alt="logo page d'accueil"></a>
                <a href="/panier"><img src="image/panier.svg" title="Acceder au Panier" alt="logo page panier"></a>
            
                <a href="#"><img src="image/favori.svg" title="Acceder aux favoris" alt="logo page favoris"></a>
                <a href="#"><img src="image/notification.svg" title="Acceder aux notifications" alt="logo page notifications"></a>
            </nav>  
            -->
    </main>

    <footer>
        <?php //include HOME_SITE . 'footer.php' ?>
    </footer>
    <script src="script.js?t=2"></script>

    <script>
        

        <?php

        foreach($categories as $cat) {
            $cat = strtolower($cat['nom_categorie']);
            $cat = strtr($cat, 'É', 'é');
            $produits_cat = info_produit_accueil_categorie($cat);
            $cat = strtr($cat, ' ', '_');
            $cat = strtr($cat, '&', 'et');
            
            
            if (count($produits_cat) != 0) {
                echo "setCaroussel('$cat');\n";
            }
        }
    ?>
    </script>

    <?php include HOME_SITE . "footer.php" ?>
</body>
</html>
<?php
// Inclusion du fichier de configuration
define('HOME_GIT', '../../');
define('HOME_SITE', '../');

if (!isset($_SESSION)) {
    session_start();
    
    if(isset($_SESSION['raison_sociale'])){
        header('location: /vendeur/stock/');
    }
}

require_once (HOME_GIT . '.config.php');
require_once (HOME_GIT . 'fonction_avis.php');
require_once (HOME_GIT . 'fonction_produit.php');
require_once (HOME_GIT . 'fonction_global.php');
require_once (HOME_GIT . 'fonction_panier.php');

if (!isset($_GET['produit']) || !is_numeric($_GET['produit'])) {
    die("ID du produit invalide.");
}

$id_produit = htmlentities($_GET['produit']);
$recherche = trim(htmlentities($_GET['recherche'] ?? ''));

// La flèche de retour redirige le client vers la page de recherche s'il vient de là
$lien_retour = empty($recherche) ? HOME_SITE : (HOME_SITE . 'recherche/?recherche=' . urlencode($recherche));

try {
    $produit = detail_produit_image($id_produit);
    
    if (!$produit) {
        die("Produit introuvable.");
    }

    $note = note_produit($id_produit)['note_moy'];

    // Récupérer les avis
    $liste_avis = avis_client_produit($_GET['produit']);
} catch (PDOException $e) {
    die("Erreur lors de la récupération du produit : " . $e->getMessage());
}

// Preparer le prix formaté
$formatted_prix_ht = '';
$formatted_prix_ttc = '';

if (isset($produit['prix'])) {
    if (is_numeric($produit['prix'])) {
        $formatted_prix_ht = number_format($produit['prix'], 2, ',', ' ') . ' €';
        $formatted_prix_ttc = number_format($produit['prix'] * (1 + $produit['tva'] / 100), 2, ',', ' ') . ' €';
    } else {
        $formatted_prix_ht = htmlentities($produit['prix'] ?? '');
        $formatted_prix_ttc = $formatted_prix_ht;
    }
}

if (isset($produit['prix_actuel']) && ($produit['prix_actuel'] != $produit['prix'])) {
    $formatted_prix_bas = number_format($produit['prix_actuel'] * (1 + $produit['tva'] / 100), 2, ',', ' ') . '€';
}

if (isset($_SESSION['id_compte'])) {
    $id_cli = $_SESSION['id_compte'];
}

if (isset($_POST['quantite'])) {
    $qte = $_POST['quantite'];
    $id_prod = $_GET['produit'];

    if (isset($_SESSION['id_compte'])) {
        ajouter_panier($id_prod,$id_cli,$qte);

    } else {
        ajouter_panier_visiteur($id_prod, $qte);

    }

    header('Location:' . HOME_SITE . 'panier');
}

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <?php include HOME_SITE . "link_head.php"; ?>
    <title>Alizon - <?= htmlentities($produit['nom_public'] ?? 'Produit') ?></title>
</head>
<body class="produit">
    <?php include HOME_SITE . "header.php"; ?>

    <script>
        function changer(val) {
            const inputQuantite = document.getElementById('input_quantite');
            let value = Number(inputQuantite.value);
            if (value + val > 0 && value + val < 50000) {
                inputQuantite.stepUp(val);
            }
        }
    </script>

    <?php
        $img_p = get_image($produit['id_image_principale']);
        $img_1 = get_image($produit['id_image1']);
        $img_2 = get_image($produit['id_image2']);
    ?>
    
    <main>
        <div class="gauche">
            <a href="<?= $lien_retour ?>"><img src="../image/retour.svg"></a>

            <section class="detail_produit">
                <!-- Présentation du produit -->
                <article>
                    <div>
                        <!-- Image principale -->
                        <div>
                            <img src="<?= HOME_SITE . $img_p['url_image'] ?>" title="<?= $img_p['titre'] ?>" alt="<?= $img_p['alt'] ?>">
                        </div>

                        <!-- Images facultatives -->
                        <div>
                            <?php 
                                if (!empty($img1_url)) { ?>
                                    <img src="<?= HOME_SITE . $img1['url_image'] ?>" title="<?= $img1['titre'] ?>" alt="<?= $img1['alt'] ?>">
                                <?php }

                                if (!empty($img2_url)) { ?>
                                    <img src="<?= HOME_SITE . $img2['url_image'] ?>" title="<?= $img2['titre'] ?>" alt="<?= $img2['alt'] ?>">
                                <?php } 
                            ?>
                        </div>
                    </div>
                    
                    <!-- Détails du produit -->
                    <div>
                        <h1><?= htmlentities($produit['nom_public'] ?? '') ?></h1>

                        <p>
                            <em>par <?= htmlentities($produit['raison_sociale'] ?? '') ?></em>
                        </p>

                        <div>
                            <div class="etoiles">
                                <?php if (count($liste_avis) > 0) {
                                    afficher_moyenne_note($note);
                                } else {
                                    echo 'Produit non noté';
                                } ?>
                            </div>
                            
                            <!-- Nombre d'avis -->
                            <a href="#avis"><?= count($liste_avis) > 0 ? count($liste_avis) . ' avis' : '' ?></a>
                        </div>
                        
                        <!-- Description du produit -->
                        <p>
                            <?= nl2br(htmlentities($produit['description'] ?? '')) ?>
                        </p>
                    </div>
                </article>


                <!-- Description détaillée -->
                <article>
                    <?php
                        $description_detaillee = nl2br(htmlentities($produit['description_detaillee'] ?? ''));

                        if (!empty($description_detaillee)) { ?>
                            <h2>Description détaillée</h2>
                            <p>
                                <?= $description_detaillee ?>
                            </p>
                        <?php } 
                    ?>
                </article>

                <hr>
            </section>

            <!-- Section des avis -->
            <section id="avis">
                <!-- Rajouter le nombre -->
                <h2>Avis (<?= count($liste_avis) ?>)</h2>

                <a class="bouton" href="../avis/?produit=<?= urlencode($produit['id_produit']) ?>">Ajouter un avis</a>

                <ul class="liste_avis">
                    <?php foreach ($liste_avis as $avis) { ?>
                        <li>
                            <!-- Informations du produit -->
                            <div>
                                <div>
                                    <?php if (isset($avis['profile'])) {?>
                                        <img height="40px" width="40px" src="../ressources/27_1.png">
                                    <?php
                                        } else {?>
                                        <img height="40px" width="40px" src="<?=HOME_SITE . 'image/compte.svg'?>">
                                    <?php } ?>

                                    <div class="etoiles">
                                        <?= afficher_moyenne_note(htmlentities($avis['note'] ?? '')) ?>
                                    </div>
                                </div>

                                <div>
                                    <h3><?= htmlentities($avis['titre'] ?? '') ?></h3>
                                    <p><?= htmlentities($avis['commentaire'] ?? '') ?></p>
                                    <p><?= 'Avis rédigé par ' . htmlentities($avis['pseudo'] ?? '') .  ' le ' . date('d/m/Y', strtotime(htmlentities($avis['date_avis'] ?? ''))) ?></p>
                                </div>
                            </div>

                            <!-- Afficher le bouton signaler seulement si l'avis n'est pas à moi, et que je ne l'ai pas déjà signalé -->
                            <?php if (isset($id_cli)) {
                                $est_mon_avis = avis_fait_par($avis['id_avis'], $id_cli);

                                if (!avis_est_signale($avis['id_avis'], $id_cli) && !$est_mon_avis) { ?>
                                    <button class="bouton_signalement" data-avis="<?=$avis['id_avis']?>">
                                        Signaler
                                    </button>
                                <?php } else if (!$est_mon_avis) { ?>
                                    <button class="bouton_signalement" disabled>
                                        Signalé
                                    </button>
                                <?php } ?>
                            <?php } ?>

                            <!-- Afficher l'image de l'avis si elle existe -->
                            <?php if (isset($avis['url_image'])) { ?>
                                <img src="<?= HOME_SITE . $avis['url_image'] ?>" title="<?= $avis['alt_image'] ?>" alt="<?= $avis['alt_image'] ?>">
                            <?php } ?>

                        </li>
                    <?php } ?>
                </ul>

                <div id="modal_signalement" class="modal">
                    <div class="modal_content">
                        <div class="titre">
                            <h3>Signaler cet avis</h3>
                            <span class="fermer_modal">&times;</span>
                        </div>
                        
                        <form id="form_signalement" action="" method="post">
                            <input type="hidden" name="id_avis" id="id_avis">

                            <label for="input_email">Adresse e-mail (facultative)</label>
                            <input type="text" name="email" id="input_email" placeholder="xyz@domaine.fr">

                            <label for="select_raison">Raison</label>
                            <select name="raison" id="select_raison">
                                <option value="Contenu offensant">Contenu offensant</option>
                                <option value="Contenu mensonger">Contenu mensonger</option>
                                <option value="Contenu illicite">Contenu illicite</option>
                            </select>
                            <p class="error"></p>

                            <div class="boutons">
                                <button type="reset" class="fermer_modal">Annuler</button>
                                <button type="submit">Signaler</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="snackbar" class="snackbar"></div>
            </section>
        </div>
                    
        <!-- Achat du produit  -->

        <?php if (isset($_SESSION['logged_in'])) {
            $page = "../achat";
        } else {
            $page = HOME_SITE . "compte/connexion";
        } ?>
        
        <div>
            <aside>
                <div>
                    <span>Prix HT</span> 
                    <span class="prix HT">
                        <?= $formatted_prix_ht ?>
                    </span>
                </div>

                <div>
                    <span>Prix TTC</span> 
                    <span class="prix <?=isset($formatted_prix_bas) ? 'HT ancien_prix' : ''?>">
                        <?= $formatted_prix_ttc ?>
                    </span>
                </div>

                <?php if (isset($formatted_prix_bas)) { ?>
                    <div>
                        <span>Prix réduit actuel</span> 
                        <span class="prix">
                            <?= $formatted_prix_bas ?>
                        </span>
                    </div>
                <?php } ?>

                <form action="" method="post">
                    <div>
                        <label for="quantite">Quantité</label>
            
                        <span class="input_quantite">
                            <input type="button" onclick="changer(-1)" value="-"><input id="input_quantite" type="number" name="quantite" min=1 value=1 max=50000 pattern="\d*" required><input type="button" onclick="changer(1)" value="+">
                        </span>
                    </div> 
                    <input class="bouton" type="submit" value="Ajouter au panier">

                    <a class="bouton" href="<?=$page?>/index.php?produit=<?= urlencode($produit['id_produit']) ?>">Acheter cet article</a>
                </form>
            </aside>
        </div>
        
    </main>
    
    <?php include HOME_SITE . "footer.php" ?>

    <script>
        const modal = document.getElementById("modal_signalement");
        const formSignalement = document.getElementById("form_signalement");
        const snackbar = document.getElementById("snackbar");
        const inputId = document.getElementById("id_avis");

        // Afficher le modal en cliquant sur l'icône signaler
        document.querySelectorAll(".bouton_signalement").forEach(btn => {
            btn.addEventListener("click", () => {
                inputId.value = btn.dataset.avis;
                modal.style.display = "block";
            });
        });

        // Fermer le modal
        document.querySelectorAll(".fermer_modal").forEach(element => {
            element.addEventListener("click", () => {
                modal.style.display = "none";
            });
        });

        // Fermer le modal si on clique ailleurs
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        formSignalement.addEventListener("submit", async (e) => {
            e.preventDefault();

            // Récupérer les données du formulaire
            const data = new FormData(formSignalement);

            // Envoyer les données du formulaire en JSON à une autre page
            const res = await fetch("../signalement.php", {
                method: "POST",
                body: data
            });

            const json = await res.json();

            modal.style.display = "none";
            showSnackbar(json.message);

            if (json.success) {
                // Désactiver le bouton de signalement
                const btn = document.querySelector(
                    `.bouton_signalement[data-avis="${data.get('id_avis')}"]`
                );

                btn.textContent = "Signalé";
                btn.disabled = true;
            }
        });

        // Montrer la snackbar
        function showSnackbar(msg) {
            snackbar.textContent = msg;
            snackbar.className = "show";

            setTimeout(() => {
                snackbar.className = "";
            }, 3000);
        }

    </script>
</body>
</html>

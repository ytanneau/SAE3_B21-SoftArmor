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
require_once (HOME_GIT . 'fonction_categorie.php');
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

$id_cli = $_SESSION['id_compte'] ?? null;


if (isset($_POST['quantite'])) {
    $qte = $_POST['quantite'];
    $id_prod = $_GET['produit'];

    // Si le client a fait "Ajouter au panier"
    if (isset($_POST['panier'])) {

        if (isset($_SESSION['id_compte'])) {
            ajouter_panier($id_prod,$id_cli,$qte);

        } else {
            ajouter_panier_visiteur($id_prod, $qte);

            }
        header('Location:' . HOME_SITE . 'panier');

    // Sinon, s'il a fait "acheter le produit"
    } elseif (isset($_POST['achat'])) {
        if (isset($_SESSION['id_compte'])) {
            header("Location: ../achat/?produit=$id_prod&nb=$qte");

        } else {
            header("Location:../compte/connexion?produit=$id_prod");
        }
    }

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
    <?php 
        include HOME_SITE . "header.php";
        include HOME_SITE . "toolbar_categories.php";
    ?>

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

                                    <!-- Afficher le bouton signaler seulement si l'avis n'est pas à moi, et que je ne l'ai pas déjà signalé -->
                                    <?php
                                    $est_mon_avis = avis_fait_par($avis['id_avis'], $id_cli);
    
                                    if (!avis_est_signale($avis['id_avis'], $id_cli) && !$est_mon_avis) { ?>
                                        <button class="bouton_signalement" data-avis="<?=$avis['id_avis']?>">
                                            <img class="icon" src="<?= HOME_SITE . "image/reporter.svg" ?>" title="Signaler cet avis">
                                        </button>
                                    <?php } else if (!$est_mon_avis) { ?>
                                        <button class="bouton_signalement" disabled>
                                            <img class="icon" src="<?= HOME_SITE . "image/reported_rouge.svg" ?>" title="Avis signalé">
                                        </button>
                                    <?php } ?>
                                </div>

                                <div>
                                    <h3><?= htmlentities($avis['titre'] ?? '') ?></h3>
                                    <p><?= htmlentities($avis['commentaire'] ?? '') ?></p>
                                    <p><?= 'Avis rédigé par ' . htmlentities($avis['pseudo'] ?? '') .  ' le ' . date('d/m/Y', strtotime(htmlentities($avis['date_avis'] ?? ''))) ?></p>
                                </div>
                            </div>

                            <!-- Afficher l'image de l'avis si elle existe -->
                            <?php if (isset($avis['url_image'])) { ?>
                                <img src="<?= HOME_SITE . $avis['url_image'] ?>" title="<?= $avis['alt_image'] ?>" alt="<?= $avis['alt_image'] ?>">
                            <?php } ?>

                        </li>

                        <?php include HOME_SITE . 'vendeur/avis/reponse_vendeur.php'; ?>
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
                            <input type="hidden" name="id_reponse" id="id_reponse">

                            <?php if (!isset($id_cli)) { ?>
                                <label for="input_email">Adresse e-mail</label>
                                <input type="email" name="email" id="input_email" placeholder="xyz@domaine.fr">
                                <p class="error" id="error_email">Le format est invalide</p>
                            <?php } ?>

                            <label for="select_raison">Raison</label>
                            <select name="raison" id="select_raison">
                                <option value="">Sélectionnez une raison</option>
                                <option value="offensant">Contenu offensant</option>
                                <option value="hors-sujet">Contenu hors-sujet</option>
                                <option value="illicite">Contenu illicite</option>
                            </select>
                            <p class="error" id="error_raison">Veuillez indiquer la raison du signalement</p>

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
                    <input class="bouton" type="submit" name="panier" value="Ajouter au panier" onclick="submit()">

                    <input class="achat" type="submit" name="achat" value="Acheter cet article">
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
        const inputEmail = document.getElementById("input_email");
        const inputIdReponseSignalement = document.getElementById("id_reponse");

        const estVisiteur = (inputEmail != null);

        const pErrorEmail = document.getElementById("error_email");
        const pErrorRaison = document.getElementById("error_raison");


        // Afficher le modal en cliquant sur l'icône signaler
        document.querySelectorAll(".bouton_signalement").forEach(btn => {
            btn.addEventListener("click", () => {
                inputId.value = btn.dataset.avis;
                inputIdReponseSignalement.value = "";

                modal.style.display = "block";

                // Empêcher le scroll tant que le modal est ouvert
                document.body.style.overflowY = "hidden";
            });
        });

        // Afficher le modal en cliquant sur l'icône signaler réponse
        document.querySelectorAll(".bouton_signalement_reponse").forEach(btn => {
            btn.addEventListener("click", () => {
                inputIdReponseSignalement.value = btn.dataset.reponse;
                inputId.value = "";

                // Afficher le modal
                modal.style.display = "block";

                // Empêcher le scroll tant que le modal est ouvert
                document.body.style.overflowY = "hidden";
            });
        });

        // Fermer le modal
        document.querySelectorAll(".fermer_modal").forEach(element => {
            element.addEventListener("click", () => {
                modal.style.display = "none";
                document.body.style.overflowY = "auto";
            });
        });

        // Fermer le modal si on clique ailleurs
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
                document.body.style.overflowY = "auto";
            }
        }

        
        // Confirmation du signalement
        formSignalement.addEventListener("submit", async (e) => {
            e.preventDefault();

            // Récupérer les données du formulaire
            const data = new FormData(formSignalement);

            let raisonInvalide = data.get("raison") == "" || data.get("raison") == null;
            let emailInvalide = estVisiteur && !emailValide(data.get("email"));

            pErrorRaison.style.visibility = raisonInvalide ? "visible" : "hidden";

            if (estVisiteur) {
                if (emailInvalide) {
                    console.log("email invalide");
                    pErrorEmail.textContent = (data.get("email").trim() == "") ? 
                        "Veuillez renseigner ce champ" : 
                        "Le format est invalide";
                }

                pErrorEmail.style.visibility = emailInvalide ? "visible" : "hidden";
            }

            if (raisonInvalide || emailInvalide) {
                // On ne continue pas le traitement s'il manque des informations
                return;
            }

            // Envoyer les données du formulaire en JSON à une autre page
            const res = await fetch("../signalement.php", {
                method: "POST",
                body: data
            });

            const json = await res.json();

            // Fermer le modal
            modal.style.display = "none";
            document.body.style.overflowY = "auto";

            // Afficher la snackbar
            showSnackbar(json.message, json.success ? "success" : "error");

            if (json.success) {
                console.log(json.success);

                let selector;

                if (data.get('id_avis') != null) {
                    selector = `.bouton_signalement[data-avis="${data.get('id_avis')}"]`
                } else {
                    selector = `.bouton_signalement_reponse[data-reponse="${data.get('id_reponse')}"]`
                }

                // Désactiver le bouton de signalement
                const btn = document.querySelector(selector);
                
                // Changer l'image du bouton
                const img = document.querySelector(`${selector} img`);
                img.src = "../image/reported_rouge.svg";
            }
        });

        function emailValide(email) {
            let regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            return email != null && regex.test(email);
        }

        // Montrer la snackbar pendant 5 secondes
        function showSnackbar(msg, mode) {
            snackbar.textContent = msg;
            snackbar.className = `show ${mode}`;

            setTimeout(() => {
                snackbar.className = "";
            }, 5000);
        }

    </script>
</body>
</html>

<?php
    define('HOME_GIT', '../../../');
    define('HOME_SITE', '../../');

    if (!isset($_SESSION)) {
        session_start();
    }

    // Si je suis connecté mais pas en tant que vendeur, retour à l'accueil client
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && !isset($_SESSION['raison_sociale'])) {
        header('location: ' . HOME_SITE);
        exit;

    // Sinon si je ne suis pas connecté, retour à la page connexion vendeur
    } else if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] === false) {
        header('location: ../');
        exit;
    }

    $id_vendeur = $_SESSION['id_compte'];
    $id_produit = htmlentities(trim($_GET['produit'] ?? ''));

    // Rediriger à la page de stock
    if (empty($id_produit)) {
        header('location: ../stock');
        exit;
    }

    require_once HOME_GIT . 'fonction_produit.php';
    require_once HOME_GIT . 'fonction_avis.php';


    $data = avis_client_produit($_GET['produit']);
    $produit = detail_produit($_GET['produit']);

    $image_principale = get_url_image($produit['id_image_principale']);
    $image1 = get_url_image($produit['id_image1']);
    $image2 = get_url_image($produit['id_image2']);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alizon - Avis</title>
<?php
    require_once HOME_SITE . 'link_head.php';
?>
</head>

<body id="avis_vendeur">
    <?php require_once HOME_SITE . 'vendeur/header.php'; ?>
    
    <main>

    <a href="../produit?produit=<?=$_GET['produit']?>"><img src="../../image/retour.svg" class = "fleche_produit_arriere"></a>

    <?php if ($produit == NULL) { ?>
        <h1>Désolé, ce produit n'existe pas</h1>
    <?php } ?>

    <article>
        <div>
            <img src="<?=HOME_SITE . htmlentities($image_principale['url'])?>" alt="<?=htmlentities($image_principale['alt'])?>" title="<?=htmlentities($image_principale['titre'])?>">
        </div>
        <div>
            <?=htmlentities($produit['nom_public'])?>
            
            <?php if ($data != null) { ?>
                <br>Note moyenne : <?=htmlentities(round($produit['note_moy'] ?? 0, 1))?></br>
                <?php afficher_moyenne_note($produit['note_moy']);
            } else {
                echo "<br>Il n'y a pas d'avis pour ce produit";
            } ?>

            <br>Nombre d'avis : <?=htmlentities($produit['nb_avis'] ?? "0")?>
        </div>
    </article>
    <section>
        <?php if ($data != null) { ?>
            <ul class="liste_avis">
                <?php foreach ($data as $avis) { 
                    $image_pp = get_url_image($avis['id_image_pp']);

                    if (!$image_pp) {
                        $image_pp = [
                            "url" => "image/compte.svg",
                            "titre" => "Photo de profil",
                            "alt" => "Photo de profil"
                        ];
                    }
                    ?>
                    <li>
                        <div>
                            <div>
                                <img class="image_pp" src="<?= HOME_SITE . $image_pp['url'] ?>" title="<?= $image_pp['titre'] ?>" alt="<?= $image_pp['alt'] ?>">
                                <div class="etoiles">
                                    <?= afficher_moyenne_note(htmlentities($avis['note'] ?? '')) ?>
                                </div>

                                <!-- Afficher le bouton signaler seulement si l'avis n'est pas à moi, et que je ne l'ai pas déjà signalé -->
                                <?php if (!avis_est_signale($avis['id_avis'], $id_vendeur)) { ?>
                                    <button class="bouton_signalement" data-avis="<?=$avis['id_avis']?>">
                                        <img class="icon" src="<?= HOME_SITE . "image/reporter.svg" ?>" title="Signaler cet avis">
                                    </button>
                                <?php } else { ?>
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

                        <?php if (isset($avis['url_image'])) { ?>
                            <a href="<?=HOME_SITE . $avis['url_image']?>" target="_blank">
                                <img src="<?= HOME_SITE . $avis['url_image'] ?>" title="<?= $avis['titre_image'] ?>" alt="<?= $avis['alt_image'] ?>">
                            </a>
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
        <?php } ?>
    </section>

    </main>

    <?php include HOME_SITE . "footer.php" ?>

    <script>
        const modal = document.getElementById("modal_signalement");
        const formSignalement = document.getElementById("form_signalement");
        const snackbar = document.getElementById("snackbar");

        const inputId = document.getElementById("id_avis");
        const inputEmail = document.getElementById("input_email");
        const estVisiteur = (inputEmail != null);

        const pErrorEmail = document.getElementById("error_email");
        const pErrorRaison = document.getElementById("error_raison");


        // Afficher le modal en cliquant sur l'icône signaler
        document.querySelectorAll(".bouton_signalement").forEach(btn => {
            btn.addEventListener("click", () => {
                inputId.value = btn.dataset.avis;
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

            pErrorRaison.style.visibility = raisonInvalide ? "visible" : "hidden";

            if (raisonInvalide) {
                // On ne continue pas le traitement s'il manque des informations
                return;
            }

            // Envoyer les données du formulaire en JSON à une autre page
            const res = await fetch("../../signalement.php", {
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

                // Désactiver le bouton de signalement
                const btn = document.querySelector(
                    `.bouton_signalement[data-avis="${data.get('id_avis')}"]`
                );
                btn.disabled = true;
                
                // Changer l'image du bouton
                const img = document.querySelector(`.bouton_signalement[data-avis="${data.get('id_avis')}"] img`);
                img.src = "../../image/reported_rouge.svg";
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
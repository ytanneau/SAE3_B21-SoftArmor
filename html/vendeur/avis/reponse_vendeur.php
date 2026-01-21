<?php 
    $reponse = get_reponse($avis['id_avis']);

    // Si l'avis a une réponse, l'afficher
    if (!empty($reponse)) { 
        $image_pp = get_url_image($reponse['id_image_profil']);

        if (!$image_pp) {
            $image_pp = [
                "url" => "image/compte_vendeur.svg",
                "titre" => "Photo de profil",
                "alt" => "Photo de profil"
            ];
        } ?>

        <li class="reponse">
            <div>
                <div>
                    <img class="image_pp" src="<?= HOME_SITE . $image_pp['url'] ?>" title="<?= $image_pp['titre'] ?>" alt="<?= $image_pp['alt'] ?>">

                    <!-- Boutons spécifiques aux vendeurs -->
                    <?php if (isset($id_vendeur)) { ?>
                        <form action="" method="post" style="display: flex">
                            <input type="hidden" name="id_reponse" value="<?=$reponse['id_reponse']?>">

                            <button class="bouton_supprimer">
                                <img class="icon" src="<?= HOME_SITE . "image/supprimer.svg" ?>" title="Supprimer cette réponse">
                            </button>
                        </form>

                        <button class="bouton_modifier" data-avis="<?=$avis['id_avis']?>" data-reponse="<?=$reponse['id_reponse']?>">
                            <img class="icon" src="<?= HOME_SITE . "image/modifier.svg" ?>" title="Modifier cette réponse">
                        </button>

                    <!-- Boutons spécifiques aux clients et visiteurs -->
                    <?php } else { 
                        if (!reponse_est_signalee($reponse['id_reponse'], $id_compte)) { ?>
                            <button class="bouton_signalement_reponse" data-reponse="<?=$reponse['id_reponse']?>">
                                <img class="icon" src="<?= HOME_SITE . "image/reporter.svg" ?>" title="Signaler cette réponse">
                            </button>
                        <?php } else { ?>
                            <button class="bouton_signalement_reponse" disabled>
                                <img class="icon" src="<?= HOME_SITE . "image/reported_rouge.svg" ?>" title="Avis signalé">
                            </button>
                        <?php } ?>
                    <?php } ?>
                </div>

                <div>
                    <p><?= htmlentities($reponse['reponse'] ?? '') ?></p>
                    <p>
                        <?php 
                        $date_reponse = date('d/m/Y', strtotime(htmlentities($reponse['date_reponse'] ?? '')));
                        $date_modification = date('d/m/Y', strtotime(htmlentities($reponse['date_modification'] ?? '')));
                        
                        echo 'Réponse de ' . $reponse['raison_sociale'] . 
                            ' le ' . $date_reponse . 
                            (isset($reponse['date_modification']) ? " (modifiée le $date_modification)" : "") ?></p>
                </div>
            </div>
        </li>
    <?php } 
?>
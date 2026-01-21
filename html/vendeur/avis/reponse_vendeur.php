<?php 
    $reponse = get_reponse($avis['id_avis']);
    $image_pp = get_url_image($reponse['id_image_profil']);

    if (!$image_pp) {
        $image_pp = [
            "url" => "image/compte_vendeur.svg",
            "titre" => "Photo de profil",
            "alt" => "Photo de profil"
        ];
    }

    // Si l'avis a une réponse, l'afficher
    if (!empty($reponse)) { ?>
        <li class="reponse">
            <div>
                <div>
                    <img class="image_pp" src="<?= HOME_SITE . $image_pp['url'] ?>" title="<?= $image_pp['titre'] ?>" alt="<?= $image_pp['alt'] ?>">

                    <!-- Bouton supprimer -->
                    <button class="bouton_supprimer" data-reponse="<?=$reponse['id_reponse']?>">
                        <img class="icon" src="<?= HOME_SITE . "image/supprimer.svg" ?>" title="Supprimer cette réponse">
                    </button>
                </div>

                <div>
                    <p><?= htmlentities($reponse['reponse'] ?? '') ?></p>
                    <p><?= 'Réponse rédigée le ' . date('d/m/Y', strtotime(htmlentities($reponse['date_reponse'] ?? ''))) ?></p>
                </div>
            </div>
        </li>
    <?php } 
?>
<?php
    $categories = get_categorie_parent();
?>

<div class="toolbar" id="categories">
    <ul>
        <?php foreach ($categories as $cat) { ?>
            <li>
                <?php
                $sous_cats = get_sous_categorie($cat['nom_categorie']);
                $chemin = HOME_SITE . 'recherche/?categorie=' . urlencode($cat['nom_categorie']);
        
                // Si sous-catégories, menu déroulant
                if (!empty($sous_cats)) { ?>
                    <div class="dropdown">
                        <a class="dropbown-button" href="<?= $chemin ?>"><?= $cat['nom_categorie'] ?></a>
        
                        <div class="dropdown-content">
                            <?php foreach ($sous_cats as $sous_cat) { 
                                $chemin_sous_cat = 'recherche/?categorie=' . urlencode($sous_cat['nom_categorie']); ?>
                            
                                <a href="<?= $chemin_sous_cat ?>"><?= $sous_cat['nom_categorie'] ?></a>
                            <?php } ?>
                        </div>
                    </div>
                
                <!-- Si pas de sous-catégorie, simple lien -->
                <?php } else { ?>
                    <a href="<?= $chemin ?>"><?= $cat['nom_categorie'] ?></a>
                <?php } ?>
            </li>
        <?php } ?>
    </ul>
</div>

<script>
    const dropdownCategorie = document.getElementById
</script>
<?php $images = HOME_SITE . "image/" ?>

<footer id="footer_client" class="footer">
  <div class="footer-top">

    <div class="footer-logos">
      <a href= <?= HOME_SITE?> >
        <img src="<?= $images . 'Alizon_blanc.png' ?>" alt="Logo Alizon" class="logo">
      </a>
      <a href= <?= HOME_SITE . "vendeur/"?> >
        <img src="<?= $images ?>Alizon_vendeur_blanc.png" alt="Alizon vendeur" class="logo">
      </a>
    </div>

    <div class="footer-columns">
      <div class="footer-column">
        <h4>Connexion</h4>
        <?php if(isset($_SESSION['logged_in'])){?>
        <a href=<?= HOME_SITE . 'deconnexion/'?>></a>
        <?php } else {?>
        <a href=<?= HOME_SITE . 'vendeur/' ?>>Compte vendeur</a>
        <a href=<?= HOME_SITE . 'compte/connexion' ?>>Compte client</a>
        <?php } ?>
      </div>

      <div class="footer-column">
        <h4>Inscription</h4>
        <a href=<?= HOME_SITE . 'vendeur/inscription' ?>>Compte vendeur</a>
        <a href=<?= HOME_SITE . 'compte/inscription' ?>>Compte client</a>
      </div>

      <div class="footer-column">
        <h4>Des Questions ?</h4>
        <a href="#">Aide</a>
        <a href="#">Lien pratique</a>
      </div>

      <div class="footer-column">
        <h4>A propos d'Alizon</h4>
        <a href="#">Qui sommes nous ?</a>
        <a href="#">Plan du site</a>
      </div>
    </div>

    <div class="footer-socials">
      <a href="#"><img src="<?= $images ?>x_blanc.png" alt="X"></a>
      <a href="#"><img src="<?= $images ?>instagram-blanc.png" alt="Instagram"></a>
      <a href="#"><img src="<?= $images ?>linkedin-blanc.png" alt="LinkedIn"></a>
    </div>

  </div>

  <div class="footer-bottom">
    <div class="footer-links">
      <a href="#">Vos données personnelles</a>
      <a href="#">Cookies</a>
      <a href="#">Mentions légales</a>
      <a href="#">Conditions générales de vente</a>
    </div>

    <p>© 2025, alizon.com</p>
  </div>
</footer>

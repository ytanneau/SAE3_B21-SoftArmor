<?php $images = HOME_SITE . "image/" ?>

<footer id="footer_client" class="footer">
  <div class="footer-top">

    <div class="footer-logos">
      <a href= <?= HOME_SITE?> >
        <img src="<?= $images . 'Alizon_blanc.png' ?>" alt="Logo Alizon" class="logo">
      </a>
    </div>

    <div class="footer-columns">
      <div class="footer-column">
        <h4>Connexion</h4>
        <a href=<?= HOME_SITE . 'vendeur/' ?>>Compte vendeur</a>
        <a href=<?= HOME_SITE . 'compte/connexion' ?>>Compte client</a>
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
      <a href="#"><img src="<?= $image ?>/x-blanc.svg" alt="X"></a>
      <a href="#"><img src="<?= $image ?>/instagram-blanc.svg" alt="Instagram"></a>
      <a href="#"><img src="<?= $image ?>/facebook-blanc.svg" alt="Facebook"></a>
      <a href="#"><img src="<?= $image ?>/youtube-blanc.svg" alt="YouTube"></a>
      <a href="#"><img src="<?= $image ?>/tiktok-blanc.svg" alt="TikTok"></a>
      <a href="#"><img src="<?= $image ?>/linkedin-blanc.svg" alt="LinkedIn"></a>
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

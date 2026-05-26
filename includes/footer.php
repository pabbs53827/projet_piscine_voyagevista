<?php
$current_year = date('Y');
?>

<footer>
  <div class="footer-container">
    <!-- Footer Grid -->
    <div class="footer-grid">
      <!-- About Section -->
      <div class="footer-section">
        <h4>À Propos de VoyageVista</h4>
        <p>
          Découvrez le monde avec VoyageVista, votre plateforme de voyage premium. 
          Des destinations exotiques aux expériences inoubliables, nous vous aidons à créer 
          les meilleurs souvenirs de vacances.
        </p>
        <div class="social-links">
          <a href="#" class="social-link" title="Facebook">f</a>
          <a href="#" class="social-link" title="Instagram">📷</a>
          <a href="#" class="social-link" title="Twitter">𝕏</a>
          <a href="#" class="social-link" title="LinkedIn">in</a>
        </div>
      </div>

      <!-- Explore Section -->
      <div class="footer-section">
        <h4>Explorer</h4>
        <ul class="footer-links">
          <li><a href="#destinations">Destinations Populaires</a></li>
          <li><a href="#experiences">Expériences Uniques</a></li>
          <li><a href="#activities">Activités</a></li>
          <li><a href="#">Offres Spéciales</a></li>
          <li><a href="#">Blog Voyage</a></li>
        </ul>
      </div>

      <!-- Support Section -->
      <div class="footer-section">
        <h4>Support</h4>
        <ul class="footer-links">
          <li><a href="#">Centre d'Aide</a></li>
          <li><a href="#">Nous Contacter</a></li>
          <li><a href="#">FAQ</a></li>
          <li><a href="#">Conditions d'Utilisation</a></li>
          <li><a href="#">Politique de Confidentialité</a></li>
        </ul>
      </div>

      <!-- Contact Section -->
      <div class="footer-section">
        <h4>Contact</h4>
        <p>
          📍 123 Avenue du Voyage<br>
          75008 Paris, France<br><br>
          📞 +33 1 23 45 67 89<br>
          ✉️ info@voyagevista.com
        </p>
        <p style="font-size: 0.85rem; margin-top: 1rem;">
          Disponible 24h/24 - Service client premium
        </p>
      </div>
    </div>

    <!-- Footer Bottom -->
    <div class="footer-bottom">
      <div>&copy; <?php echo $current_year; ?> VoyageVista. Tous droits réservés.</div>
      <ul class="footer-legal">
        <li><a href="#">Politique de Confidentialité</a></li>
        <li><a href="#">Mentions Légales</a></li>
        <li><a href="#">Conditions Générales</a></li>
        <li><a href="#">Cookies</a></li>
      </ul>
    </div>
  </div>
</footer>

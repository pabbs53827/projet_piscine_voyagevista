<?php
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<header>
  <nav class="navbar">
    <!-- Logo -->
    <div class="logo">✈️ VoyageVista</div>
    
    <!-- Mobile Menu Toggle -->
    <div class="menu-toggle">
      <span></span>
      <span></span>
      <span></span>
    </div>
    
    <!-- Navigation Menu -->
    <ul class="nav-menu">
      <li><a href="index.php" class="nav-link <?php echo $current_page === 'index' ? 'active' : ''; ?>">Accueil</a></li>
      <li><a href="#destinations" class="nav-link">Destinations</a></li>
      <li><a href="#experiences" class="nav-link">Expériences</a></li>
      <li><a href="#activities" class="nav-link">Activités</a></li>
    </ul>
    
    <!-- Auth Buttons -->
    <div class="nav-auth">
      <button class="btn btn-outline" onclick="alert('Connexion - Fonctionnalité à développer')">Connexion</button>
      <button class="btn btn-primary" onclick="alert('Inscription - Fonctionnalité à développer')">Inscription</button>
    </div>
  </nav>
</header>

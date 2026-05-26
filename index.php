<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="VoyageVista - Planifiez vos voyages et séjours de rêve. Destinations premium, expériences uniques, service haut de gamme.">
  <meta name="keywords" content="voyage, vacances, destinations, tourisme, planification de voyage">
  <meta name="author" content="VoyageVista">
  <meta name="theme-color" content="#FF9F43">
  
  <title>VoyageVista - Planifiez Vos Voyages de Rêve</title>
  
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='75' font-size='75'>✈️</text></svg>">
  
  <!-- Styles -->
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  
  <!-- navigation -->
  <?php include 'includes/header.php'; ?>

  <main>
    
    <section class="hero">
      <div class="hero-overlay"></div>
      <div class="hero-content">
        <h1>Explorez le Monde avec VoyageVista</h1>
        <p>Découvrez les destinations les plus époustouflantes et créez des souvenirs inoubliables</p>
        
        <!-- Search Bar -->
        <form class="search-bar">
          <input 
            type="text" 
            placeholder="Destination, hôtel ou aéroport" 
            required
          >
          <input 
            type="date" 
            required
          >
          <input 
            type="date" 
            required
          >
          <select required>
            <option value="">Voyageurs</option>
            <option value="1">1 Personne</option>
            <option value="2">2 Personnes</option>
            <option value="3">3-4 Personnes</option>
            <option value="5">5+ Personnes</option>
          </select>
          <button type="submit" class="btn btn-primary">Rechercher</button>
        </form>
        
        <!-- CTA Buttons -->
        <div class="hero-cta">
          <button class="btn btn-primary btn-large" onclick="document.querySelector('#destinations').scrollIntoView({behavior: 'smooth'})">
            Découvrir les Destinations
          </button>
          <button class="btn btn-outline btn-large" onclick="alert('Vidéo de présentation à bientôt!')">
            🎬 Voir la Vidéo
          </button>
        </div>
      </div>
    </section>

    <section class="stats">
      <div class="stats-container">
        <div class="stats-grid">
          <div class="stat-item">
            <div class="stat-number" data-target="50000">50,000</div>
            <div class="stat-label">Voyageurs Heureux</div>
          </div>
          <div class="stat-item">
            <div class="stat-number" data-target="150">150</div>
            <div class="stat-label">Destinations</div>
          </div>
          <div class="stat-item">
            <div class="stat-number" data-target="500">500+</div>
            <div class="stat-label">Hôtels & Hébergements</div>
          </div>
          <div class="stat-item">
            <div class="stat-number" data-target="1000">1,000+</div>
            <div class="stat-label">Expériences Uniques</div>
          </div>
        </div>
      </div>
    </section>

    <section class="destinations" id="destinations">
      <div class="destinations-container">
        <div class="section-header">
          <h2>Destinations Populaires</h2>
          <p>Explorez les plus belles destinations du monde et réservez votre prochain séjour paradisiaque</p>
        </div>

        <div class="destinations-grid">
          <div class="destination-card scroll-reveal">
            <div class="destination-image">🏝️</div>
            <div class="destination-info">
              <div class="destination-header">
                <div class="destination-name">
                  <h3>Maldives</h3>
                  <p>Océan Indien</p>
                </div>
                <div class="destination-price">À partir de 1,299€</div>
              </div>
              <div class="destination-rating">
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span style="color: #999; margin-left: 0.5rem;">(4.8 - 2,145 avis)</span>
              </div>
              <p class="destination-desc">Séjours exotiques avec bungalows sur l'eau, plages de sable blanc et récifs coralliens spectaculaires.</p>
              <div class="destination-features">
                <span class="feature-tag">Plage Privée</span>
                <span class="feature-tag">Snorkeling</span>
                <span class="feature-tag">Spa Luxe</span>
              </div>
              <div class="destination-action">
                <button class="btn-details">Détails</button>
                <button class="btn-book">Réserver</button>
              </div>
            </div>
          </div>

          <div class="destination-card scroll-reveal">
            <div class="destination-image">🏞️</div>
            <div class="destination-info">
              <div class="destination-header">
                <div class="destination-name">
                  <h3>Bali</h3>
                  <p>Indonésie</p>
                </div>
                <div class="destination-price">À partir de 799€</div>
              </div>
              <div class="destination-rating">
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span style="color: #999; margin-left: 0.5rem;">(4.7 - 3,421 avis)</span>
              </div>
              <p class="destination-desc">Découvrez les temples anciens, les rizières verdoyantes et les plages de sable noir de Bali.</p>
              <div class="destination-features">
                <span class="feature-tag">Temples</span>
                <span class="feature-tag">Yoga</span>
                <span class="feature-tag">Nature</span>
              </div>
              <div class="destination-action">
                <button class="btn-details">Détails</button>
                <button class="btn-book">Réserver</button>
              </div>
            </div>
          </div>

          <div class="destination-card scroll-reveal">
            <div class="destination-image">🌅</div>
            <div class="destination-info">
              <div class="destination-header">
                <div class="destination-name">
                  <h3>Santorin</h3>
                  <p>Grèce</p>
                </div>
                <div class="destination-price">À partir de 1,099€</div>
              </div>
              <div class="destination-rating">
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span style="color: #999; margin-left: 0.5rem;">(4.9 - 1,876 avis)</span>
              </div>
              <p class="destination-desc">Couchers de soleil magiques, maisons blanches bleues et vins locaux renommés à Santorin.</p>
              <div class="destination-features">
                <span class="feature-tag">Coucher de Soleil</span>
                <span class="feature-tag">Vin</span>
                <span class="feature-tag">Mer</span>
              </div>
              <div class="destination-action">
                <button class="btn-details">Détails</button>
                <button class="btn-book">Réserver</button>
              </div>
            </div>
          </div>

          <div class="destination-card scroll-reveal">
            <div class="destination-image">🏛️</div>
            <div class="destination-info">
              <div class="destination-header">
                <div class="destination-name">
                  <h3>Thaïlande</h3>
                  <p>Asie du Sud-Est</p>
                </div>
                <div class="destination-price">À partir de 649€</div>
              </div>
              <div class="destination-rating">
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span style="color: #999; margin-left: 0.5rem;">(4.8 - 2,654 avis)</span>
              </div>
              <p class="destination-desc">Explorez Bangkok, les îles tropicales et les plages paradisiaques de la Thaïlande.</p>
              <div class="destination-features">
                <span class="feature-tag">Îles</span>
                <span class="feature-tag">Culture</span>
                <span class="feature-tag">Cuisine</span>
              </div>
              <div class="destination-action">
                <button class="btn-details">Détails</button>
                <button class="btn-book">Réserver</button>
              </div>
            </div>
          </div>
        </div>

        <div class="carousel-nav">
          <button class="carousel-btn">←</button>
          <button class="carousel-btn">→</button>
        </div>
      </div>
    </section>

    <section class="why-us" id="why-us">
      <div class="section-header">
        <h2>Pourquoi Choisir VoyageVista ?</h2>
        <p>Découvrez les avantages qui font de VoyageVista votre meilleur allié pour voyager</p>
      </div>

      <div class="why-us-grid">
        <div class="benefit-card scroll-reveal">
          <div class="benefit-icon">🔐</div>
          <h4>Réservations Sécurisées</h4>
          <p>Paiements cryptés et garantie de remboursement. Vos données sont toujours protégées avec les dernières technologies de sécurité.</p>
        </div>

        <div class="benefit-card scroll-reveal">
          <div class="benefit-icon">💰</div>
          <h4>Meilleurs Prix Garantis</h4>
          <p>Trouvez les tarifs les plus compétitifs du marché. Si vous trouvez moins cher ailleurs, nous vous remboursons la différence.</p>
        </div>

        <div class="benefit-card scroll-reveal">
          <div class="benefit-icon">🌍</div>
          <h4>Plus de 150 Destinations</h4>
          <p>Explorez le monde avec notre catalogue complet de destinations, des plages paradisiaques aux montagnes majestueuses.</p>
        </div>

        <div class="benefit-card scroll-reveal">
          <div class="benefit-icon">🎯</div>
          <h4>Support Client 24/7</h4>
          <p>Besoin d'aide ? Notre équipe professionnelle est disponible 24 heures sur 24 pour répondre à vos questions.</p>
        </div>
      </div>
    </section>

    <section class="experiences" id="experiences">
      <div class="experiences-container">
        <div class="section-header">
          <h2>Expériences Inoubliables</h2>
          <p>Vivre des moments exceptionnels et créer des souvenirs pour toute une vie</p>
        </div>

        <div class="experiences-grid">
          <div class="experience-card scroll-reveal">
            <div class="experience-image">🤿</div>
            <div class="experience-content">
              <h3 class="experience-title">Plongée Sous-Marine</h3>
              <p class="experience-desc">Explorez les récifs coralliens et découvrez la vie marine spectaculaire dans les eaux cristallines des tropiques.</p>
              <a href="#" class="experience-link">Découvrir la plongée →</a>
            </div>
          </div>

          <div class="experience-card scroll-reveal">
            <div class="experience-image">🏄</div>
            <div class="experience-content">
              <h3 class="experience-title">Surfing & Sports d'Eau</h3>
              <p class="experience-desc">Prenez les vagues sur les plus belles plages du monde. Leçons et équipement inclus pour tous les niveaux.</p>
              <a href="#" class="experience-link">Essayer le surf →</a>
            </div>
          </div>

          <div class="experience-card scroll-reveal">
            <div class="experience-image">🏔️</div>
            <div class="experience-content">
              <h3 class="experience-title">Trekking & Randonnée</h3>
              <p class="experience-desc">Gravitissez les plus hauts sommets et explorez les paysages montagneux les plus respirants du globe.</p>
              <a href="#" class="experience-link">Commencer l'aventure →</a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="testimonials" id="testimonials">
      <div class="testimonials-container">
        <div class="section-header">
          <h2>Avis de Nos Voyageurs</h2>
          <p>Lisez les témoignages de voyageurs qui ont vécu des expériences extraordinaires avec VoyageVista</p>
        </div>

        <div class="testimonials-grid">
          <div class="testimonial-card scroll-reveal">
            <div class="testimonial-stars">
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
            </div>
            <p class="testimonial-text">
              "VoyageVista a transformé mon voyage aux Maldives en expérience magique! Le service était impeccable du début à la fin. Je recommande vivement!"
            </p>
            <div class="testimonial-author">
              <div class="author-avatar">A</div>
              <div class="author-info">
                <h4>Amélie Dupont</h4>
                <p>Paris, France</p>
              </div>
            </div>
          </div>

          <div class="testimonial-card scroll-reveal">
            <div class="testimonial-stars">
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
            </div>
            <p class="testimonial-text">
              "Les meilleurs prix trouvés ailleurs! L'application est super facile à utiliser et le support client répond à toutes mes questions immédiatement."
            </p>
            <div class="testimonial-author">
              <div class="author-avatar">M</div>
              <div class="author-info">
                <h4>Marc Leroux</h4>
                <p>Lyon, France</p>
              </div>
            </div>
          </div>

          <div class="testimonial-card scroll-reveal">
            <div class="testimonial-stars">
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
              <span class="star">★</span>
            </div>
            <p class="testimonial-text">
              "Mon dernier voyage en Thaïlande était incroyable. VoyageVista a orchestré chaque détail à la perfection. À bientôt pour le prochain voyage!"
            </p>
            <div class="testimonial-author">
              <div class="author-avatar">S</div>
              <div class="author-info">
                <h4>Sophie Rousseau</h4>
                <p>Marseille, France</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="newsletter" id="newsletter">
      <div class="newsletter-container">
        <h2>Recevez nos Meilleures Offres</h2>
        <p>Inscrivez-vous à notre newsletter pour recevoir les dernières offres de voyage et conseils en exclusivité</p>
        
        <form class="newsletter-form">
          <input 
            type="email" 
            placeholder="Votre adresse email" 
            required
          >
          <button type="submit" class="btn btn-primary">S'inscrire</button>
        </form>
      </div>
    </section>

  </main>

  <!-- footer -->
  <?php include 'includes/footer.php'; ?>

  <!-- Scripts -->
  <script src="js/app.js"></script>
</body>
</html>

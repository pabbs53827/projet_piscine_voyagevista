const header = document.querySelector('header');
const menuToggle = document.querySelector('.menu-toggle');
const navMenu = document.querySelector('.nav-menu');
const navLinks = document.querySelectorAll('.nav-link');

window.addEventListener('scroll', function() {
  if (window.scrollY > 50) {
    header.classList.add('scrolled');
  } else {
    header.classList.remove('scrolled');
  }
});

if (menuToggle) {
  menuToggle.addEventListener('click', function() {
    navMenu.classList.toggle('active');
  });
}

navLinks.forEach(function(lien) {
  lien.addEventListener('click', function() {
    navMenu.classList.remove('active');
  });
});

document.addEventListener('click', function(e) {
  if (!e.target.closest('.navbar')) {
    navMenu.classList.remove('active');
  }
});

document.querySelectorAll('a[href^="#"]').forEach(function(lien) {
  lien.addEventListener('click', function(e) {
    e.preventDefault();
    const cible = document.querySelector(this.getAttribute('href'));
    if (cible) cible.scrollIntoView({ behavior: 'smooth' });
  });
});

const searchForm = document.querySelector('.search-bar');
if (searchForm) {
  searchForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const destination = document.querySelector('input[placeholder*="Destination"]').value;
    const dates = document.querySelectorAll('input[type="date"]');
    const dateDepart = dates[0] ? dates[0].value : '';
    const dateRetour = dates[1] ? dates[1].value : '';

    if (destination && dateDepart && dateRetour) {
      afficherNotification('Recherche lancée pour ' + destination + ' !', 'success');
    } else {
      afficherNotification('Veuillez remplir tous les champs', 'erreur');
    }
  });
}

function afficherNotification(message, type) {
  const notification = document.createElement('div');
  notification.textContent = message;
  notification.style.cssText = `
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    background: ${type === 'success' ? '#FF9F43' : '#F17B5D'};
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 8px;
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    z-index: 9999;
    font-weight: 600;
    font-family: 'Poppins', sans-serif;
    font-size: 0.9rem;
  `;
  document.body.appendChild(notification);
  setTimeout(function() { notification.remove(); }, 3000);
}

document.querySelectorAll('.destination-card').forEach(function(carte) {
  const boutonReserver = carte.querySelector('.btn-book');
  if (boutonReserver) {
    boutonReserver.addEventListener('click', function(e) {
      e.stopPropagation();
      afficherNotification(carte.querySelector('h3').textContent + ' ajoutée au panier !', 'success');
    });
  }

  const boutonDetails = carte.querySelector('.btn-details');
  if (boutonDetails) {
    boutonDetails.addEventListener('click', function(e) {
      e.stopPropagation();
      afficherNotification('Chargement des détails de ' + carte.querySelector('h3').textContent + '...', 'info');
    });
  }
});

function estEmailValide(email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

const newsletterForm = document.querySelector('.newsletter-form');
if (newsletterForm) {
  newsletterForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const champEmail = newsletterForm.querySelector('input[type="email"]');
    if (!estEmailValide(champEmail.value)) {
      afficherNotification('Adresse email invalide', 'erreur');
      return;
    }
    champEmail.value = '';
    afficherNotification('Inscription réussie ! Merci.', 'success');
  });
}

function animerCompteur(element, valeurFinale, dureeMs) {
  let valeurActuelle = 0;
  const increment = valeurFinale / (dureeMs / 16);

  function mettreAJour() {
    valeurActuelle += increment;
    if (valeurActuelle < valeurFinale) {
      element.textContent = Math.floor(valeurActuelle).toLocaleString('fr-FR');
      requestAnimationFrame(mettreAJour);
    } else {
      element.textContent = valeurFinale.toLocaleString('fr-FR');
    }
  }
  mettreAJour();
}

let compteursLances = false;
window.addEventListener('scroll', function() {
  const sectionStats = document.querySelector('.stats');
  if (!sectionStats || compteursLances) return;

  if (sectionStats.getBoundingClientRect().top < window.innerHeight) {
    compteursLances = true;
    document.querySelectorAll('.stat-number').forEach(function(el) {
      const valeur = parseInt(el.getAttribute('data-target'));
      if (valeur > 0) animerCompteur(el, valeur, 2000);
    });
  }
});

let indexCarousel = 0;
const cartesDestination = document.querySelectorAll('.destination-card');
const nbCartes = cartesDestination.length;

function faireDefilerCarousel() {
  const conteneur = document.querySelector('.destinations-grid');
  if (conteneur && cartesDestination[0]) {
    conteneur.scrollLeft = indexCarousel * (cartesDestination[0].offsetWidth + 24);
  }
}

const btnPrecedent = document.querySelector('.carousel-btn:first-child');
const btnSuivant = document.querySelector('.carousel-btn:last-child');

if (btnPrecedent) btnPrecedent.addEventListener('click', function() {
  indexCarousel = (indexCarousel - 1 + nbCartes) % nbCartes;
  faireDefilerCarousel();
});

if (btnSuivant) btnSuivant.addEventListener('click', function() {
  indexCarousel = (indexCarousel + 1) % nbCartes;
  faireDefilerCarousel();
});

document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape' && navMenu) navMenu.classList.remove('active');
});

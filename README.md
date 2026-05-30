# VoyageVista — Instructions d'installation

Projet pédagogique ECE ING2 — application web de réservation de voyages lents. 

## Prérequis

- **MAMP** (macOS) ou **WAMP/XAMPP** (Windows) avec :
  - PHP 8.0+
  - MySQL 8.0+
- Navigateur moderne (Chrome, Firefox, Safari, Edge)
- Connexion Internet (React, Babel et l'API de devises sont chargés via CDN)

---

## Installation en 4 étapes

### 1. Copier les fichiers

Placez le dossier `voyagevista/` dans le répertoire racine du serveur :

| Environnement | Chemin |
|---------------|--------|
| MAMP (macOS)  | `/Applications/MAMP/htdocs/voyagevista/` |
| WAMP (Windows)| `C:\wamp64\www\voyagevista\` |
| XAMPP         | `C:\xampp\htdocs\voyagevista\` |

### 2. Configurer la base de données

Dans MAMP / phpMyAdmin :

1. Créer une base de données nommée `voyagevista` (encodage `utf8mb4_unicode_ci`)
2. Importer **dans cet ordre** :
   - `schema.sql` — crée toutes les tables et contraintes
   - `seed_data.sql` — insère les données de démonstration

### 3. Configurer la connexion PHP

Copier `api/config.example.php` en `api/config.php` et adapter les valeurs :

```php
return [
    'host'     => '127.0.0.1',
    'port'     => 8889,        // MAMP : 8889 | WAMP/XAMPP : 3306
    'dbname'   => 'voyagevista',
    'user'     => 'root',
    'password' => 'root',      // MAMP : 'root' | WAMP : '' | XAMPP : ''
];
```

### 4. Lancer l'application

Démarrer les serveurs Apache et MySQL dans MAMP, puis ouvrir :

```
http://localhost:8888/voyagevista/
```

*(WAMP/XAMPP : `http://localhost/voyagevista/`)*

---

## Comptes de démonstration

Tous les comptes ont le mot de passe : **`password`**

| Email | Rôle | Description |
|-------|------|-------------|
| `admin@voyagevista.fr` | Admin | Accès complet |
| `ahmet@voyagevista.fr` | Hôte | Istanbul, Dubaï, Marrakech, Zanzibar |
| `ana@voyagevista.fr` | Hôte | Amérique latine |
| `kenji@voyagevista.fr` | Hôte | Asie |
| `fatima@voyagevista.fr` | Hôte | Europe |
| `lucas@example.com` | Voyageur | Compte voyageur de test |

Il est également possible de créer un nouveau compte voyageur ou hôte via le formulaire d'inscription.

---

## Structure du projet

```
voyagevista/
├── index.html          # Application SPA (React 18 + Babel, sans build)
├── styles.css          # Feuille de styles
├── logo.png            # Logo
├── schema.sql          # Schéma de la base de données
├── seed_data.sql       # Données de démonstration
└── api/
    ├── config.php          # Configuration BDD (à créer, voir config.example.php)
    ├── config.example.php  # Modèle de configuration
    ├── _db.php             # Connexion PDO
    ├── _helpers.php        # Fonctions utilitaires (auth, JSON, etc.)
    └── *.php               # Endpoints REST
```

## Dépendances

Toutes chargées via CDN — **aucune installation npm requise** :

- React 18 + ReactDOM
- Babel Standalone (transpilation JSX côté client)
- @fawazahmed0/currency-api (conversion de devises)

---

## Fonctionnalités principales

- Catalogue de destinations avec filtres et favoris
- Réservation progressive : transport aller → transport retour → hébergement → activités
- Panier de séjour avec calcul du prix total
- Paiement simulé et confirmation
- Historique des voyages avec génération PDF d'itinéraire
- Annulation de séjour validé
- Conversion de devises en temps réel (€ / $ / £ / ¥)
- Espace hôte : gestion des hébergements, activités et disponibilités
- Espace admin : gestion des utilisateurs, destinations et transports
- Système de notifications et d'évaluations

# ecf-ecoride
connexion:
    utilisateur: admin
    password: admin
    mail: admin@ecoride.fr

# EcoRide - Plateforme de covoiturage

## 🚀 Lancer en local (Docker requis)

```bash
git clone https://github.com/yoannfb/ecf-ecoride.git
cd ecoride
docker-compose up --build

# 🛺 EcoRide — Plateforme de covoiturage éco-responsable

**EcoRide** est une application web dynamique de covoiturage, conçue pour faciliter les trajets partagés entre utilisateurs, tout en assurant une traçabilité des recherches grâce à l'intégration de MongoDB. Le projet utilise une base MySQL pour les données relationnelles et MongoDB pour le stockage des logs.

---

## 📁 Structure du projet

- **PHP 8.1** avec Apache
- **MySQL 8** pour la base de données relationnelle
- **MongoDB 6** pour le suivi des recherches utilisateur
- **PDO** pour les accès SQL
- **MongoDB PHP Driver** pour les accès NoSQL
- **phpMyAdmin** pour la gestion de la base MySQL
- **Docker & docker-compose** pour l’environnement de développement
- **Heroku** pour le déploiement cloud

---

## 🚀 Lancer l'application en local (via Docker)

### Prérequis
- Docker et Docker Compose installés

### Étapes

```bash
git clone https://github.com/yoannfb/ecf-ecoride.git
cd ecf-ecoride
docker-compose up --build
```

L’application sera accessible sur : [http://localhost:8080](http://localhost:8080)  
phpMyAdmin sera accessible sur : [http://localhost:8081](http://localhost:8081)

### Connexion admin (préconfigurée)
- **Email** : `admin@ecoride.fr`
- **Mot de passe** : `admin`

---

## 🌐 Déploiement sur Heroku (avec Docker)

### 1. Prérequis
- Avoir un compte Heroku
- Heroku CLI installé
- Docker installé et connecté à Heroku

### 2. Connexion à Heroku

```bash
heroku login
heroku container:login
```

### 3. Créer l'application Heroku

```bash
heroku create ecoride-app
```

### 4. Déployer le conteneur

```bash
heroku container:push web --app ecoride-app
heroku container:release web --app ecoride-app
```

### 5. Ajouter une base de données (optionnel)

```bash
heroku addons:create heroku-mysql --app ecoride-app
```

*⚠️ Pour MongoDB, utiliser un service comme MongoDB Atlas et configurer l'URI via une variable d’environnement.*

### 6. Accéder à l'application

```bash
heroku open --app ecoride-app
```

---

## 📄 Scripts init

Les fichiers `.sql` à placer dans `docker-entrypoint-initdb.d/` seront exécutés à la première exécution du conteneur MySQL, pour créer les tables, utilisateurs ou données de test.

---

## 🧪 Tests

- Les composants métiers comme `log_mongo.php` peuvent être testés en lançant une recherche depuis l'application.
- Le suivi MongoDB peut être vérifié via un client externe (Compass, Mongo Shell...) connecté à `localhost:27017`.
